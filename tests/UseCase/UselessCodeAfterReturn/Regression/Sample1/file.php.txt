<?php

namespace App\Application\ExternalData\ArticleWhitelist;

use Assert\Assert;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

class Client
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $guzzleClient;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(ClientInterface $guzzleClient, LoggerInterface $logger)
    {
        $this->guzzleClient = $guzzleClient;
        $this->logger = $logger;
    }

    /**
     * This method proxies an HTTP GET call, and accepts a URI or an array of
     * URIs as input.
     *
     * @param string|string[] $endpoints
     *
     * @return array<string|null> An array of response content (or `null` if the request was not successful)
     *                            matching each input endpoint/request
     */
    public function get($endpoints): array
    {
        if (is_string($endpoints)) {
            $endpoints = [$endpoints];
        }

        Assert::that($endpoints)->isArray();
        Assert::thatAll($endpoints)->url();

        $requests = array_map(function (string $endpoint) {
            return $this->guzzleClient->requestAsync('GET', $endpoint);
        }, $endpoints);

        $requests = Promise\settle($requests)
            ->then(function (array $states) {
                return array_map(function (array $state) {
                    if ($state['state'] === PromiseInterface::FULFILLED) {
                        // make sure there is no BOM in the body content
                        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $state['value']->getBody()->getContents());
                    }

                    $uri = (string) $state['reason']->getRequest()->getUri();

                    $this->logger->error("Error while trying to fetch article whitelist from `{$uri}`", [
                        'error' => $state['reason'],
                    ]);
                }, $states);
            })
        ;

        $this->logger->notice('Fetching article whitelists from multiple endpoints', [
            'endpoints' => $endpoints,
        ]);

        return $requests->wait();
    }
}
