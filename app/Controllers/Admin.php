<?php

namespace App\Controllers;

use App\Models\KlasseModel;
use App\Models\SchuelerModel;
use App\Models\ClubModel;
use App\Models\ClubOfferModel;

class Admin extends BaseController
{
    protected KlasseModel $klasseModel;
    protected SchuelerModel $schuelerModel;
    protected ClubModel $clubModel;
    protected ClubOfferModel $offerModel;

    public function __construct()
    {
        $this->klasseModel = new KlasseModel();
        $this->schuelerModel = new SchuelerModel();
        $this->clubModel = new ClubModel();
        $this->offerModel = new ClubOfferModel();
    }

    /**
     * Admin Dashboard - Übersicht
     * 
     * @return string
     */
    public function index(): string
    {
        $data = [
            'title' => 'Admin - Verwaltung',
            'klassen' => $this->klasseModel->findAll(),
            'clubs' => $this->clubModel->findAll(),
            'schueler_count' => $this->schuelerModel->countAll(),
        ];

        return view('admin/index', $data);
    }

    /**
     * Klassen verwalten
     * 
     * @return string
     */
    public function klassen(): string
    {
        $data = [
            'title' => 'Klassen verwalten',
            'klassen' => $this->klasseModel->orderBy('jahrgang', 'ASC')->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/klassen', $data);
    }

    /**
     * Klasse erstellen (HTMX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface|string
     */
    public function createKlasse()
    {
        $rules = [
            'name' => 'required|min_length[2]',
            'jahrgang' => 'required|integer|greater_than[0]',
            'klassenleitung' => 'required|min_length[2]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'jahrgang' => $this->request->getPost('jahrgang'),
            'klassenleitung' => $this->request->getPost('klassenleitung'),
        ];

        if ($this->klasseModel->insert($data)) {
            // Return updated list for HTMX
            $klassen = $this->klasseModel->orderBy('jahrgang', 'ASC')->orderBy('name', 'ASC')->findAll();
            return view('admin/partials/klassen_list', ['klassen' => $klassen]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Fehler beim Erstellen der Klasse'
        ]);
    }

    /**
     * Klasse löschen (HTMX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface|string
     */
    public function deleteKlasse(int $id)
    {
        if ($this->klasseModel->delete($id)) {
            // Return updated list for HTMX
            $klassen = $this->klasseModel->orderBy('jahrgang', 'ASC')->orderBy('name', 'ASC')->findAll();
            return view('admin/partials/klassen_list', ['klassen' => $klassen]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'message' => 'Fehler beim Löschen'
        ]);
    }

    /**
     * Klasse aktualisieren (HTMX)
     *
     * @return \CodeIgniter\HTTP\ResponseInterface|string
     */
    public function updateKlasse(int $id)
    {
        try {
            $raw = $this->request->getRawInput();
            
            $data = [
                'name' => $raw['name'] ?? null,
                'jahrgang' => $raw['jahrgang'] ?? null,
                'klassenleitung' => $raw['klassenleitung'] ?? null,
            ];

            $data = array_filter($data, fn($v) => $v !== null);
            
            // Validierung der gelieferten PUT-Daten
            $rules = [
                'name' => 'required|min_length[1]|max_length[50]',
                'jahrgang' => 'required|integer|greater_than[0]|less_than[14]',
                'klassenleitung' => 'required|min_length[2]|max_length[255]',
            ];

            $validation = \Config\Services::validation();
            $validation->setRules($rules);
            if (!$validation->run($data)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'errors' => $validation->getErrors()
                    ]);
            }

            $this->klasseModel->update($id, $data);

            $klassen = $this->klasseModel->orderBy('jahrgang', 'ASC')->orderBy('name', 'ASC')->findAll();
            return view('admin/partials/klassen_list', ['klassen' => $klassen]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody('Fehler: ' . $e->getMessage());
        }
    }

    /**
     * Klasse Details anzeigen (für Schüler-Verwaltung)
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function showKlasse(int $id)
    {
        $klasse = $this->klasseModel->getWithSchueler($id);
        
        if (!$klasse) {
            return redirect()->to('/admin/klassen')->with('error', 'Klasse nicht gefunden');
        }

        $data = [
            'title' => 'Klasse: ' . $klasse['name'],
            'klasse' => $klasse,
        ];

        return view('admin/klasse_detail', $data);
    }

    /**
     * Schüler erstellen (HTMX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function createSchueler(int $klasseId)
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'typ_gl' => 'required|in_list[G,LE]',
        ];

        if (!$this->validate($rules)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'klasse_id' => $klasseId,
            'typ_gl' => $this->request->getPost('typ_gl'),
        ];

        try {
            $inserted = $this->schuelerModel->insert($data);
            log_message('debug', 'Schüler eingefügt mit ID: ' . $inserted);
            
            // Lade aktualisierte Klasse mit Schülern
            $klasse = $this->klasseModel->getWithSchueler($klasseId);
            log_message('debug', 'Anzahl Schüler nach Insert: ' . count($klasse['schueler']));
            
            // Gebe das Content-Partial zurück (enthält Statistik + Liste)
            return $this->response->setBody(view('admin/partials/klasse_content', ['klasse' => $klasse]));
        } catch (\Exception $e) {
            log_message('error', 'Fehler beim Einfügen: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Fehler: ' . $e->getMessage());
        }
    }

    /**
     * Schüler löschen (HTMX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function deleteSchueler(int $klasseId, int $schuelerId)
    {
        try {
            $this->schuelerModel->delete($schuelerId);
            
            // Lade aktualisierte Klasse mit Schülern
            $klasse = $this->klasseModel->getWithSchueler($klasseId);
            
            // Gebe das Content-Partial zurück (enthält Statistik + Liste)
            return view('admin/partials/klasse_content', ['klasse' => $klasse]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody('Fehler: ' . $e->getMessage());
        }
    }

    /**
     * Schüler bearbeiten
     * 
     * @param int $klasseId
     * @param int $schuelerId
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function updateSchueler(int $klasseId, int $schuelerId): \CodeIgniter\HTTP\ResponseInterface|string
    {
        try {
            // Bei PUT-Requests müssen die Daten über getRawInput() gelesen werden
            $raw = $this->request->getRawInput();
            $data = [
                'name'   => $raw['name']   ?? '',
                'typ_gl' => $raw['typ_gl'] ?? '',
            ];

            // Validierung mit CI4-Validator (explizit Daten übergeben)
            $rules = [
                'name' => 'required|min_length[3]',
                'typ_gl' => 'required|in_list[G,LE]',
            ];

            $validation = \Config\Services::validation();
            $validation->setRules($rules);
            if (!$validation->run($data)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'errors' => $validation->getErrors()
                    ]);
            }

            $this->schuelerModel->update($schuelerId, $data);

            // Lade aktualisierte Klasse mit Schülern
            $klasse = $this->klasseModel->getWithSchueler($klasseId);

            // Gebe das Content-Partial zurück (enthält Statistik + Liste)
            return view('admin/partials/klasse_content', ['klasse' => $klasse]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody('Fehler: ' . $e->getMessage());
        }
    }

    /**
     * AGs verwalten
     * 
     * @return string
     */
    public function clubs(): string
    {
        $clubs = $this->clubModel->findAll();
        
        // Für jeden Club die Angebote laden
        foreach ($clubs as &$club) {
            $club['offers'] = $this->offerModel->where('club_id', $club['id'])->findAll();
        }
        
        // Statistiken berechnen
        $activeOffersCount = $this->offerModel->where('active', 1)->countAllResults();
        $totalCapacity = 0;
        $allOffers = $this->offerModel->where('active', 1)->findAll();
        foreach ($allOffers as $offer) {
            $totalCapacity += $offer['capacity'];
        }
        
        $data = [
            'title' => 'AGs verwalten',
            'clubs' => $clubs,
            'activeOffersCount' => $activeOffersCount,
            'totalCapacity' => $totalCapacity,
        ];

        return view('admin/clubs', $data);
    }

    /**
     * AG erstellen (HTMX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function createClub()
    {
        try {
            $rules = [
                'titel' => 'required|min_length[3]',
                'lehrkraft' => 'required',
                'jahrgaenge' => 'required',
                'capacity' => 'required|integer|greater_than[0]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setStatusCode(400)->setBody('Validierungsfehler');
            }

            // Club erstellen
            $clubData = [
                'titel' => $this->request->getPost('titel'),
                'beschreibung' => $this->request->getPost('beschreibung'),
                'lehrkraft' => $this->request->getPost('lehrkraft'),
                'zweite_lehrkraft' => $this->request->getPost('zweite_lehrkraft'),
                'jahrgaenge' => $this->request->getPost('jahrgaenge'),
            ];

            $clubId = $this->clubModel->insert($clubData);

            // Angebot für aktuelles Schuljahr erstellen
            if ($clubId) {
                $offerData = [
                    'club_id' => $clubId,
                    'schoolyear' => '2024/2025',
                    'capacity' => $this->request->getPost('capacity'),
                    'active' => 1,
                ];
                $this->offerModel->insert($offerData);
            }

            // Aktualisierte Liste zurückgeben
            $clubs = $this->clubModel->findAll();
            foreach ($clubs as &$club) {
                $club['offers'] = $this->offerModel->where('club_id', $club['id'])->findAll();
            }
            
            $html = view('admin/clubs', [
                'clubs' => $clubs,
                'activeOffersCount' => $this->offerModel->where('active', 1)->countAllResults(),
                'totalCapacity' => array_sum(array_column($this->offerModel->where('active', 1)->findAll(), 'capacity')),
            ]);
            
            return $this->response->setBody($html);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody('Fehler: ' . $e->getMessage());
        }
    }

    /**
     * Club aktualisieren (HTMX)
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function updateClub(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setStatusCode(501)->setBody('Not Implemented');
    }

    /**
     * AG löschen (HTMX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface|string
     */
    public function deleteClub(int $id)
    {
        if ($this->clubModel->delete($id)) {
            $clubs = $this->clubModel->findAll();
            return view('admin/partials/clubs_list', ['clubs' => $clubs]);
        }

        return $this->response->setStatusCode(400);
    }
}
