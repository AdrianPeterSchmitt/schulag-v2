<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin User erstellen (falls nicht vorhanden)
        $existingUser = $this->db->table('users')->where('email', 'admin@schulag.test')->get()->getRow();
        if (!$existingUser) {
            $this->db->table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@schulag.test',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 2. Klassen erstellen (Jahrgang 5-10)
        $klassen = [];
        foreach ([5, 6, 7, 8, 9, 10] as $jahrgang) {
            foreach (['a', 'b', 'c'] as $klasse) {
                $klassenName = $jahrgang . $klasse;
                $this->db->table('klassen')->insert([
                    'name' => $klassenName,
                    'jahrgang' => $jahrgang,
                    'klassenleitung' => 'Lehrer ' . strtoupper($klassenName),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $klassen[] = $this->db->insertID();
            }
        }

        // 3. Schüler erstellen (5 pro Klasse)
        $schuelerIds = [];
        $vornamen = ['Max', 'Anna', 'Leon', 'Emma', 'Noah', 'Mia', 'Felix', 'Sophie', 'Paul', 'Laura'];
        $nachnamen = ['Müller', 'Schmidt', 'Schneider', 'Fischer', 'Weber', 'Meyer', 'Wagner', 'Becker', 'Schulz', 'Hoffmann'];
        $typen = ['GL', 'LL'];

        foreach ($klassen as $klasseId) {
            for ($i = 0; $i < 5; $i++) {
                $vorname = $vornamen[array_rand($vornamen)];
                $nachname = $nachnamen[array_rand($nachnamen)];
                
                $this->db->table('schueler')->insert([
                    'klasse_id' => $klasseId,
                    'name' => $vorname . ' ' . $nachname,
                    'typ_gl' => $typen[array_rand($typen)],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $schuelerIds[] = $this->db->insertID();
            }
        }

        // 4. AGs erstellen
        $clubs = [
            ['titel' => 'Fußball', 'beschreibung_kurz' => 'Fußball für alle Jahrgangsstufen', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 20],
            ['titel' => 'Basketball', 'beschreibung_kurz' => 'Basketball Training', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 18],
            ['titel' => 'Schach', 'beschreibung_kurz' => 'Schach lernen und spielen', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 15],
            ['titel' => 'Kunst', 'beschreibung_kurz' => 'Kreatives Gestalten', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 16],
            ['titel' => 'Theater', 'beschreibung_kurz' => 'Theaterstücke einstudieren', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 20],
            ['titel' => 'Musik', 'beschreibung_kurz' => 'Gemeinsam musizieren', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 22],
            ['titel' => 'Kochen', 'beschreibung_kurz' => 'Kochen und Backen', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 12],
            ['titel' => 'Robotik', 'beschreibung_kurz' => 'Programmieren und Roboter bauen', 'min_grade' => 7, 'max_grade' => 10, 'max_teilnehmer' => 15],
            ['titel' => 'Garten', 'beschreibung_kurz' => 'Schulgarten pflegen', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 18],
            ['titel' => 'Medien', 'beschreibung_kurz' => 'Film und Fotografie', 'min_grade' => 6, 'max_grade' => 10, 'max_teilnehmer' => 16],
        ];

        $clubIds = [];
        foreach ($clubs as $club) {
            $this->db->table('clubs')->insert([
                'titel' => $club['titel'],
                'beschreibung_kurz' => $club['beschreibung_kurz'],
                'min_grade' => $club['min_grade'],
                'max_grade' => $club['max_grade'],
                'max_teilnehmer' => $club['max_teilnehmer'],
                'allowed_types_gl' => 'GL,LL', // Alle Typen erlaubt
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $clubIds[] = $this->db->insertID();
        }

        // 5. AG-Angebote erstellen (Schuljahr 2024/2025)
        foreach ($clubIds as $clubId) {
            $this->db->table('club_offers')->insert([
                'club_id' => $clubId,
                'schoolyear' => '2024/2025',
                'capacity' => rand(15, 25), // Zufällige Kapazität zwischen 15 und 25
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo "Testdaten erfolgreich erstellt!\n";
        echo "- 1 Admin User (admin@schulag.test / admin123)\n";
        echo "- " . count($klassen) . " Klassen\n";
        echo "- " . count($schuelerIds) . " Schüler\n";
        echo "- " . count($clubIds) . " AGs\n";
    }
}
