<?php

/**
 * SchulAG Helper Functions
 * 
 * Globale Helper-Funktionen für das SchulAG-System
 */

use Config\SchulAG;

if (!function_exists('getCurrentSchoolyear')) {
    /**
     * Get das aktuelle Schuljahr
     * 
     * @return string Format: "YYYY/YYYY"
     */
    function getCurrentSchoolyear(): string
    {
        $config = config('SchulAG');
        return $config->getCurrentSchoolyear();
    }
}

if (!function_exists('getAvailableSchoolyears')) {
    /**
     * Get alle verfügbaren Schuljahre
     * 
     * @param int $yearsBack
     * @param int $yearsForward
     * @return array<string>
     */
    function getAvailableSchoolyears(int $yearsBack = 3, int $yearsForward = 1): array
    {
        $config = config('SchulAG');
        return $config->getAvailableSchoolyears($yearsBack, $yearsForward);
    }
}

if (!function_exists('getSchulAGConfig')) {
    /**
     * Get die SchulAG-Konfiguration
     * 
     * @return \Config\SchulAG
     */
    function getSchulAGConfig(): \Config\SchulAG
    {
        return config('SchulAG');
    }
}

if (!function_exists('formatSchoolyear')) {
    /**
     * Formatiere Schuljahr für Anzeige
     * 
     * @param string $schoolyear
     * @return string
     */
    function formatSchoolyear(string $schoolyear): string
    {
        return "Schuljahr {$schoolyear}";
    }
}

