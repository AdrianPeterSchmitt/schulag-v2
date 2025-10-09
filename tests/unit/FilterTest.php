<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Filters\Auth as AuthFilter;

class FilterTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $namespace = 'App';

    /**
     * Test: Auth-Filter blockt nicht-eingeloggte User
     */
    public function testAuthFilterBlocksUnauthenticatedUsers()
    {
        $filter = new AuthFilter();
        $request = service('request');
        
        // Keine Session
        $result = $filter->before($request);
        
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
    }

    /**
     * Test: Auth-Filter erlaubt eingeloggte User
     */
    public function testAuthFilterAllowsAuthenticatedUsers()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'ADMIN';
        $_SESSION['logged_in'] = true;
        
        $filter = new AuthFilter();
        $request = service('request');
        
        $result = $filter->before($request, ['admin']);
        
        $this->assertNull($result);
    }

    /**
     * Test: Auth-Filter prüft Rollen korrekt
     */
    public function testAuthFilterEnforcesRoles()
    {
        $_SESSION['user_id'] = 2;
        $_SESSION['user_role'] = 'TEACHER';
        $_SESSION['logged_in'] = true;
        
        $filter = new AuthFilter();
        $request = service('request');
        
        // Teacher versucht auf Admin zuzugreifen
        $result = $filter->before($request, ['admin']);
        
        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
    }

    /**
     * Test: Auth-Filter erlaubt multiple Rollen
     */
    public function testAuthFilterAllowsMultipleRoles()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'ADMIN';
        $_SESSION['logged_in'] = true;
        
        $filter = new AuthFilter();
        $request = service('request');
        
        // Admin ist in erlaubten Rollen
        $result = $filter->before($request, ['admin', 'coordinator']);
        
        $this->assertNull($result);
    }

    /**
     * Test: Auth-Filter ist case-insensitive für Rollen
     */
    public function testAuthFilterIsCaseInsensitive()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'admin'; // lowercase
        $_SESSION['logged_in'] = true;
        
        $filter = new AuthFilter();
        $request = service('request');
        
        $result = $filter->before($request, ['ADMIN']); // uppercase
        
        $this->assertNull($result);
    }

    /**
     * Test: Auth-Filter hat keinen after() Effekt
     */
    public function testAuthFilterAfterMethodDoesNothing()
    {
        $filter = new AuthFilter();
        $request = service('request');
        $response = service('response');
        
        $result = $filter->after($request, $response);
        
        $this->assertNull($result);
    }
}


