<?php

namespace App\Models;

use CodeIgniter\Model;

class ClubOfferModel extends Model
{
    protected $table = 'club_offers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'club_id',
        'schoolyear',
        'capacity',
        'room',
        'active',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'club_id' => 'required|integer',
        'schoolyear' => 'required|min_length[9]',
        'capacity' => 'required|integer|greater_than[0]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get Club für ein Offer
     * 
     * @return array<string, mixed>|null
     */
    public function getClub(int $offerId): ?array
    {
        $offer = $this->find($offerId);
        if ($offer) {
            $clubModel = new ClubModel();
            return $clubModel->find($offer['club_id']);
        }
        return null;
    }

    /**
     * Get alle Choices für ein Offer
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getChoices(int $offerId): array
    {
        $choiceModel = new ChoiceModel();
        return $choiceModel->where('offer_id', $offerId)->findAll();
    }

    /**
     * Get alle Allocations für ein Offer
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getAllocations(int $offerId): array
    {
        $allocationModel = new AllocationModel();
        return $allocationModel->where('offer_id', $offerId)->findAll();
    }

    /**
     * Zählt zugewiesene Schüler
     */
    public function assignedCount(int $offerId): int
    {
        $allocationModel = new AllocationModel();
        return $allocationModel
            ->where('offer_id', $offerId)
            ->where('status', 'ASSIGNED')
            ->countAllResults();
    }

    /**
     * Prüft ob noch Plätze frei sind
     */
    public function hasSpace(int $offerId): bool
    {
        $offer = $this->find($offerId);
        if (!$offer) {
            return false;
        }
        
        return $this->assignedCount($offerId) < $offer['capacity'];
    }

    /**
     * Get alle aktiven Offers für ein Schuljahr mit Club-Informationen
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getActiveOffers(string $schoolyear): array
    {
        $offers = $this->where('schoolyear', $schoolyear)
                       ->where('active', 1)
                       ->findAll();
        
        // Lade Club-Informationen für jedes Offer
        foreach ($offers as &$offer) {
            $offer['club'] = $this->getClub($offer['id']);
        }
        
        return $offers;
    }

    /**
     * Get Offer mit Club-Informationen
     * 
     * @return array<string, mixed>|null
     */
    public function getWithClub(int $offerId): ?array
    {
        $offer = $this->find($offerId);
        if ($offer) {
            $offer['club'] = $this->getClub($offerId);
        }
        return $offer;
    }
}
