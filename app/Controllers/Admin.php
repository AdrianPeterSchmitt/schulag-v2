<?php

namespace App\Controllers;

use App\Models\KlasseModel;
use App\Models\SchuelerModel;
use App\Models\ClubModel;
use App\Models\ClubOfferModel;

class Admin extends BaseController
{
    protected $klasseModel;
    protected $schuelerModel;
    protected $clubModel;
    protected $offerModel;

    public function __construct()
    {
        $this->klasseModel = new KlasseModel();
        $this->schuelerModel = new SchuelerModel();
        $this->clubModel = new ClubModel();
        $this->offerModel = new ClubOfferModel();
    }

    /**
     * Admin Dashboard - Übersicht
     */
    public function index()
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
     */
    public function klassen()
    {
        $data = [
            'title' => 'Klassen verwalten',
            'klassen' => $this->klasseModel->orderBy('jahrgang', 'ASC')->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/klassen', $data);
    }

    /**
     * Klasse erstellen (HTMX)
     */
    public function createKlasse()
    {
        $rules = [
            'name' => 'required|min_length[2]',
            'jahrgang' => 'required|integer|greater_than[0]',
            'klassenleitung' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
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
     */
    public function deleteKlasse($id)
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
     * Klasse Details anzeigen (für Schüler-Verwaltung)
     */
    public function showKlasse($id)
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
     */
    public function createSchueler(int $klasseId)
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'typ_gl' => 'required|in_list[G,LE]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setBody('Validierung fehlgeschlagen');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'klasse_id' => $klasseId,
            'typ_gl' => $this->request->getPost('typ_gl'),
        ];

        try {
            $this->schuelerModel->insert($data);
            
            // Lade aktualisierte Schüler-Liste
            $klasse = $this->klasseModel->getWithSchueler($klasseId);
            
            // Erstelle HTML für die Schüler-Liste
            $html = '';
            foreach ($klasse['schueler'] as $schueler) {
                $html .= '<div class="p-6 hover:bg-gray-50 transition flex items-center justify-between">';
                $html .= '  <div class="flex items-center space-x-4">';
                $html .= '    <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-white font-bold">';
                $html .= '      ' . esc(substr($schueler['name'], 0, 2)) . '';
                $html .= '    </div>';
                $html .= '    <div>';
                $html .= '      <h3 class="text-lg font-semibold text-gray-900">' . esc($schueler['name']) . '</h3>';
                $html .= '      <div class="flex items-center space-x-3 text-sm text-gray-600">';
                $html .= '        <span>' . esc($schueler['typ_gl']) . '</span>';
                $html .= '      </div>';
                $html .= '    </div>';
                $html .= '  </div>';
                $html .= '  <button hx-delete="' . base_url('admin/klassen/' . $klasseId . '/schueler/' . $schueler['id']) . '"';
                $html .= '          hx-target="#schueler-list" hx-swap="innerHTML"';
                $html .= '          hx-confirm="Schüler \'' . esc($schueler['name']) . '\' wirklich löschen?"';
                $html .= '          class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition flex items-center space-x-2">';
                $html .= '    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                $html .= '      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>';
                $html .= '    </svg>';
                $html .= '    <span>Löschen</span>';
                $html .= '  </button>';
                $html .= '</div>';
            }
            
            return $this->response->setBody($html);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody('Fehler: ' . $e->getMessage());
        }
    }

    /**
     * Schüler löschen (HTMX)
     */
    public function deleteSchueler(int $klasseId, int $schuelerId)
    {
        try {
            $this->schuelerModel->delete($schuelerId);
            
            // Lade aktualisierte Schüler-Liste
            $klasse = $this->klasseModel->getWithSchueler($klasseId);
            
            // Erstelle HTML für die Schüler-Liste
            $html = '';
            foreach ($klasse['schueler'] as $schueler) {
                $html .= '<div class="p-6 hover:bg-gray-50 transition flex items-center justify-between">';
                $html .= '  <div class="flex items-center space-x-4">';
                $html .= '    <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center text-white font-bold">';
                $html .= '      ' . esc(substr($schueler['name'], 0, 2)) . '';
                $html .= '    </div>';
                $html .= '    <div>';
                $html .= '      <h3 class="text-lg font-semibold text-gray-900">' . esc($schueler['name']) . '</h3>';
                $html .= '      <div class="flex items-center space-x-3 text-sm text-gray-600">';
                $html .= '        <span>' . esc($schueler['typ_gl']) . '</span>';
                $html .= '      </div>';
                $html .= '    </div>';
                $html .= '  </div>';
                $html .= '  <button hx-delete="' . base_url('admin/klassen/' . $klasseId . '/schueler/' . $schueler['id']) . '"';
                $html .= '          hx-target="#schueler-list" hx-swap="innerHTML"';
                $html .= '          hx-confirm="Schüler \'' . esc($schueler['name']) . '\' wirklich löschen?"';
                $html .= '          class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition flex items-center space-x-2">';
                $html .= '    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                $html .= '      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>';
                $html .= '    </svg>';
                $html .= '    <span>Löschen</span>';
                $html .= '  </button>';
                $html .= '</div>';
            }
            
            return $this->response->setBody($html);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody('Fehler: ' . $e->getMessage());
        }
    }

    /**
     * AGs verwalten
     */
    public function clubs()
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
     * AG löschen (HTMX)
     */
    public function deleteClub($id)
    {
        if ($this->clubModel->delete($id)) {
            $clubs = $this->clubModel->findAll();
            return view('admin/partials/clubs_list', ['clubs' => $clubs]);
        }

        return $this->response->setStatusCode(400);
    }
}
