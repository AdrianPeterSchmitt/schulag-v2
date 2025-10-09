<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Services\AllocationService;
use App\Models\ClubModel;
use App\Models\ClubOfferModel;
use App\Models\KlasseModel;
use App\Models\SchuelerModel;
use App\Models\ChoiceModel;

class ImprovedAllocationServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $namespace = 'App';
    
    protected AllocationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AllocationService();
    }

    /**
     * Test: Service kann instanziiert werden
     */
    public function testServiceInstantiation()
    {
        $this->assertInstanceOf(AllocationService::class, $this->service);
    }

    /**
     * Test: checkCapacity mit Schuljahr-Parameter
     */
    public function testCheckCapacityWithSchoolyear()
    {
        $clubModel = new ClubModel();
        $offerModel = new ClubOfferModel();
        $klasseModel = new KlasseModel();
        $schuelerModel = new SchuelerModel();
        
        // Club erstellen
        $clubId = $clubModel->insert([
            'titel' => 'Test AG',
            'beschreibung_kurz' => 'Test',
            'min_grade' => 5,
            'max_grade' => 10,
            'max_teilnehmer' => 30,
        ]);
        
        // Offer erstellen
        $offerModel->insert([
            'club_id' => $clubId,
            'schoolyear' => '2024/2025',
            'capacity' => 30,
            'active' => 1,
        ]);
        
        // Schüler erstellen
        $klasseId = $klasseModel->insert(['name' => '5a', 'jahrgang' => 5, 'klassenleitung' => 'Test']);
        for ($i = 0; $i < 10; $i++) {
            $schuelerModel->insert([
                'klasse_id' => $klasseId,
                'name' => "Schüler $i",
                'typ_gl' => 'Ganztagsschule',
            ]);
        }
        
        $result = $this->service->checkCapacity('2024/2025');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('sufficient', $result);
        $this->assertArrayHasKey('total_students', $result);
        $this->assertArrayHasKey('total_capacity', $result);
    }

    /**
     * Test: getStatistics mit Schuljahr
     */
    public function testGetStatisticsWithSchoolyear()
    {
        $result = $this->service->getStatistics('2024/2025');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_students', $result);
        $this->assertArrayHasKey('students_with_choices', $result);
        $this->assertArrayHasKey('total_allocations', $result);
        $this->assertArrayHasKey('total_offers', $result);
    }

    /**
     * Test: Service-Methoden existieren
     */
    public function testServiceMethodsExist()
    {
        $this->assertTrue(method_exists($this->service, 'checkCapacity'));
        $this->assertTrue(method_exists($this->service, 'getStatistics'));
        $this->assertTrue(method_exists($this->service, 'allocate'));
    }
}

