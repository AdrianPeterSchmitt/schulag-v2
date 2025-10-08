<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class SchulAG extends BaseConfig
{
    /**
     * Aktuelles Schuljahr
     * Format: YYYY/YYYY (z.B. "2024/2025")
     */
    public string $currentSchoolyear = '2024/2025';
    
    /**
     * Standard-Startdatum für Schuljahr
     * Format: MM-DD (Monat-Tag)
     */
    public string $schoolyearStartDate = '08-01'; // 1. August
    
    /**
     * Standard-Enddatum für Schuljahr
     * Format: MM-DD (Monat-Tag)
     */
    public string $schoolyearEndDate = '07-31'; // 31. Juli
    
    /**
     * Automatische Berechnung des Schuljahres
     * Wenn true, wird basierend auf aktuellem Datum berechnet
     */
    public bool $autoCalculateSchoolyear = false;
    
    /**
     * Minimum Teilnehmerzahl pro AG
     */
    public int $minTeilnehmer = 6;
    
    /**
     * Maximum Teilnehmerzahl Einzelbetreuung
     */
    public int $maxTeilnehmerEinzel = 11;
    
    /**
     * Minimum Teilnehmerzahl für zweite Lehrkraft
     */
    public int $minTeilnehmerZweiteLehrkraft = 12;
    
    /**
     * Verfügbare Jahrgangsstufen
     */
    public array $availableGrades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    
    /**
     * Verfügbare Förderschwerpunkte
     */
    public array $availableTypesGL = ['G', 'LE'];
    
    /**
     * Anzahl der Wahl-Prioritäten
     */
    public int $numberOfChoices = 3;
    
    /**
     * Get aktuelles Schuljahr
     * 
     * @return string
     */
    public function getCurrentSchoolyear(): string
    {
        if ($this->autoCalculateSchoolyear) {
            return $this->calculateCurrentSchoolyear();
        }
        
        return $this->currentSchoolyear;
    }
    
    /**
     * Berechne aktuelles Schuljahr basierend auf Datum
     * 
     * @return string
     */
    private function calculateCurrentSchoolyear(): string
    {
        $now = new \DateTime();
        $currentYear = (int)$now->format('Y');
        $currentMonth = (int)$now->format('m');
        
        // Schuljahr beginnt normalerweise im August (Monat 8)
        // Wenn wir vor August sind, sind wir noch im Schuljahr des vorherigen Jahres
        if ($currentMonth < 8) {
            $startYear = $currentYear - 1;
            $endYear = $currentYear;
        } else {
            $startYear = $currentYear;
            $endYear = $currentYear + 1;
        }
        
        return sprintf('%d/%d', $startYear, $endYear);
    }
    
    /**
     * Get alle verfügbaren Schuljahre (für Dropdown etc.)
     * 
     * @param int $yearsBack Wie viele Jahre zurück
     * @param int $yearsForward Wie viele Jahre voraus
     * @return array<string>
     */
    public function getAvailableSchoolyears(int $yearsBack = 3, int $yearsForward = 1): array
    {
        $current = $this->getCurrentSchoolyear();
        list($currentStart) = explode('/', $current);
        $currentStart = (int)$currentStart;
        
        $years = [];
        for ($i = -$yearsBack; $i <= $yearsForward; $i++) {
            $startYear = $currentStart + $i;
            $endYear = $startYear + 1;
            $years[] = sprintf('%d/%d', $startYear, $endYear);
        }
        
        return $years;
    }
}

