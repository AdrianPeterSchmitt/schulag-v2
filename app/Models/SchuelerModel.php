<?php

namespace App\Models;

use CodeIgniter\Model;

class SchuelerModel extends Model
{
    protected $table = 'schueler';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'klasse_id',
        'typ_gl',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]',
        'klasse_id' => 'required|integer',
        'typ_gl' => 'required|in_list[G,LE]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get Klasse für einen Schüler
     * 
     * @return array<string, mixed>|null
     */
    public function getKlasse(int $schuelerId): ?array
    {
        $schueler = $this->find($schuelerId);
        if ($schueler) {
            $klasseModel = new KlasseModel();
            return $klasseModel->find($schueler['klasse_id']);
        }
        return null;
    }

    /**
     * Get alle Choices für einen Schüler
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getChoices(int $schuelerId): array
    {
        $choiceModel = new ChoiceModel();
        return $choiceModel->where('student_id', $schuelerId)->findAll();
    }

    /**
     * Get alle Allocations für einen Schüler
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getAllocations(int $schuelerId): array
    {
        $allocationModel = new AllocationModel();
        return $allocationModel->where('student_id', $schuelerId)->findAll();
    }

    /**
     * Get die aktive Zuteilung eines Schülers
     * 
     * @return array<string, mixed>|null
     */
    public function getAllocation(int $schuelerId): ?array
    {
        $allocationModel = new AllocationModel();
        return $allocationModel
            ->where('student_id', $schuelerId)
            ->where('status', 'ASSIGNED')
            ->first();
    }

    /**
     * Get Schüler mit Klasse
     * 
     * @return array<string, mixed>|null
     */
    public function getWithKlasse(int $schuelerId): ?array
    {
        $schueler = $this->find($schuelerId);
        if ($schueler) {
            $schueler['klasse'] = $this->getKlasse($schuelerId);
        }
        return $schueler;
    }
    
    /**
     * Get Gesamtanzahl aller Schüler
     */
    public function countAll(): int
    {
        return $this->countAllResults();
    }
}
