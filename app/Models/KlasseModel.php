<?php

namespace App\Models;

use CodeIgniter\Model;

class KlasseModel extends Model
{
    protected $table = 'klassen';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'jahrgang',
        'klassenleitung',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[2]',
        'jahrgang' => 'required|integer|greater_than[0]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all Schüler für eine Klasse
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getSchueler(int $klasseId): array
    {
        $schuelerModel = new SchuelerModel();
        return $schuelerModel->where('klasse_id', $klasseId)->findAll();
    }

    /**
     * Prüft ob alle Schüler der Klasse ihre Wahlen abgegeben haben
     */
    public function isChoicesComplete(int $klasseId): bool
    {
        $schueler = $this->getSchueler($klasseId);
        
        // Wenn keine Schüler vorhanden, betrachten wir es als vollständig
        if (empty($schueler)) {
            return true;
        }

        $choiceModel = new ChoiceModel();
        
        foreach ($schueler as $student) {
            $choices = $choiceModel->where('student_id', $student['id'])->findAll();
            
            // Prüfe ob "Nimmt nicht teil" gewählt wurde
            $hasNoParticipation = false;
            foreach ($choices as $choice) {
                if ($choice['priority'] === 'no_participation') {
                    $hasNoParticipation = true;
                    break;
                }
            }
            
            // Zähle normale Wahlen (1, 2, 3)
            $normalChoicesCount = 0;
            foreach ($choices as $choice) {
                if (in_array($choice['priority'], ['1', '2', '3']) && $choice['offer_id'] !== null) {
                    $normalChoicesCount++;
                }
            }
            
            // Ein Schüler ist vollständig, wenn er entweder "Nimmt nicht teil" hat
            // ODER alle 3 Wünsche ausgefüllt hat
            if (!$hasNoParticipation && $normalChoicesCount !== 3) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get Klasse mit allen Schülern
     * 
     * @return array<string, mixed>|null
     */
    public function getWithSchueler(int $klasseId): ?array
    {
        $klasse = $this->find($klasseId);
        if ($klasse) {
            $klasse['schueler'] = $this->getSchueler($klasseId);
        }
        return $klasse;
    }
    
    /**
     * Get Klassen mit unvollständigen Wahlen
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getClassesWithIncompleteChoices(): array
    {
        $klassen = $this->findAll();
        $incomplete = [];
        
        foreach ($klassen as $klasse) {
            if (!$this->isChoicesComplete($klasse['id'])) {
                $incomplete[] = $klasse;
            }
        }
        
        return $incomplete;
    }
}
