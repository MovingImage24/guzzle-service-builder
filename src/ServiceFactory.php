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

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($config)
    {
        $class = $config['class'];
        /** @var GuzzleClient $service */
        $desc = new Description($config['description']);
        $service = new $class(new Client(), $desc);
        $service->getEmitter()->attach(new ClassResponse($desc, $this->serializer));
        return $service;
    }
}
