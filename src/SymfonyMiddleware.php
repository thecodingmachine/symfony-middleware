<?php

namespace Mouf\StackPhp;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This StackPHP middleware creates a middleware from a Symfony application.
 * Basically, the middleware will use the Symfony application to catch any request.
 * If no request is found, instead of returning a 404 page, control is passed
 * to the next middleware.
 *
 * @author David Négrier <david@mouf-php.com>
 */
class SymfonyMiddleware implements HttpKernelInterface, TerminableInterface
{
    private $app;
    private $symfonyApp;
    private $initDone = false;

    /**
     *
     * @param HttpKernelInterface $app The next application the request will be forwarded to if not handled by Symfony
     * @param HttpKernel $symfonyApp The Symfony application that will try catching requests
     */
    public function __construct(HttpKernelInterface $app, KernelInterface $symfonyApp)
    {
        $this->app = $app;
        $this->symfonyApp = $symfonyApp;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\HttpKernel\HttpKernelInterface::handle()
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (!$this->initDone) {
            $this->symfonyApp->boot();
            $dispatcher = $this->symfonyApp->getContainer()->get('event_dispatcher');
            $dispatcher->addListener('kernel.exception', function(Event $event) use ($request, $type, $catch) {
                if ($event->getException() instanceof NotFoundHttpException) {
                    $response = $this->app->handle($request, $type, $catch);
                    // Let's force the return code of the response into HttpKernel:
                    $response->headers->set('X-Status-Code', $response->getStatusCode());
                    $event->setResponse($response);
                }
            });

            $this->initDone = true;
        }

        return $this->symfonyApp->handle($request, $type, $catch);
    }
	
    /**
     * Terminates a request/response cycle.
     *
     * Should be called after sending the response and before shutting down the kernel.
     *
     * @param Request $request A Request instance
     * @param \Symfony\Component\HttpFoundation\Response $response A Response instance
     *
     * @api
     */
    public function terminate(Request $request, Response $response)
    {
        $this->symfonyApp->terminate($request, $response);
    }
}
