<?php
/**
 * PHPStan Bootstrap File
 * Defines CodeIgniter helper functions for static analysis
 */

// CodeIgniter Helper Functions
if (!function_exists('view')) {
    function view(string $name, array $data = [], array $options = []): string {
        return '';
    }
}

if (!function_exists('redirect')) {
    function redirect(?string $uri = null) {
        return new \CodeIgniter\HTTP\RedirectResponse(new \Config\App());
    }
}

if (!function_exists('base_url')) {
    function base_url($relativePath = '', ?string $scheme = null): string {
        return '';
    }
}

if (!function_exists('log_message')) {
    function log_message(string $level, string $message, array $context = []): bool {
        return true;
    }
}

if (!function_exists('esc')) {
    function esc($data, string $context = 'html', ?string $encoding = null) {
        return $data;
    }
}

if (!function_exists('session')) {
    /**
     * @return \CodeIgniter\Session\Session
     */
    function session(?string $val = null) {
        return new \CodeIgniter\Session\Session(new \Config\App());
    }
}

if (!function_exists('getCurrentSchoolyear')) {
    /**
     * @return string
     */
    function getCurrentSchoolyear(): string {
        $config = config('SchulAG');
        return $config->getCurrentSchoolyear();
    }
}

if (!function_exists('getAvailableSchoolyears')) {
    /**
     * @param int $yearsBack
     * @param int $yearsForward
     * @return array<string>
     */
    function getAvailableSchoolyears(int $yearsBack = 3, int $yearsForward = 1): array {
        $config = config('SchulAG');
        return $config->getAvailableSchoolyears($yearsBack, $yearsForward);
    }
}

if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}
