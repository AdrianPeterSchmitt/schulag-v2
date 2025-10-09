<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Config\SchulAG;

class ConfigTest extends CIUnitTestCase
{
    /**
     * Test: SchulAG Config kann geladen werden
     */
    public function testSchulAGConfigCanBeLoaded()
    {
        $config = new SchulAG();
        
        $this->assertInstanceOf(SchulAG::class, $config);
    }

    /**
     * Test: SchulAG Config hat default schoolyear
     */
    public function testSchulAGConfigHasDefaultSchoolyear()
    {
        $config = new SchulAG();
        
        $this->assertObjectHasProperty('defaultSchoolyear', $config);
        $this->assertIsString($config->defaultSchoolyear);
        $this->assertMatchesRegularExpression('/^\d{4}\/\d{4}$/', $config->defaultSchoolyear);
    }

    /**
     * Test: getCurrentSchoolyear Methode
     */
    public function testGetCurrentSchoolyearMethod()
    {
        $config = new SchulAG();
        
        if (method_exists($config, 'getCurrentSchoolyear')) {
            $schoolyear = $config->getCurrentSchoolyear();
            $this->assertIsString($schoolyear);
            $this->assertMatchesRegularExpression('/^\d{4}\/\d{4}$/', $schoolyear);
        } else {
            $this->markTestSkipped('getCurrentSchoolyear method not found');
        }
    }

    /**
     * Test: Config hat autoCalculateYear
     */
    public function testSchulAGConfigHasAutoCalculateYear()
    {
        $config = new SchulAG();
        
        if (property_exists($config, 'autoCalculateYear')) {
            $this->assertIsBool($config->autoCalculateYear);
        } else {
            $this->markTestSkipped('autoCalculateYear property not found');
        }
    }

    /**
     * Test: Config-Struktur ist valide
     */
    public function testConfigStructureIsValid()
    {
        $config = new SchulAG();
        
        // Config sollte ein Objekt sein
        $this->assertIsObject($config);
        
        // Config sollte von BaseConfig erben
        $this->assertInstanceOf(\CodeIgniter\Config\BaseConfig::class, $config);
    }
}

