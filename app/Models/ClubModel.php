<?php

namespace App\Models;

use CodeIgniter\Model;

class ClubModel extends Model
{
    protected $table = 'clubs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'titel',
        'beschreibung_kurz',
        'min_grade',
        'max_grade',
        'allowed_types_gl',
        'max_teilnehmer',
        'zweite_lehrkraft_name',
        'zweite_lehrkraft_email',
        'zweite_lehrkraft_telefon',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'titel' => 'required|min_length[3]',
        'max_teilnehmer' => 'required|integer|greater_than_equal_to[6]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get alle Offers für einen Club
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getOffers(int $clubId): array
    {
        $offerModel = new ClubOfferModel();
        return $offerModel->where('club_id', $clubId)->findAll();
    }

    /**
     * Prüft ob ein Club für einen Schüler erlaubt ist
     */
    public function isAllowedForStudent(int $clubId, int $studentId): bool
    {
        $club = $this->find($clubId);
        if (!$club) {
            return false;
        }

        $schuelerModel = new SchuelerModel();
        $student = $schuelerModel->getWithKlasse($studentId);
        
        if (!$student || !isset($student['klasse'])) {
            return false;
        }

        // Jahrgangs-Check
        if ($club['min_grade'] && $student['klasse']['jahrgang'] < $club['min_grade']) {
            return false;
        }

        if ($club['max_grade'] && $student['klasse']['jahrgang'] > $club['max_grade']) {
            return false;
        }

        // Förderschwerpunkt-Check (G/LE)
        if ($club['allowed_types_gl']) {
            $allowedTypes = explode(',', $club['allowed_types_gl']);
            if (!in_array($student['typ_gl'], $allowedTypes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Prüft ob die AG eine zweite Lehrkraft benötigt (ab 12 Teilnehmern)
     */
    public function requiresZweiteLehrkraft(int $clubId): bool
    {
        $club = $this->find($clubId);
        return $club && $club['max_teilnehmer'] >= 12;
    }

    /**
     * Prüft ob die AG im Einzelbetreuungs-Bereich ist (6-11 Teilnehmer)
     */
    public function isEinzelbetreuung(int $clubId): bool
    {
        $club = $this->find($clubId);
        return $club && $club['max_teilnehmer'] >= 6 && $club['max_teilnehmer'] <= 11;
    }

    /**
     * Prüft ob die Teilnehmerzahl gültig ist (mindestens 6)
     */
    public function hasValidTeilnehmerzahl(int $clubId): bool
    {
        $club = $this->find($clubId);
        return $club && $club['max_teilnehmer'] >= 6;
    }

    /**
     * Prüft ob die AG eine zweite Lehrkraft hat
     */
    public function hasZweiteLehrkraft(int $clubId): bool
    {
        $club = $this->find($clubId);
        return $club && !empty($club['zweite_lehrkraft_name']);
    }
}
