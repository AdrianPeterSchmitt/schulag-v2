<?php

namespace App\Services;

use App\Models\AllocationModel;
use App\Models\ChoiceModel;
use App\Models\ClubOfferModel;
use App\Models\SchuelerModel;

class AllocationService
{
    protected $allocationModel;
    protected $choiceModel;
    protected $offerModel;
    protected $schuelerModel;

    public function __construct()
    {
        $this->allocationModel = new AllocationModel();
        $this->choiceModel = new ChoiceModel();
        $this->offerModel = new ClubOfferModel();
        $this->schuelerModel = new SchuelerModel();
    }

    /**
     * Prüft ob genug AG-Plätze für alle teilnehmenden Schüler vorhanden sind
     */
    public function checkCapacity(string $schoolyear): array
    {
        // Zähle alle Schüler die teilnehmen wollen (ohne "Nimmt nicht teil")
        $db = \Config\Database::connect();
        
        // Subquery: Schüler mit "no_participation"
        $subquery = $db->table('choices')
                      ->select('student_id')
                      ->where('priority', 'no_participation')
                      ->getCompiledSelect();
        
        $totalStudents = $db->table('schueler')
                           ->where("id NOT IN ($subquery)", null, false)
                           ->countAllResults();

        // Zähle alle verfügbaren AG-Plätze
        $totalCapacity = $this->offerModel
            ->selectSum('capacity')
            ->where('schoolyear', $schoolyear)
            ->where('active', 1)
            ->get()
            ->getRow()
            ->capacity ?? 0;

        $shortage = max(0, $totalStudents - $totalCapacity);

        return [
            'total_students' => $totalStudents,
            'total_capacity' => $totalCapacity,
            'has_enough_capacity' => $totalStudents <= $totalCapacity,
            'shortage' => $shortage
        ];
    }

    /**
     * Führt das Losverfahren für ein Schuljahr durch
     */
    public function runLottery(string $schoolyear): array
    {
        // Prüfe Kapazität vor dem Losverfahren
        $capacityCheck = $this->checkCapacity($schoolyear);
        
        if (!$capacityCheck['has_enough_capacity']) {
            throw new \RuntimeException(
                "Nicht genug AG-Plätze verfügbar! " .
                "Schüler: {$capacityCheck['total_students']}, " .
                "Plätze: {$capacityCheck['total_capacity']}, " .
                "Fehlend: {$capacityCheck['shortage']}"
            );
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Lösche alte Zuteilungen für dieses Schuljahr
            $offers = $this->offerModel
                ->where('schoolyear', $schoolyear)
                ->where('active', 1)
                ->findAll();
            
            $offerIds = array_column($offers, 'id');

            if (!empty($offerIds)) {
                $this->allocationModel->whereIn('offer_id', $offerIds)->delete();
            }

            // Sammle alle Wahlen nach Priorität (ohne "no_participation")
            $choices = $this->choiceModel
                ->whereIn('offer_id', $offerIds)
                ->whereNotNull('offer_id')
                ->findAll();

            // Gruppiere nach Priorität
            $choicesByPriority = [
                '1' => [],
                '2' => [],
                '3' => []
            ];

            foreach ($choices as $choice) {
                if (in_array($choice['priority'], ['1', '2', '3'])) {
                    $choicesByPriority[$choice['priority']][] = $choice;
                }
            }

            $assignedStudentIds = [];

            // Deterministischer Random-Seed basierend auf Schuljahr
            mt_srand(crc32($schoolyear));

            // Verarbeite Priorität 1, 2, 3
            foreach (['1', '2', '3'] as $priority) {
                if (empty($choicesByPriority[$priority])) {
                    continue;
                }

                // Shuffle für Fairness
                $priorityChoices = $choicesByPriority[$priority];
                shuffle($priorityChoices);

                foreach ($priorityChoices as $choice) {
                    // Schüler bereits zugeteilt?
                    if (in_array($choice['student_id'], $assignedStudentIds)) {
                        continue;
                    }

                    // Prüfe Kapazität
                    if ($this->offerModel->hasSpace($choice['offer_id'])) {
                        // Zuweisen
                        $this->allocationModel->insert([
                            'student_id' => $choice['student_id'],
                            'offer_id' => $choice['offer_id'],
                            'status' => 'ASSIGNED',
                        ]);

                        $assignedStudentIds[] = $choice['student_id'];
                    }
                    // Wenn AG voll ist, wird Schüler NICHT zugeteilt
                }
            }

            // Rest-Warteliste: Alle Schüler die noch nicht zugeteilt wurden
            // Schüler mit "no_participation"
            $subquery = $db->table('choices')
                          ->select('student_id')
                          ->where('priority', 'no_participation')
                          ->getCompiledSelect();
            
            $allParticipatingStudents = $db->table('schueler')
                                          ->select('id')
                                          ->where("id NOT IN ($subquery)", null, false)
                                          ->get()
                                          ->getResultArray();
            
            $allParticipatingStudentIds = array_column($allParticipatingStudents, 'id');
            
            $unassignedStudentIds = array_diff($allParticipatingStudentIds, $assignedStudentIds);

            // Erstelle Rest-Warteliste Einträge
            foreach ($unassignedStudentIds as $studentId) {
                $this->allocationModel->insert([
                    'student_id' => $studentId,
                    'offer_id' => null,
                    'status' => 'REST_WAITLIST',
                ]);
            }

            // Resette Random-Seed
            mt_srand();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaktion fehlgeschlagen');
            }

            log_message('info', 'Lottery successful for schoolyear: ' . $schoolyear);

            return [
                'success' => true,
                'assigned_count' => count($assignedStudentIds),
                'rest_waitlist_count' => count($unassignedStudentIds),
            ];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Lottery error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Führe einen manuellen Tausch durch
     */
    public function performSwap($studentAId, $studentBId, $offerAId, $offerBId, $createdBy = null): bool
    {
        $manualSwapModel = new \App\Models\ManualSwapModel();
        return (bool)$manualSwapModel->performSwap($studentAId, $studentBId, $offerAId, $offerBId, $createdBy);
    }

    /**
     * Get Statistiken für ein Schuljahr
     */
    public function getStatistics($schoolyear): array
    {
        $offers = $this->offerModel->getActiveOffers($schoolyear);
        
        $stats = [
            'total_clubs' => count($offers),
            'total_capacity' => 0,
            'total_assigned' => 0,
            'total_rest_waitlist' => 0,
            'clubs' => []
        ];

        foreach ($offers as $offer) {
            $assignedCount = $this->offerModel->assignedCount($offer['id']);
            $stats['total_capacity'] += $offer['capacity'];
            $stats['total_assigned'] += $assignedCount;
            
            $stats['clubs'][] = [
                'offer_id' => $offer['id'],
                'capacity' => $offer['capacity'],
                'assigned' => $assignedCount,
                'free' => $offer['capacity'] - $assignedCount,
            ];
        }

        // Rest-Warteliste
        $stats['total_rest_waitlist'] = $this->allocationModel
            ->where('status', 'REST_WAITLIST')
            ->countAllResults();

        return $stats;
    }
}
