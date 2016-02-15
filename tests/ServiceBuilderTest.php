<?php

namespace Mi\Guzzle\ServiceBuilder\Tests;

use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Mi\Guzzle\ServiceBuilder\Configuration\ServiceConfiguration;
use Mi\Guzzle\ServiceBuilder\Configuration\ServicesConfiguration;
use Mi\Guzzle\ServiceBuilder\Loader\LoaderInterface;
use Mi\Guzzle\ServiceBuilder\ServiceBuilder;
use Mi\Guzzle\ServiceBuilder\ServiceFactory;
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
     * @var ObjectProphecy
     */
    private $servicesConfiguration;

    /**
     * @test
     */
    public function instantiateClientOnlyOnce()
    {
        $client = $this->prophesize(GuzzleClient::class);
        $serviceConfiguration = $this->prophesize(ServiceConfiguration::class);

        $this->servicesConfiguration->getServiceConfiguration('dummy')->willReturn($serviceConfiguration->reveal());

        $this->serviceFactory->factory($serviceConfiguration->reveal())->willReturn($client->reveal())->shouldBeCalledTimes(1);

        self::assertEquals($client->reveal(), $this->serviceBuilder->get('dummy'));
        self::assertEquals($client->reveal(), $this->serviceBuilder->get('dummy'));
    }

    protected function setUp()
    {
        $this->servicesConfiguration = $this->prophesize(ServicesConfiguration::class);
        $this->serviceFactory = $this->prophesize(ServiceFactory::class);
        $this->loader = $this->prophesize(LoaderInterface::class);

        $this->loader->loadServices('resource_path')->willReturn($this->servicesConfiguration->reveal());

        $this->serviceBuilder = new ServiceBuilder($this->loader->reveal(), $this->serviceFactory->reveal(), 'resource_path');
    }
}
