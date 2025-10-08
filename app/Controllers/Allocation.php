<?php

namespace App\Controllers;

use App\Models\AllocationModel;
use App\Models\KlasseModel;
use App\Models\SchuelerModel;
use App\Models\ClubModel;
use App\Models\ClubOfferModel;
use App\Models\ChoiceModel;
use App\Services\AllocationService;

class Allocation extends BaseController
{
    protected $allocationModel;
    protected $klasseModel;
    protected $schuelerModel;
    protected $clubModel;
    protected $offerModel;
    protected $choiceModel;
    protected $allocationService;

    public function __construct()
    {
        $this->allocationModel = new AllocationModel();
        $this->klasseModel = new KlasseModel();
        $this->schuelerModel = new SchuelerModel();
        $this->clubModel = new ClubModel();
        $this->offerModel = new ClubOfferModel();
        $this->choiceModel = new ChoiceModel();
        $this->allocationService = new AllocationService();
    }

    /**
     * Allocation Dashboard - Übersicht über das Losverfahren
     */
    public function index()
    {
        $schoolyear = '2024/2025'; // TODO: Aus Config holen
        
        // Statistiken sammeln
        $allOffers = $this->offerModel->getActiveOffers($schoolyear);
        $totalCapacity = 0;
        foreach ($allOffers as $offer) {
            $totalCapacity += $offer['capacity'];
        }
        
        $allKlassen = $this->klasseModel->findAll();
        $klassenComplete = 0;
        foreach ($allKlassen as $klasse) {
            if ($this->klasseModel->isChoicesComplete($klasse['id'])) {
                $klassenComplete++;
            }
        }
        
        $stats = [
            'total_students' => $this->schuelerModel->countAllResults(),
            'students_with_choices' => $this->choiceModel->countAllResults(),
            'total_offers' => count($allOffers),
            'total_capacity' => $totalCapacity,
            'allocations_done' => $this->allocationModel->countAllResults(),
            'klassen_complete' => $klassenComplete,
            'klassen_total' => count($allKlassen),
        ];

        // Letzte Durchläufe anzeigen (TODO: Methode implementieren)
        $recentRuns = [];

        // Klassen-Status
        $klassenStatus = [];
        foreach ($allKlassen as $klasse) {
            $schueler = $this->klasseModel->getSchueler($klasse['id']);
            $completedStudents = 0;
            foreach ($schueler as $student) {
                $choices = $this->choiceModel->where('student_id', $student['id'])->countAllResults();
                if ($choices > 0) {
                    $completedStudents++;
                }
            }
            $klassenStatus[] = [
                'id' => $klasse['id'],
                'name' => $klasse['name'],
                'total_students' => count($schueler),
                'completed_students' => $completedStudents,
                'is_complete' => $this->klasseModel->isChoicesComplete($klasse['id']),
            ];
        }

        $data = [
            'title' => 'AG-Zuteilung',
            'schoolyear' => $schoolyear,
            'stats' => $stats,
            'recentRuns' => $recentRuns,
            'klassenStatus' => $klassenStatus,
        ];

        return view('allocation/index', $data);
    }

    /**
     * Losverfahren ausführen
     */
    public function run()
    {
        $schoolyear = '2024/2025';
        
        try {
            // Prüfe ob alle Wahlen vollständig sind
            $incompleteClasses = $this->klasseModel->getClassesWithIncompleteChoices();
            if (!empty($incompleteClasses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nicht alle Klassen haben ihre Wahlen abgegeben',
                    'incomplete_classes' => $incompleteClasses
                ]);
            }

            // Prüfe Kapazität
            $capacityCheck = $this->allocationService->checkCapacity($schoolyear);
            if (!$capacityCheck['sufficient']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nicht genügend AG-Plätze verfügbar',
                    'details' => $capacityCheck
                ]);
            }

            // Losverfahren ausführen
            $result = $this->allocationService->runLottery($schoolyear);
            
            if ($result['success']) {
                // Statistiken nach dem Lauf
                $stats = [
                    'allocated_students' => $result['allocated_count'],
                    'total_students' => $result['total_students'],
                    'run_id' => $result['run_id'],
                    'timestamp' => date('d.m.Y H:i'),
                ];

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Losverfahren erfolgreich abgeschlossen!',
                    'stats' => $stats
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Fehler beim Losverfahren: ' . $result['error']
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Allocation error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unerwarteter Fehler: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ergebnisse anzeigen
     */
    public function results()
    {
        $schoolyear = '2024/2025';
        $runId = $this->request->getGet('run_id');
        
        // Wenn kein Run-ID angegeben, neueste Ergebnisse laden
        if (!$runId) {
            $latestRun = $this->allocationModel->getLatestRun();
            if ($latestRun) {
                return redirect()->to(base_url('allocation/results?run_id=' . $latestRun['id']));
            }
        }

        $run = $runId ? $this->allocationModel->getRunWithResults($runId) : null;
        $allRuns = $this->allocationModel->getRecentRuns(10);

        if (!$run) {
            return redirect()->to('/allocation')->with('error', 'Keine Ergebnisse gefunden');
        }

        // Ergebnisse nach Klassen gruppieren
        $resultsByKlasse = [];
        foreach ($run['results'] as $allocation) {
            $klasseId = $allocation['student']['klasse_id'];
            if (!isset($resultsByKlasse[$klasseId])) {
                $resultsByKlasse[$klasseId] = [
                    'klasse' => $allocation['student']['klasse'],
                    'allocations' => []
                ];
            }
            $resultsByKlasse[$klasseId]['allocations'][] = $allocation;
        }

        $data = [
            'title' => 'AG-Zuteilung Ergebnisse',
            'run' => $run,
            'allRuns' => $allRuns,
            'resultsByKlasse' => $resultsByKlasse,
            'schoolyear' => $schoolyear,
        ];

        return view('allocation/results', $data);
    }

    /**
     * Manuelle Tausche verwalten
     */
    public function swaps()
    {
        $schoolyear = '2024/2025';
        
        // Alle aktuellen Zuteilungen laden
        $allocations = $this->allocationModel->getCurrentAllocations($schoolyear);
        
        // Nach Klassen gruppieren
        $allocationsByKlasse = [];
        foreach ($allocations as $allocation) {
            $klasseId = $allocation['student']['klasse_id'];
            if (!isset($allocationsByKlasse[$klasseId])) {
                $allocationsByKlasse[$klasseId] = [
                    'klasse' => $allocation['student']['klasse'],
                    'allocations' => []
                ];
            }
            $allocationsByKlasse[$klasseId]['allocations'][] = $allocation;
        }

        $data = [
            'title' => 'Manuelle Tausche',
            'allocationsByKlasse' => $allocationsByKlasse,
            'schoolyear' => $schoolyear,
        ];

        return view('allocation/swaps', $data);
    }

    /**
     * Tausch zwischen zwei Schülern durchführen (HTMX)
     */
    public function performSwap()
    {
        $student1Id = $this->request->getPost('student1_id');
        $student2Id = $this->request->getPost('student2_id');

        if (!$student1Id || !$student2Id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Beide Schüler müssen ausgewählt werden'
            ]);
        }

        try {
            $result = $this->allocationService->performSwap($student1Id, $student2Id);
            
            if ($result['success']) {
                // Aktualisierte Zuteilungen zurückgeben
                $updatedAllocations = $this->allocationModel->getAllocationsForStudents([$student1Id, $student2Id]);
                
                return view('allocation/partials/swap_result', [
                    'success' => true,
                    'message' => 'Tausch erfolgreich durchgeführt!',
                    'allocations' => $updatedAllocations
                ]);
            } else {
                return view('allocation/partials/swap_result', [
                    'success' => false,
                    'message' => $result['error']
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Swap error: ' . $e->getMessage());
            return view('allocation/partials/swap_result', [
                'success' => false,
                'message' => 'Unerwarteter Fehler beim Tausch'
            ]);
        }
    }

    /**
     * Export-Funktionen
     */
    public function export($format = 'pdf')
    {
        $schoolyear = '2024/2025';
        $runId = $this->request->getGet('run_id');
        
        if (!$runId) {
            $latestRun = $this->allocationModel->getLatestRun();
            $runId = $latestRun ? $latestRun['id'] : null;
        }

        if (!$runId) {
            return redirect()->to('/allocation')->with('error', 'Keine Ergebnisse zum Exportieren gefunden');
        }

        $run = $this->allocationModel->getRunWithResults($runId);
        
        if ($format === 'pdf') {
            return $this->exportPDF($run);
        } elseif ($format === 'excel') {
            return $this->exportExcel($run);
        }

        return redirect()->to('/allocation')->with('error', 'Ungültiges Export-Format');
    }

    /**
     * PDF Export
     */
    private function exportPDF($run)
    {
        // TODO: PDF Export implementieren
        return $this->response->setJSON([
            'success' => false,
            'message' => 'PDF Export noch nicht implementiert'
        ]);
    }

    /**
     * Excel Export
     */
    private function exportExcel($run)
    {
        // TODO: Excel Export implementieren
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Excel Export noch nicht implementiert'
        ]);
    }

    /**
     * Statistiken anzeigen
     */
    public function statistics()
    {
        $schoolyear = '2024/2025';
        $stats = $this->allocationService->getStatistics($schoolyear);

        $data = [
            'title' => 'AG-Statistiken',
            'stats' => $stats,
            'schoolyear' => $schoolyear,
        ];

        return view('allocation/statistics', $data);
    }
}
