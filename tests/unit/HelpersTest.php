<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests für Helper-Funktionen
 */
class HelpersTest extends CIUnitTestCase
{
    /**
     * Test: getCurrentSchoolYear funktioniert
     */
    public function testGetCurrentSchoolYear()
    {
        $schoolYear = getCurrentSchoolYear();
        
        $this->assertIsString($schoolYear);
        $this->assertMatchesRegularExpression('/^\d{4}\/\d{4}$/', $schoolYear);
        
        // Format: YYYY/YYYY
        $parts = explode('/', $schoolYear);
        $this->assertCount(2, $parts);
        $this->assertEquals(1, (int)$parts[1] - (int)$parts[0]);
    }

    /**
     * Test: debug_log erstellt Log-Einträge
     */
    public function testDebugLogCreatesLogEntry()
    {
        // Log-Verzeichnis sollte existieren
        $this->assertTrue(is_dir(WRITEPATH . 'logs'));
        
        debug_log('Test message', ['key' => 'value'], 'debug');
        
        // Log-Datei sollte erstellt worden sein
        $logFile = WRITEPATH . 'logs/log-' . date('Y-m-d') . '.log';
        
        // Note: Log könnte auch .php Extension haben
        $phpLogFile = WRITEPATH . 'logs/log-' . date('Y-m-d') . '.php';
        
        $this->assertTrue(
            file_exists($logFile) || file_exists($phpLogFile),
            'Log-Datei sollte existieren'
        );
    }

    /**
     * Test: dd() function exists
     */
    public function testDdFunctionExists()
    {
        $this->assertTrue(function_exists('dd'));
    }

    /**
     * Test: debug_user_action function exists
     */
    public function testDebugUserActionFunctionExists()
    {
        $this->assertTrue(function_exists('debug_user_action'));
    }

    /**
     * Test: debug_performance function exists
     */
    public function testDebugPerformanceFunctionExists()
    {
        $this->assertTrue(function_exists('debug_performance'));
    }

    /**
     * Test: debug_allocation function exists
     */
    public function testDebugAllocationFunctionExists()
    {
        $this->assertTrue(function_exists('debug_allocation'));
    }

    /**
     * Test: debug_error function exists
     */
    public function testDebugErrorFunctionExists()
    {
        $this->assertTrue(function_exists('debug_error'));
    }

    /**
     * Test: debug_security function exists
     */
    public function testDebugSecurityFunctionExists()
    {
        $this->assertTrue(function_exists('debug_security'));
    }

    /**
     * Test: dump_query function exists
     */
    public function testDumpQueryFunctionExists()
    {
        $this->assertTrue(function_exists('dump_query'));
    }

    /**
     * Test: debug_database_query function exists
     */
    public function testDebugDatabaseQueryFunctionExists()
    {
        $this->assertTrue(function_exists('debug_database_query'));
    }

    /**
     * Test: debug_api_request function exists
     */
    public function testDebugApiRequestFunctionExists()
    {
        $this->assertTrue(function_exists('debug_api_request'));
    }
}

