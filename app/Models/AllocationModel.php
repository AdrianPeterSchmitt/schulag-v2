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
     */
    public function getStudent($allocationId)
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
     */
    public function getOffer($allocationId)
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
     */
    public function getForSchoolyear($schoolyear)
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
     */
    public function getAssigned($schoolyear = null)
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
     */
    public function getRestWaitlist($schoolyear = null)
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
    public function deleteForSchoolyear($schoolyear)
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
}
