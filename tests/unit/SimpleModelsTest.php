<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\UserModel;
use App\Models\KlasseModel;
use App\Models\ClubModel;

/**
 * Vereinfachte Model-Tests ohne komplexe DB-Constraints
 */
class SimpleModelsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $namespace = 'App';

    /**
     * Test: UserModel - CRUD Operations
     */
    public function testUserModelCRUD()
    {
        $model = new UserModel();
        
        // Create
        $userId = $model->insert([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'TEACHER',
        ]);
        
        $this->assertIsInt($userId);
        
        // Read
        $user = $model->find($userId);
        $this->assertEquals('Test User', $user['name']);
        $this->assertEquals('TEACHER', $user['role']);
        
        // Update
        $model->update($userId, ['name' => 'Updated Name']);
        $user = $model->find($userId);
        $this->assertEquals('Updated Name', $user['name']);
        
        // Delete
        $model->delete($userId);
        $user = $model->find($userId);
        $this->assertNull($user);
    }

    /**
     * Test: KlasseModel - CRUD Operations
     */
    public function testKlasseModelCRUD()
    {
        $model = new KlasseModel();
        
        // Create
        $klasseId = $model->insert([
            'name' => '5a',
            'jahrgang' => 5,
            'klassenleitung' => 'Herr Test',
        ]);
        
        $this->assertIsInt($klasseId);
        
        // Read
        $klasse = $model->find($klasseId);
        $this->assertEquals('5a', $klasse['name']);
        $this->assertEquals(5, $klasse['jahrgang']);
        
        // Update
        $model->update($klasseId, ['klassenleitung' => 'Frau Test']);
        $klasse = $model->find($klasseId);
        $this->assertEquals('Frau Test', $klasse['klassenleitung']);
        
        // Delete
        $model->delete($klasseId);
        $klasse = $model->find($klasseId);
        $this->assertNull($klasse);
    }

    /**
     * Test: ClubModel - CRUD Operations
     */
    public function testClubModelCRUD()
    {
        $model = new ClubModel();
        
        // Create
        $clubId = $model->insert([
            'titel' => 'Fußball AG',
            'beschreibung_kurz' => 'Fußball spielen und trainieren',
            'min_grade' => 5,
            'max_grade' => 10,
            'max_teilnehmer' => 25,
        ]);
        
        $this->assertIsInt($clubId);
        
        // Read
        $club = $model->find($clubId);
        $this->assertEquals('Fußball AG', $club['titel']);
        $this->assertEquals(25, $club['max_teilnehmer']);
        
        // Update
        $model->update($clubId, ['max_teilnehmer' => 30]);
        $club = $model->find($clubId);
        $this->assertEquals(30, $club['max_teilnehmer']);
        
        // Delete
        $model->delete($clubId);
        $club = $model->find($clubId);
        $this->assertNull($club);
    }

    /**
     * Test: UserModel - Email muss unique sein
     */
    public function testUserEmailUniqueness()
    {
        $model = new UserModel();
        
        $model->insert([
            'name' => 'User 1',
            'email' => 'unique@test.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'role' => 'ADMIN',
        ]);
        
        // Zweiter User mit gleicher Email sollte fehlschlagen
        $result = $model->insert([
            'name' => 'User 2',
            'email' => 'unique@test.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'role' => 'TEACHER',
        ]);
        
        $this->assertFalse($result);
    }

    /**
     * Test: KlasseModel - findAll liefert Array
     */
    public function testKlasseModelFindAll()
    {
        $model = new KlasseModel();
        
        $model->insert(['name' => '6a', 'jahrgang' => 6, 'klassenleitung' => 'Test 1']);
        $model->insert(['name' => '6b', 'jahrgang' => 6, 'klassenleitung' => 'Test 2']);
        
        $klassen = $model->findAll();
        
        $this->assertIsArray($klassen);
        $this->assertCount(2, $klassen);
    }

    /**
     * Test: ClubModel - Filter nach Jahrgang
     */
    public function testClubModelFilterByGrade()
    {
        $model = new ClubModel();
        
        $model->insert([
            'titel' => 'Schach (5-7)',
            'beschreibung_kurz' => 'Schach für jüngere',
            'min_grade' => 5,
            'max_grade' => 7,
            'max_teilnehmer' => 15,
        ]);
        
        $model->insert([
            'titel' => 'Robotik (8-10)',
            'beschreibung_kurz' => 'Robotik für ältere',
            'min_grade' => 8,
            'max_grade' => 10,
            'max_teilnehmer' => 12,
        ]);
        
        // Suche AGs für Jahrgang 6
        $clubs = $model->where('min_grade <=', 6)
                        ->where('max_grade >=', 6)
                        ->findAll();
        
        $this->assertCount(1, $clubs);
        $this->assertEquals('Schach (5-7)', $clubs[0]['titel']);
    }

    /**
     * Test: Models können nach ID suchen
     */
    public function testModelsFindById()
    {
        $userModel = new UserModel();
        $klasseModel = new KlasseModel();
        
        $userId = $userModel->insert([
            'name' => 'Find Test',
            'email' => 'find@test.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'role' => 'COORDINATOR',
        ]);
        
        $klasseId = $klasseModel->insert([
            'name' => '10a',
            'jahrgang' => 10,
            'klassenleitung' => 'Test',
        ]);
        
        $user = $userModel->find($userId);
        $klasse = $klasseModel->find($klasseId);
        
        $this->assertNotNull($user);
        $this->assertNotNull($klasse);
        $this->assertEquals($userId, $user['id']);
        $this->assertEquals($klasseId, $klasse['id']);
    }
}


