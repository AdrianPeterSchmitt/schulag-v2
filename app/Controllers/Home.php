<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Prüfe Datenbank-Verbindung
        $dbConnected = false;
        try {
            $db = \Config\Database::connect();
            // Test query to check connection
            $db->query('SELECT 1');
            $dbConnected = true;
        } catch (\Exception $e) {
            $dbConnected = false;
        }
        
        $data = [
            'db_connected' => $dbConnected,
            'environment' => ENVIRONMENT,
            'ci_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
        ];
        
        return view('welcome', $data);
    }
}
