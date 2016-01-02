<?php

use Psr\Http\Message\ServerRequestInterface;
use PTS\Middleware\MiddlewareManager;

include_once __DIR__ . '/Request.php';
include_once __DIR__ . '/MiddlewareA.php';
include_once __DIR__ . '/MiddlewareB.php';
include_once __DIR__ . '/MiddlewareC.php';

class MiddlewareManagerTest extends PHPUnit_Framework_TestCase
{

    /** @var MiddlewareManager */
    protected $manager;

    protected function setUp()
    {
        $this->manager = new MiddlewareManager();
    }

    public function testCreate()
    {
        $this->assertInstanceOf('PTS\\Middleware\\MiddlewareManager', $this->manager);
    }

    public function testSimpleMiddleware()
    {
        $this->manager->push(new MiddlewareA);
        $response = call_user_func($this->manager, new Request);

        $this->assertCount(3, $response);
        $this->assertEquals(200, $response['status']);
    }

    public function testWithoutMiddlewares()
    {
        $response = call_user_func($this->manager, new Request);
        $this->assertNull($response);
    }

    public function testWithOnlyRequestMiddlewares()
    {
        $this->manager->push(new MiddlewareB);
        $response = call_user_func($this->manager, new Request);

        $this->assertNull($response);
    }

    public function testResponseMiddleware()
    {
        $this->manager->push(new MiddlewareC);
        $this->manager->push(new MiddlewareA);
        $response = call_user_func($this->manager, new Request);

        $this->assertCount(4, $response);
        $this->assertEquals(200, $response['status']);
        $this->assertCount(1, $response['paged']);
    }

    public function testFunctionMiddleware()
    {
        $this->manager->push(function(ServerRequestInterface $request, callable $next){
            $response = $next($request);
            return $response . ' Hello world';
        });

        $this->manager->push(function(ServerRequestInterface $request, callable $next){
            return 'Response creator.';
        });

        $response = call_user_func($this->manager, new Request);
        $this->assertEquals('Response creator. Hello world', $response);
    }
}