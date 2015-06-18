<?php

namespace Mi\Guzzle\ServiceBuilder\Tests;

use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Mi\Guzzle\ServiceBuilder\Loader\JsonLoader;
use Mi\Guzzle\ServiceBuilder\ServiceBuilder;
use Mi\Guzzle\ServiceBuilder\ServiceFactory;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 * 
 * @covers Mi\Guzzle\ServiceBuilder\ServiceBuilder
 */
class ServiceBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceBuilder
     */
    private $serviceBuilder;

    /**
     * @var ObjectProphecy
     */
    private $serviceFactory;

    /**
     * @var ObjectProphecy
     */
    private $loader;

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No service is registered as test
     * @test
     */
    public function getWrongClient()
    {
        $this->serviceBuilder->get('test');
    }

    /**
     * @test
     */
    public function getClient()
    {
        $client = $this->prophesize(GuzzleClient::class);

        $this->serviceFactory->factory(['config'])->willReturn($client->reveal());

        self::assertEquals($client->reveal(), $this->serviceBuilder->get('dummy'));
    }

    /**
     * @test
     */
    public function instantiateClientOnlyOnce()
    {
        $client = $this->prophesize(GuzzleClient::class);

        $this->serviceFactory->factory(['config'])->willReturn($client->reveal())->shouldBeCalledTimes(1);

        self::assertEquals($client->reveal(), $this->serviceBuilder->get('dummy'));
        self::assertEquals($client->reveal(), $this->serviceBuilder->get('dummy'));
    }

    protected function setUp()
    {
        $this->serviceFactory = $this->prophesize(ServiceFactory::class);
        $this->loader = $this->prophesize(JsonLoader::class);

        $this->loader->load('resource_path')->willReturn(['services' => ['dummy' => ['config']]]);

        $this->serviceBuilder = new ServiceBuilder($this->loader->reveal(), $this->serviceFactory->reveal(), 'resource_path');
    }
}
