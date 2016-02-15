<?php

namespace Mi\Guzzle\ServiceBuilder;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use JMS\Serializer\SerializerInterface;
use Mi\Guzzle\ServiceBuilder\Configuration\ServiceConfiguration;
use Mi\Guzzle\ServiceBuilder\Subscriber\ClassResponse;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class ServiceFactory implements ServiceFactoryInterface
{
    private $serializer;
    private $client;

    /**
     * @param SerializerInterface $serializer
     * @param Client              $client
     */
    public function __construct(SerializerInterface $serializer, Client $client = null)
    {
        $this->serializer = $serializer;
        $this->client = $client !== null ? $client : new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function factory(ServiceConfiguration $config)
    {
        $class = $config->getFqcn();

        /** @var GuzzleClient $service */
        $service = new $class($this->client, $config->getDescription());
        $service->getEmitter()->attach(new ClassResponse($config->getDescription(), $this->serializer));

        return $service;
    }
}
