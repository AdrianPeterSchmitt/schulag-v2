<?php

namespace App\Models;

use CodeIgniter\Model;

class ManualSwapModel extends Model
{
    protected $table = 'manual_swaps';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_a_id',
        'student_b_id',
        'offer_a_id',
        'offer_b_id',
        'created_by',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'student_a_id' => 'required|integer',
        'student_b_id' => 'required|integer',
        'offer_a_id' => 'required|integer',
        'offer_b_id' => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Erstelle einen Swap und führe ihn durch
     */
    public function performSwap($studentAId, $studentBId, $offerAId, $offerBId, $createdBy = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // 1. Speichere den Swap in der Historie
            $swapId = $this->insert([
                'student_a_id' => $studentAId,
                'student_b_id' => $studentBId,
                'offer_a_id' => $offerAId,
                'offer_b_id' => $offerBId,
                'created_by' => $createdBy,
            ]);
            
            // 2. Update Allocations
            $allocationModel = new AllocationModel();
            
            // Student A: offer_a_id → offer_b_id
            $allocationModel->where('student_id', $studentAId)
                           ->where('offer_id', $offerAId)
                           ->set(['offer_id' => $offerBId, 'status' => 'MANUAL'])
                           ->update();
            
            // Student B: offer_b_id → offer_a_id
            $allocationModel->where('student_id', $studentBId)
                           ->where('offer_id', $offerBId)
                           ->set(['offer_id' => $offerAId, 'status' => 'MANUAL'])
                           ->update();
            
            $db->transComplete();
            
            return $db->transStatus() ? $swapId : false;
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Manual Swap Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get alle Swaps für ein Schuljahr
     */
    public function getForSchoolyear($schoolyear)
    {
        $offerModel = new ClubOfferModel();
        $offers = $offerModel->where('schoolyear', $schoolyear)->findAll();
        $offerIds = array_column($offers, 'id');
        
        if (empty($offerIds)) {
            return [];
        }
        
        return $this->whereIn('offer_a_id', $offerIds)
                    ->orWhereIn('offer_b_id', $offerIds)
                    ->findAll();
    }
}
