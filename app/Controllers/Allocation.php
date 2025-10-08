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
    protected AllocationModel $allocationModel;
    protected KlasseModel $klasseModel;
    protected SchuelerModel $schuelerModel;
    protected ClubModel $clubModel;
    protected ClubOfferModel $offerModel;
    protected ChoiceModel $choiceModel;
    protected AllocationService $allocationService;

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
     * 
     * @return string
     */
    public function index(): string
    {
        $schoolyear = getCurrentSchoolyear();
        
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

        // Letzte Durchläufe anzeigen
        $recentRuns = $this->allocationModel->getRecentRuns(5);

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
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
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
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
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
     * 
     * @return string
     */
    public function swaps(): string
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
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function performSwap(): \CodeIgniter\HTTP\ResponseInterface
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
            // Hole die aktuellen Allocations für beide Schüler
            $allocation1 = $this->allocationModel->where('student_id', $student1Id)
                ->where('status', 'ASSIGNED')
                ->first();
            $allocation2 = $this->allocationModel->where('student_id', $student2Id)
                ->where('status', 'ASSIGNED')
                ->first();
                
            if (!$allocation1 || !$allocation2) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Einer oder beide Schüler haben keine Zuteilung'
                ]);
            }
            
            $result = $this->allocationService->performSwap(
                $student1Id, 
                $student2Id, 
                $allocation1['offer_id'], 
                $allocation2['offer_id'],
                session()->get('user')['id'] ?? null
            );
            
            if (isset($result['success']) && $result['success']) {
                // Aktualisierte Zuteilungen zurückgeben
                $updatedAllocations = $this->allocationModel->getAllocationsForStudents([$student1Id, $student2Id]);
                
                $html = view('allocation/partials/swap_result', [
                    'success' => true,
                    'message' => 'Tausch erfolgreich durchgeführt!',
                    'allocations' => $updatedAllocations
                ]);
                
                return $this->response->setBody($html);
            } else {
                $errorMessage = isset($result['error']) ? $result['error'] : 'Unbekannter Fehler';
                $html = view('allocation/partials/swap_result', [
                    'success' => false,
                    'message' => $errorMessage
                ]);
                
                return $this->response->setBody($html);
            }

        } catch (\Exception $e) {
            log_message('error', 'Swap error: ' . $e->getMessage());
            $html = view('allocation/partials/swap_result', [
                'success' => false,
                'message' => 'Unerwarteter Fehler beim Tausch'
            ]);
            
            return $this->response->setBody($html);
        }
    }

    /**
     * Export-Funktionen
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
     */
    public function export(string $format = 'pdf')
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
     * 
     * @param array<string, mixed> $run
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    private function exportPDF(array $run): \CodeIgniter\HTTP\ResponseInterface
    {
        $schoolyear = getCurrentSchoolyear();
        
        // Lade alle Daten für den Export
        $allocations = $this->allocationModel->getAssigned($schoolyear);
        $offers = $this->offerModel->getActiveOffers($schoolyear);
        
        // Gruppiere Allocations nach AG
        $allocationsByOffer = [];
        foreach ($allocations as $allocation) {
            $offerId = $allocation['offer_id'];
            if (!isset($allocationsByOffer[$offerId])) {
                $allocationsByOffer[$offerId] = [];
            }
            $allocationsByOffer[$offerId][] = $allocation;
        }
        
        // HTML für PDF generieren
        $html = view('allocation/pdf/export', [
            'run' => $run,
            'schoolyear' => $schoolyear,
            'offers' => $offers,
            'allocationsByOffer' => $allocationsByOffer,
            'generatedAt' => date('d.m.Y H:i'),
        ]);
        
        // PDF mit Dompdf erstellen
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // PDF ausgeben
        $filename = 'AG-Zuteilung-' . str_replace('/', '-', $schoolyear) . '.pdf';
        
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Excel Export
     * 
     * @param array<string, mixed> $run
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    private function exportExcel(array $run): \CodeIgniter\HTTP\ResponseInterface
    {
        $schoolyear = getCurrentSchoolyear();
        
        // Lade alle Daten
        $allocations = $this->allocationModel->getAssigned($schoolyear);
        $offers = $this->offerModel->getActiveOffers($schoolyear);
        
        // Erstelle Excel-Datei
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Titel
        $sheet->setTitle('AG-Zuteilungen');
        $sheet->setCellValue('A1', 'AG-Zuteilungen ' . $schoolyear);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A1:F1');
        
        // Header
        $row = 3;
        $headers = ['AG-Name', 'Schüler-ID', 'Status', 'Zugewiesen am', 'Kapazität', 'Auslastung'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4A5568');
            $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }
        
        // Daten
        $row = 4;
        foreach ($offers as $offer) {
            $agAllocations = array_filter($allocations, function($a) use ($offer) {
                return $a['offer_id'] == $offer['id'];
            });
            
            $agName = $offer['club']['titel'] ?? 'Unbekannt';
            $capacity = $offer['capacity'];
            $count = count($agAllocations);
            $auslastung = $capacity > 0 ? round(($count / $capacity) * 100, 1) . '%' : '0%';
            
            if (empty($agAllocations)) {
                // Zeige AG auch wenn keine Zuteilungen
                $sheet->setCellValue('A' . $row, $agName);
                $sheet->setCellValue('B' . $row, '-');
                $sheet->setCellValue('C' . $row, '-');
                $sheet->setCellValue('D' . $row, '-');
                $sheet->setCellValue('E' . $row, $capacity);
                $sheet->setCellValue('F' . $row, $auslastung);
                $row++;
            } else {
                foreach ($agAllocations as $allocation) {
                    $sheet->setCellValue('A' . $row, $agName);
                    $sheet->setCellValue('B' . $row, $allocation['student_id']);
                    $sheet->setCellValue('C' . $row, $allocation['status']);
                    $sheet->setCellValue('D' . $row, date('d.m.Y', strtotime($allocation['created_at'])));
                    $sheet->setCellValue('E' . $row, $capacity);
                    $sheet->setCellValue('F' . $row, $auslastung);
                    $row++;
                }
            }
        }
        
        // Auto-Size Spalten
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Speichern und ausgeben
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'AG-Zuteilung-' . str_replace('/', '-', $schoolyear) . '.xlsx';
        
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody($content);
    }

    /**
     * Statistiken anzeigen
     * 
     * @return string
     */
    public function statistics(): string
    {
        $schoolyear = getCurrentSchoolyear();
        $stats = $this->allocationService->getStatistics($schoolyear);

        $data = [
            'title' => 'AG-Statistiken',
            'stats' => $stats,
            'schoolyear' => $schoolyear,
        ];

        return view('allocation/statistics', $data);
    }
}
