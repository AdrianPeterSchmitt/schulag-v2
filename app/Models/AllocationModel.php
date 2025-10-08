<?php

namespace App\Models;

use CodeIgniter\Model;

class AllocationModel extends Model
{
    protected $table = 'allocations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_id',
        'offer_id',
        'status',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'student_id' => 'required|integer',
        'status' => 'required|in_list[ASSIGNED,WAITLIST,REST_WAITLIST,MANUAL]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get Student für eine Allocation
     * 
     * @return array<string, mixed>|null
     */
    public function getStudent(int $allocationId): ?array
    {
        $allocation = $this->find($allocationId);
        if ($allocation) {
            $schuelerModel = new SchuelerModel();
            return $schuelerModel->find($allocation['student_id']);
        }
        return null;
    }

    /**
     * Get Offer für eine Allocation
     * 
     * @return array<string, mixed>|null
     */
    public function getOffer(int $allocationId): ?array
    {
        $allocation = $this->find($allocationId);
        if ($allocation && $allocation['offer_id']) {
            $offerModel = new ClubOfferModel();
            return $offerModel->find($allocation['offer_id']);
        }
        return null;
    }

    /**
     * Get alle Allocations für ein Schuljahr
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getForSchoolyear(string $schoolyear): array
    {
        $offerModel = new ClubOfferModel();
        $offers = $offerModel->where('schoolyear', $schoolyear)->findAll();
        $offerIds = array_column($offers, 'id');
        
        if (empty($offerIds)) {
            return $this->where('offer_id IS NULL')->findAll();
        }
        
        return $this->whereIn('offer_id', $offerIds)
                    ->orWhere('offer_id IS NULL')
                    ->findAll();
    }

    /**
     * Get alle zugewiesenen Allocations (ASSIGNED status)
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getAssigned(?string $schoolyear = null): array
    {
        $query = $this->where('status', 'ASSIGNED');
        
        if ($schoolyear) {
            $offerModel = new ClubOfferModel();
            $offers = $offerModel->where('schoolyear', $schoolyear)->findAll();
            $offerIds = array_column($offers, 'id');
            
            if (!empty($offerIds)) {
                $query->whereIn('offer_id', $offerIds);
            }
        }
        
        return $query->findAll();
    }

    /**
     * Get REST_WAITLIST Allocations
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getRestWaitlist(?string $schoolyear = null): array
    {
        $query = $this->where('status', 'REST_WAITLIST');
        
        if ($schoolyear) {
            // REST_WAITLIST hat offer_id = NULL, also keine weitere Filterung nötig
        }
        
        return $query->findAll();
    }

    /**
     * Lösche alle Allocations für ein Schuljahr
     */
    public function deleteForSchoolyear(string $schoolyear): bool
    {
        $offerModel = new ClubOfferModel();
        $offers = $offerModel->where('schoolyear', $schoolyear)->findAll();
        $offerIds = array_column($offers, 'id');
        
        if (!empty($offerIds)) {
            $this->whereIn('offer_id', $offerIds)->delete();
        }
        
        // Lösche auch REST_WAITLIST Einträge (haben offer_id = NULL)
        // Hier müssten wir anders filtern, z.B. über created_at oder separate Lösung
        return true;
    }
    
    /**
     * Get den neuesten Losverfahren-Run
     * 
     * @return array<string, mixed>|null
     */
    public function getLatestRun(): ?array
    {
        $runModel = new AllocationRunModel();
        return $runModel->getLatest();
    }
    
    /**
     * Get Run mit allen Ergebnissen
     * 
     * @return array<string, mixed>|null
     */
    public function getRunWithResults(int $runId): ?array
    {
        $runModel = new AllocationRunModel();
        $run = $runModel->getWithMetadata($runId);
        
        if ($run) {
            // Lade die Allocations die zu diesem Run gehören
            // (basierend auf Datum-Range oder spezieller Verknüpfung)
            $run['allocations'] = $this->where('created_at >=', $run['run_date'])
                                       ->where('created_at <', date('Y-m-d H:i:s', strtotime($run['run_date'] . ' +5 minutes')))
                                       ->findAll();
        }
        
        return $run;
    }
    
    /**
     * Get die letzten N Runs
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getRecentRuns(int $limit = 10): array
    {
        $runModel = new AllocationRunModel();
        return $runModel->getRecent($limit);
    }
    
    /**
     * Get aktuelle Allocations für ein Schuljahr
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getCurrentAllocations(string $schoolyear): array
    {
        return $this->getAssigned($schoolyear);
    }
    
    /**
     * Get Allocations für mehrere Schüler
     * 
     * @param array<int> $studentIds
     * @return array<int, array<string, mixed>>
     */
    public function getAllocationsForStudents(array $studentIds): array
    {
        if (empty($studentIds)) {
            return [];
        }
        
        return $this->whereIn('student_id', $studentIds)->findAll();
    }
}
