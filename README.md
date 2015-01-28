Silex middleware for StackPHP
=============================

This package contains a [StackPHP middleware](http://stackphp.com/) that unables you to push a Silex
application directly on the middleware stack. The Silex application will try to handle requests but
instead of sending a 404 response if nothing is found, the next middleware on the stack will be called.

Installation
------------

Through [Composer](https://getcomposer.org/) as [mouf/silex-middleware](https://packagist.org/packages/mouf/silex-middleware).

Usage
-----

Simply use the `SilexMiddleWare` class in your middleware stack:

```php
use Mouf\StackPhp\SilexMiddleware;
use Silex\Application;
use Stack\Builder;

$app = ...

$silex = new Silex\Application();
$silex->get('/hello', function(Request $request) {
    return 'Hello World!';
});

$stack = (new Stack\Builder())
    ->push(SilexMiddleWare::class, $silex);

$app = $stack->resolve($app);
```
