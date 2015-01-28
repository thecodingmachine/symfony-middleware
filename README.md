Symfony middleware for StackPHP
===============================

This package contains a [StackPHP middleware](http://stackphp.com/) that enables you to push a Symfony
application (actually a `Kernel`) directly on the middleware stack.
The Symfony application will try to handle requests but instead of sending a 404 response if no route is found, 
the next middleware on the stack will be called.

Installation
------------

Through [Composer](https://getcomposer.org/) as [mouf/symfony-middleware](https://packagist.org/packages/mouf/symfony-middleware).

Usage
-----

Simply use the `SymfonyMiddleWare` class in your middleware stack:

```php
use Mouf\StackPhp\SymfonyMiddleware;
use My\Symfony\Application;
use Stack\Builder;

$app = ...

$symfonyApplication = new Application(...);

$stack = (new Stack\Builder())
    ->push(SymfonyMiddleware::class, $symfonyApplication);

$app = $stack->resolve($app);
```

Why?
----

Why would I want to make a Symfony app a middleware?
Because if every app becomes a middleware, we can easily chain middlewares together, and therefore, chain many
frameworks in the same application... and this is cool :)
