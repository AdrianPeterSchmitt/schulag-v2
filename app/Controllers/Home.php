<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Abwärtskompatibel: Zeige einfache Weiterleitungsseite
        return view('welcome_message');
    }
}
