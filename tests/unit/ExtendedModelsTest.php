<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\UserModel;
use App\Models\KlasseModel;
use App\Models\ClubModel;

/**
 * Erweiterte Model-Tests für bessere Coverage
 */
class ExtendedModelsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $namespace = 'App';

    /**
     * Test: UserModel - getAllUsers
     */
    public function testUserModelGetAllUsers()
    {
        $model = new UserModel();
        
        // 3 User erstellen
        for ($i = 1; $i <= 3; $i++) {
            $model->insert([
                'name' => "User $i",
                'email' => "user$i@test.com",
                'password' => password_hash('test', PASSWORD_DEFAULT),
                'role' => 'TEACHER',
            ]);
        }
        
        $users = $model->findAll();
        
        $this->assertCount(3, $users);
    }

    /**
     * Test: UserModel - findByEmail
     */
    public function testUserModelFindByEmail()
    {
        $model = new UserModel();
        
        $model->insert([
            'name' => 'Test User',
            'email' => 'find@example.com',
            'password' => password_hash('test', PASSWORD_DEFAULT),
            'role' => 'ADMIN',
        ]);
        
        $user = $model->where('email', 'find@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user['name']);
        $this->assertEquals('ADMIN', $user['role']);
    }

    /**
     * Test: UserModel - findByRole
     */
    public function testUserModelFindByRole()
    {
        $model = new UserModel();
        
        $model->insert(['name' => 'Admin 1', 'email' => 'admin1@test.com', 'password' => password_hash('test', PASSWORD_DEFAULT), 'role' => 'ADMIN']);
        $model->insert(['name' => 'Teacher 1', 'email' => 'teacher1@test.com', 'password' => password_hash('test', PASSWORD_DEFAULT), 'role' => 'TEACHER']);
        $model->insert(['name' => 'Teacher 2', 'email' => 'teacher2@test.com', 'password' => password_hash('test', PASSWORD_DEFAULT), 'role' => 'TEACHER']);
        
        $teachers = $model->where('role', 'TEACHER')->findAll();
        
        $this->assertCount(2, $teachers);
    }

    /**
     * Test: KlasseModel - findByJahrgang
     */
    public function testKlasseModelFindByJahrgang()
    {
        $model = new KlasseModel();
        
        $model->insert(['name' => '5a', 'jahrgang' => 5, 'klassenleitung' => 'Test 1']);
        $model->insert(['name' => '5b', 'jahrgang' => 5, 'klassenleitung' => 'Test 2']);
        $model->insert(['name' => '6a', 'jahrgang' => 6, 'klassenleitung' => 'Test 3']);
        
        $klassen = $model->where('jahrgang', 5)->findAll();
        
        $this->assertCount(2, $klassen);
        $this->assertEquals('5a', $klassen[0]['name']);
        $this->assertEquals('5b', $klassen[1]['name']);
    }

    /**
     * Test: KlasseModel - orderBy
     */
    public function testKlasseModelOrdering()
    {
        $model = new KlasseModel();
        
        $model->insert(['name' => '10c', 'jahrgang' => 10, 'klassenleitung' => 'Test 1']);
        $model->insert(['name' => '5a', 'jahrgang' => 5, 'klassenleitung' => 'Test 2']);
        $model->insert(['name' => '8b', 'jahrgang' => 8, 'klassenleitung' => 'Test 3']);
        
        $klassen = $model->orderBy('jahrgang', 'ASC')->findAll();
        
        $this->assertEquals(5, $klassen[0]['jahrgang']);
        $this->assertEquals(8, $klassen[1]['jahrgang']);
        $this->assertEquals(10, $klassen[2]['jahrgang']);
    }

    /**
     * Test: ClubModel - Pagination
     */
    public function testClubModelPagination()
    {
        $model = new ClubModel();
        
        // 5 Clubs erstellen
        for ($i = 1; $i <= 5; $i++) {
            $model->insert([
                'titel' => "Club $i",
                'beschreibung_kurz' => "Beschreibung $i",
                'min_grade' => 5,
                'max_grade' => 10,
                'max_teilnehmer' => 20,
            ]);
        }
        
        // Erste 3 holen
        $clubs = $model->findAll(3);
        
        $this->assertCount(3, $clubs);
    }

    /**
     * Test: Model - countAll
     */
    public function testModelCountAll()
    {
        $userModel = new UserModel();
        $klasseModel = new KlasseModel();
        
        $userModel->insert(['name' => 'U1', 'email' => 'u1@test.com', 'password' => password_hash('test', PASSWORD_DEFAULT), 'role' => 'ADMIN']);
        $userModel->insert(['name' => 'U2', 'email' => 'u2@test.com', 'password' => password_hash('test', PASSWORD_DEFAULT), 'role' => 'TEACHER']);
        
        $klasseModel->insert(['name' => '7a', 'jahrgang' => 7, 'klassenleitung' => 'Test']);
        $klasseModel->insert(['name' => '7b', 'jahrgang' => 7, 'klassenleitung' => 'Test']);
        $klasseModel->insert(['name' => '7c', 'jahrgang' => 7, 'klassenleitung' => 'Test']);
        
        $this->assertEquals(2, $userModel->countAll());
        $this->assertEquals(3, $klasseModel->countAll());
    }

    /**
     * Test: Model - whereIn
     */
    public function testModelWhereIn()
    {
        $model = new ClubModel();
        
        $id1 = $model->insert(['titel' => 'Fußball', 'beschreibung_kurz' => 'Test', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 20]);
        $id2 = $model->insert(['titel' => 'Basketball', 'beschreibung_kurz' => 'Test', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 20]);
        $id3 = $model->insert(['titel' => 'Volleyball', 'beschreibung_kurz' => 'Test', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 20]);
        
        $clubs = $model->whereIn('id', [$id1, $id3])->findAll();
        
        $this->assertCount(2, $clubs);
    }

    /**
     * Test: Model - like search
     */
    public function testModelLikeSearch()
    {
        $model = new ClubModel();
        
        $model->insert(['titel' => 'Fußball AG', 'beschreibung_kurz' => 'Test', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 20]);
        $model->insert(['titel' => 'Basketball AG', 'beschreibung_kurz' => 'Test', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 20]);
        $model->insert(['titel' => 'Kunst Kurs', 'beschreibung_kurz' => 'Test', 'min_grade' => 5, 'max_grade' => 10, 'max_teilnehmer' => 20]);
        
        $clubs = $model->like('titel', 'AG')->findAll();
        
        $this->assertCount(2, $clubs);
    }
}

