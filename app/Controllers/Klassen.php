<?php

namespace App\Controllers;

use App\Models\KlasseModel;
use App\Models\SchuelerModel;
use App\Models\ClubModel;
use App\Models\ClubOfferModel;
use App\Models\ChoiceModel;

class Klassen extends BaseController
{
    protected $klasseModel;
    protected $schuelerModel;
    protected $clubModel;
    protected $offerModel;
    protected $choiceModel;

    public function __construct()
    {
        $this->klasseModel = new KlasseModel();
        $this->schuelerModel = new SchuelerModel();
        $this->clubModel = new ClubModel();
        $this->offerModel = new ClubOfferModel();
        $this->choiceModel = new ChoiceModel();
    }

    /**
     * Klassenauswahl für Lehrer
     */
    public function select()
    {
        $klassen = $this->klasseModel->orderBy('jahrgang', 'ASC')->orderBy('name', 'ASC')->findAll();
        
        // Für jede Klasse die Schüler-Anzahl und Completion-Status laden
        foreach ($klassen as &$klasse) {
            $klasse['schueler_count'] = count($this->klasseModel->getSchueler($klasse['id']));
            $klasse['is_complete'] = $this->klasseModel->isChoicesComplete($klasse['id']);
        }
        
        $data = [
            'title' => 'Klasse auswählen',
            'klassen' => $klassen,
        ];

        return view('klassen/select', $data);
    }

    /**
     * Klasse mit Schülern und AG-Wahlen anzeigen
     */
    public function show($klasseId)
    {
        $klasse = $this->klasseModel->getWithSchueler($klasseId);
        
        if (!$klasse) {
            return redirect()->to('/klassen')->with('error', 'Klasse nicht gefunden');
        }

        // Get alle aktiven AG-Angebote für das aktuelle Schuljahr
        $schoolyear = '2024/2025'; // TODO: Aus Config holen
        $offers = $this->offerModel->getActiveOffers($schoolyear);
        
        // Completion Status berechnen
        $isComplete = $this->klasseModel->isChoicesComplete($klasseId);
        $completedCount = 0;
        $totalCount = count($klasse['schueler']);
        
        // Für jeden Schüler die aktuellen Wahlen laden
        foreach ($klasse['schueler'] as &$schueler) {
            $schueler['choices'] = $this->choiceModel->where('student_id', $schueler['id'])->findAll();
            
            // Zähle ob Schüler fertig ist
            $hasNoParticipation = false;
            $normalChoicesCount = 0;
            foreach ($schueler['choices'] as $choice) {
                if ($choice['priority'] === 'no_participation') {
                    $hasNoParticipation = true;
                } elseif (in_array($choice['priority'], ['1', '2', '3']) && $choice['offer_id'] !== null) {
                    $normalChoicesCount++;
                }
            }
            if ($hasNoParticipation || $normalChoicesCount === 3) {
                $completedCount++;
            }
            
            // Verfügbare AGs für diesen Schüler filtern
            $schueler['available_offers'] = $offers; // Temporär: Alle AGs verfügbar
            // foreach ($offers as $offer) {
            //     if ($this->clubModel->isAllowedForStudent($offer['club_id'], $schueler['id'])) {
            //         $schueler['available_offers'][] = $offer;
            //     }
            // }
        }

        $data = [
            'title' => 'Klasse: ' . $klasse['name'],
            'klasse' => $klasse,
            'schoolyear' => $schoolyear,
            'all_offers' => $offers,
            'isComplete' => $isComplete,
            'completedCount' => $completedCount,
            'totalCount' => $totalCount,
        ];

        return view('klassen/show', $data);
    }

    /**
     * AG-Wahlen für einen Schüler speichern (HTMX)
     */
    public function saveChoices($klasseId)
    {
        $studentId = $this->request->getPost('student_id');
        $choices = [
            '1' => $this->request->getPost('choice_1'),
            '2' => $this->request->getPost('choice_2'),
            '3' => $this->request->getPost('choice_3'),
        ];

        // Prüfe ob "Nimmt nicht teil" gewählt wurde
        $noParticipation = $this->request->getPost('no_participation') === '1';
        
        if ($noParticipation) {
            $choices = ['no_participation' => 'no_participation'];
        }

        // Validierung
        if (!$noParticipation) {
            $rules = [
                'choice_1' => 'required|integer',
                'choice_2' => 'required|integer',
                'choice_3' => 'required|integer',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }

            // Prüfe ob alle Wahlen unterschiedlich sind
            if ($choices['1'] === $choices['2'] || $choices['1'] === $choices['3'] || $choices['2'] === $choices['3']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Alle drei Wahlen müssen unterschiedlich sein'
                ]);
            }
        }

        // Speichere Wahlen
        try {
            $this->choiceModel->saveChoicesForStudent($studentId, $choices);
            
            // Lade aktualisierte Student-Daten
            $student = $this->schuelerModel->find($studentId);
            $student['choices'] = $this->choiceModel->where('student_id', $studentId)->findAll();
            
            // Lade verfügbare AGs
            $schoolyear = '2024/2025';
            $offers = $this->offerModel->getActiveOffers($schoolyear);
            $student['available_offers'] = $offers; // Temporär: Alle AGs
            
            // Gebe aktualisierte Student-Card zurück
            return view('klassen/partials/student_card', ['student' => $student]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody('Fehler beim Speichern: ' . $e->getMessage());
        }
    }

    /**
     * Prüfe ob alle Schüler der Klasse ihre Wahlen abgegeben haben
     */
    public function checkCompletion($klasseId)
    {
        $isComplete = $this->klasseModel->isChoicesComplete($klasseId);
        
        return $this->response->setJSON([
            'complete' => $isComplete,
            'message' => $isComplete ? 'Alle Wahlen sind vollständig!' : 'Noch nicht alle Wahlen abgegeben'
        ]);
    }
}
