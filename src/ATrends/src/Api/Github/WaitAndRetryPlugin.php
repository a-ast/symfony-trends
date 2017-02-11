<?php

namespace Aa\ATrends\Api\Github;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Github\HttpClient\Message\ResponseMediator;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * WaitAndRetryPlugin
 */
class WaitAndRetryPlugin implements Plugin
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }


    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        return $next($request)->then(
            function (ResponseInterface $response) use ($request, $next, $first) {

                if (403 !== $response->getStatusCode()) {
                    return $response;
                }

                $date = ResponseMediator::getHeader($response, 'Date');
                $reset = (int)ResponseMediator::getHeader($response, 'X-RateLimit-Reset');

                $this->waitForRateLimitRecovery($date, $reset);

                $promise = $this->handleRequest($request, $next, $first);

                return $promise->wait();
            }
        );
    }

    /**
     * @param $date
     * @param $reset
     */
    protected function waitForRateLimitRecovery($date, $reset)
    {
        $responseDate = new \DateTime($date);
        $current = $responseDate->getTimestamp();

        $timeToSleep = $reset - $current;

        $event = new GenericEvent();
        $event->setArgument('wait', $timeToSleep);

        $this->dispatcher->dispatch('github_api.before_wait_for_rate_limit_recovery', $event);
        sleep($timeToSleep);
    }
}
