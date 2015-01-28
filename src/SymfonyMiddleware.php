<?php

namespace Mouf\StackPhp;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Request;

/**
 * This StackPHP middleware creates a middleware from a Symfony application.
 * Basically, the middleware will use the Symfony application to catch any request.
 * If no request is found, instead of returning a 404 page, control is passed
 * to the next middleware.
 *
 * @author David Négrier <david@mouf-php.com>
 */
class SymfonyMiddleware implements HttpKernelInterface
{
	private $request;
	private $type;
	private $catch;
	private $symfonyApp;

	/**
	 *
	 * @param HttpKernelInterface $app The next application the request will be forwarded to if not handled by Symfony
	 * @param HttpKernel $symfonyApp The Symfony application that will try catching requests
	 */
	public function __construct(HttpKernelInterface $app, HttpKernel $symfonyApp) {
		$this->symfonyApp = $symfonyApp;
		// TODO
		$this->symfonyApp->error(function(\Exception $e, $code) use ($app) {
			if ($code == 404) {
				return $app->handle($this->request, $this->type, $this->catch);
			} else {
				return;
			}
		});
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\HttpKernel\HttpKernelInterface::handle()
	 */
	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
		$this->request = $request;
		$this->type = $type;
		$this->catch = $catch;

		return $this->symfonyApp->handle($request, $type, $catch);
	}
}
