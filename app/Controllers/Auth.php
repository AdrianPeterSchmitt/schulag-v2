<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Login-Seite anzeigen
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function login()
    {
        // Wenn bereits eingeloggt → Redirect
        if (session()->get('user_id')) {
            $target = $this->redirectPathForRole((string) session()->get('user_role'));
            return redirect()->to($target);
        }

        return view('auth/login', ['title' => 'Login']);
    }

    /**
     * Login verarbeiten
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function doLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validierung
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // User suchen
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email oder Passwort falsch');
        }

        // Passwort prüfen
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email oder Passwort falsch');
        }

        // Session setzen
        $sessionData = [
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
            'logged_in' => true,
        ];

        session()->set($sessionData);

        // Redirect basierend auf Rolle
        return redirect()->to($this->redirectPathForRole((string) ($user['role'] ?? '')));
    }

    /**
     * Logout
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Erfolgreich abgemeldet');
    }

    /**
     * Berechnet die Ziel-URL anhand der Rolle.
     */
    private function redirectPathForRole(string $role): string
    {
        $normalized = strtoupper($role);
        return match ($normalized) {
            'ADMIN' => '/admin',
            'TEACHER' => '/klassen',
            'COORDINATOR' => '/allocation',
            default => '/admin',
        };
    }

    /**
     * Registrierung (nur für Admins)
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function register()
    {
        // Nur für eingeloggte Admins
        if (!session()->get('user_id') || strtoupper((string) session()->get('user_role')) !== 'ADMIN') {
            return redirect()->to('/login');
        }

        return view('auth/register', ['title' => 'Benutzer anlegen']);
    }

    /**
     * Registrierung verarbeiten
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function doRegister()
    {
        // Nur für eingeloggte Admins
        if (!session()->get('user_id') || strtoupper((string) session()->get('user_role')) !== 'ADMIN') {
            return redirect()->to('/login');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'role' => 'required|in_list[ADMIN,TEACHER,COORDINATOR]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/admin')->with('success', 'Benutzer erfolgreich angelegt');
        }

        return redirect()->back()->withInput()->with('error', 'Fehler beim Anlegen des Benutzers');
    }
}
