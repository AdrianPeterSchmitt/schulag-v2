<?php

namespace App\Models;

use CodeIgniter\Model;

class ChoiceModel extends Model
{
    protected $table = 'choices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_id',
        'offer_id',
        'priority',
        'created_by',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'student_id' => 'required|integer',
        'priority' => 'required|in_list[1,2,3,no_participation]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get Student für eine Choice
     * 
     * @return array<string, mixed>|null
     */
    public function getStudent(int $choiceId): ?array
    {
        $choice = $this->find($choiceId);
        if ($choice) {
            $schuelerModel = new SchuelerModel();
            return $schuelerModel->find($choice['student_id']);
        }
        return null;
    }

    /**
     * Get Offer für eine Choice
     * 
     * @return array<string, mixed>|null
     */
    public function getOffer(int $choiceId): ?array
    {
        $choice = $this->find($choiceId);
        if ($choice && $choice['offer_id']) {
            $offerModel = new ClubOfferModel();
            return $offerModel->find($choice['offer_id']);
        }
        return null;
    }

    /**
     * Get alle Choices für ein Schuljahr
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getForSchoolyear(string $schoolyear): array
    {
        $offerModel = new ClubOfferModel();
        $offers = $offerModel->where('schoolyear', $schoolyear)->findAll();
        $offerIds = array_column($offers, 'id');
        
        if (empty($offerIds)) {
            return [];
        }
        
        return $this->whereIn('offer_id', $offerIds)->findAll();
    }

    /**
     * Speichere Choices für einen Schüler
     * Löscht vorhandene Wahlen und speichert neue
     * 
     * @param array<string, mixed> $choices
     */
    public function saveChoicesForStudent(int $studentId, array $choices, ?int $createdBy = null): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        
        // Lösche alte Wahlen
        $builder->where('student_id', $studentId)->delete();
        
        // Speichere neue Wahlen
        $data = [];
        foreach ($choices as $priority => $offerId) {
            $data[] = [
                'student_id' => $studentId,
                'offer_id' => $offerId === 'no_participation' ? null : $offerId,
                'priority' => $priority,
                'created_by' => $createdBy,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        if (!empty($data)) {
            return $builder->insertBatch($data);
        }
        
        return true;
    }
}
