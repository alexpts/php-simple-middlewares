# php-simple-middlewares

[![Build Status](https://travis-ci.org/alexpts/php-simple-middlewares.svg?branch=master)](https://travis-ci.org/alexpts/php-simple-middlewares)
[![Test Coverage](https://codeclimate.com/github/alexpts/php-simple-middlewares/badges/coverage.svg)](https://codeclimate.com/github/alexpts/php-simple-middlewares/coverage)
[![Code Climate](https://codeclimate.com/github/alexpts/php-simple-middlewares/badges/gpa.svg)](https://codeclimate.com/github/alexpts/php-simple-middlewares)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-simple-middlewares/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-simple-middlewares/?branch=master)


Simple middlewares compatible with the PSR-7

MiddlewaresManager позволяет настраивать очередь выполнения программного обеспечения промежуточного уровня.
Каждый middleware принимает на вход объект запроса `$request`, при желании может что-то сделать с ним или создать новый объект `$request` и передать его в следующий middleware, ожидая от него объект response. Затчем он может что-то сделать с объектом response или просто передать его пердыдущему middleware.

Milddleware, может не вызывать слеюудщий middleware, а вернуть результат в ранее вызывнне middleware.
Т.е. каждый middleware получает управление 2 раза. В момент прохода объекта `$request` и в момент возврата результата в предудущие middleware.

Для обеспечения гибкости и встраивомости компонента, я решил не ограничивать middleware обработчкичи жестко никаким интерфейсом. Обработчиком может быть любой callable тип, которому будет передано 2 параметра `ServerRequestInterface $request`, `callable $next`. Для более формальной разработки каждый обработчик может поддерживать формальный интерфейс `MiddlewareInterface`.

Возвращаемый тип объекта `$response` также никак не ограничен. Вы можете вернуть из обработчика любой тип, например массив или объект типа ResponseInterface (psr-7).

## Installation

```$ composer require alexpts/php-simple-middlewares```

## Примеры

Произвольный обработчик

```php
use Psr\Http\Message\ServerRequestInterface;
use PTS\Middleware\MiddlewareManager;

$middlewareManager = new MiddlewareManager();
$middlewareManager->push(function($request, $next){
	// optionally modify the request
    $request = $request->...;
	
	// optionally skip the $next middleware and return early
    if (...) {
        return $response;
    }
	
	// optionally invoke the $next middleware and get back a new response
    $response = $next($request);
	
	// optionally modify the Response if desired
    $response = $response->...;
	
    return $response;
});
```


Несолько `MiddlewareInterface` обработчиков

```php
use Psr\Http\Message\ServerRequestInterface;
use PTS\Middleware\MiddlewareManager;
use PTS\Middleware\MiddlewareInterface;

class MiddlewareA implements MiddlewareInterface
{
	public function __invoke(ServerRequestInterface $request, callable $next)
    {
		$method = $request->getMethod();
        if (method !== 'GET') {
			return new JsonResponse(['status' => 405], 405);
		}
		
		$response = $next($request);
		// optionally modify the Response if desired
    	$response = $response->...;
		
		return $response;
    }
}

class FrontController implements MiddlewareInterface
{
	public function __invoke(ServerRequestInterface $request, callable $next)
    {
		// ... some work and create response		
		return $response;
    }
}

$middlewareManager = new MiddlewareManager();
$middlewareManager->push(new MiddlewareA);
$middlewareManager->push(new FrontController);

// ... creatre $request from global
$response = $middlewareManager($request);
```