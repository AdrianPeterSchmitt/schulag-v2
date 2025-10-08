<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\RedirectResponse
    {
        $session = session();

        if (!$session->get('user_id')) {
            return redirect()->to('/login');
        }

        // Redirect basierend auf Rolle
        $role = (string) $session->get('user_role');
        $normalized = strtoupper($role);
        
        return match ($normalized) {
            'ADMIN' => redirect()->to('/admin'),
            'TEACHER' => redirect()->to('/klassen'),
            'COORDINATOR' => redirect()->to('/allocation'),
            default => redirect()->to('/admin'),
        };
    }
}
