<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    /**
     * Prüft ob User eingeloggt ist
     * 
     * @param array<string>|null $arguments
     * @return RequestInterface|ResponseInterface|string|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Prüfe ob User eingeloggt ist
        if (!$session->get('user_id')) {
            // Nicht eingeloggt → Redirect zu Login
            return redirect()->to('/login')->with('error', 'Bitte melden Sie sich an');
        }
        
        // Prüfe Rollen-Berechtigung
        if ($arguments) {
            $userRole = $session->get('user_role');
            $allowedRoles = is_array($arguments) ? $arguments : [$arguments];
            
            if (!in_array($userRole, $allowedRoles)) {
                // Keine Berechtigung
                return redirect()->to('/')->with('error', 'Keine Berechtigung für diesen Bereich');
            }
        }
        
        return null;
    }

    /**
     * @param array<string>|null $arguments
     * @return ResponseInterface|null
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nichts zu tun
        return null;
    }
}
