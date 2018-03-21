<?php

namespace Tests\Unit\Auth;

use App\Http\Controllers\Auth\Ldap\LdapController;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LdapControllerTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testFormatName()
    {
        $controller = new LdapController();

        $result = $this->invokeMethod($controller, 'formatName', ['JOHN DOE']);

        $this->assertEquals('John Doe', $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetRdn()
    {
        $controller = new LdapController();

        $result = $this->invokeMethod($controller, 'getRdn', ['99999']);

        $this->assertContains('uid=99999', $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatKeySearch()
    {
        $controller = new LdapController();

        $result = $this->invokeMethod($controller, 'formatKeySearch', ['99999']);

        $this->assertEquals('cn=99999', $result);
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
