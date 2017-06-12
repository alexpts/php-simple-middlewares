<?php

use Psr\Http\Message\ServerRequestInterface;
use PTS\Middleware\MiddlewareManager;
use PHPUnit\Framework\TestCase;

include_once __DIR__ . '/Request.php';
include_once __DIR__ . '/MiddlewareA.php';
include_once __DIR__ . '/MiddlewareB.php';
include_once __DIR__ . '/MiddlewareC.php';

class MiddlewareManagerTest extends TestCase
{

    /** @var MiddlewareManager */
    protected $manager;

    protected function setUp(): void
    {
        $this->manager = new MiddlewareManager();
    }

    public function testCreate(): void
    {
        $this->assertInstanceOf(MiddlewareManager::class, $this->manager);
    }

    public function testSimpleMiddleware(): void
    {
        $this->manager->push(new MiddlewareA);
        $response = call_user_func($this->manager, new Request);

        $this->assertCount(3, $response);
        $this->assertEquals(200, $response['status']);
    }

    public function testWithoutMiddlewares(): void
    {
        $response = call_user_func($this->manager, new Request);
        $this->assertNull($response);
    }

    public function testWithOnlyRequestMiddlewares(): void
    {
        $this->manager->push(new MiddlewareB);
        $response = call_user_func($this->manager, new Request);

        $this->assertNull($response);
    }

    public function testResponseMiddleware(): void
    {
        $this->manager->push(new MiddlewareC);
        $this->manager->push(new MiddlewareA);
        $response = call_user_func($this->manager, new Request);

        $this->assertCount(4, $response);
        $this->assertEquals(200, $response['status']);
        $this->assertCount(1, $response['paged']);
    }

    public function testFunctionMiddleware(): void
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

    public function testCustomExceptionHandler(): void
    {
        $this->manager->push(function(ServerRequestInterface $request, callable $next){
            $response = $next($request);
            return $response . ' Hello world';
        });

        $this->manager->push(function(ServerRequestInterface $request, callable $next){
            throw new \Exception('Exception in middleware.');
        }, function (\Throwable $ex) {
            return $ex->getMessage();
        });

        $response = call_user_func($this->manager, new Request);
        $this->assertEquals('Exception in middleware. Hello world', $response);
    }

    public function testWithoutExceptionHandler(): void
    {
        $this->expectException(\BadFunctionCallException::class);

        $this->manager->push(function(ServerRequestInterface $request, callable $next){
            $response = $next($request);
            return $response . ' Hello world';
        });

        $this->manager->push(function(ServerRequestInterface $request, callable $next){
            throw new \BadFunctionCallException('Exception in middleware.');
        }, null);

        call_user_func($this->manager, new Request);
    }
}
