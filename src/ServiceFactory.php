<?php

namespace Mi\Guzzle\ServiceBuilder;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use JMS\Serializer\SerializerInterface;
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
    public function factory($config)
    {
        $class = $config['class'];
        $desc = new Description($config['description']);

        /** @var GuzzleClient $service */
        $service = new $class($this->client, $desc);
        $service->getEmitter()->attach(new ClassResponse($desc, $this->serializer));

        return $service;
    }
}
