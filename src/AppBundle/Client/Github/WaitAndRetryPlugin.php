<?php

namespace AppBundle\Client\Github;

use Http\Client\Common\Plugin;
use Http\Client\Exception;
use Psr\Http\Message\RequestInterface;
use Github\HttpClient\Message\ResponseMediator;
use Github\Exception\ApiLimitExceedException;
use Psr\Http\Message\ResponseInterface;

/**
 * WaitAndRetryPlugin
 */
class WaitAndRetryPlugin implements Plugin
{
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

        sleep($timeToSleep);
    }
}
