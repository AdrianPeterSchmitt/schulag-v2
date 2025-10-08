<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Login-Seite anzeigen
     */
    public function login()
    {
        // Wenn bereits eingeloggt → Redirect
        if (session()->get('user_id')) {
            return redirect()->to('/admin');
        }

        return view('auth/login', ['title' => 'Login']);
    }

    /**
     * Login verarbeiten
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
        switch ($user['role']) {
            case 'admin':
                return redirect()->to('/admin');
            case 'teacher':
                return redirect()->to('/klassen');
            case 'coordinator':
                return redirect()->to('/allocation');
            default:
                return redirect()->to('/');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Erfolgreich abgemeldet');
    }

    /**
     * Registrierung (nur für Admins)
     */
    public function register()
    {
        // Nur für eingeloggte Admins
        if (!session()->get('user_id') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/login');
        }

        return view('auth/register', ['title' => 'Benutzer anlegen']);
    }

    /**
     * Registrierung verarbeiten
     */
    public function doRegister()
    {
        // Nur für eingeloggte Admins
        if (!session()->get('user_id') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'role' => 'required|in_list[admin,teacher,coordinator]',
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
