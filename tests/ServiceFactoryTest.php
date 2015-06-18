<?php

namespace Mi\Guzzle\ServiceBuilder\Tests;

use GuzzleHttp\Command\Guzzle\GuzzleClient;
use JMS\Serializer\SerializerInterface;
use Mi\Guzzle\ServiceBuilder\ServiceFactory;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 * 
 * @covers Mi\Guzzle\ServiceBuilder\ServiceFactory
 */
class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function factory()
    {
        $serializer = $this->prophesize(SerializerInterface::class);
        $serviceFactory = new ServiceFactory($serializer->reveal());

        $service = $serviceFactory->factory(['class' => GuzzleClient::class, 'description' => []]);

        self::assertInstanceOf(GuzzleClient::class, $service);
    }
}
