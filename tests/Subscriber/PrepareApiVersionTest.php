<?php

namespace Mi\Guzzle\ServiceBuilder\Tests\Common\Subscriber;

use GuzzleHttp\Command\Event\PreparedEvent;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Message\Request;
use Mi\Guzzle\ServiceBuilder\Subscriber\PrepareApiVersion;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 * 
 * @covers Mi\Guzzle\ServiceBuilder\Subscriber\PrepareApiVersion
 */
class PrepareApiVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrepareApiVersion
     */
    private $prepareApiVersion;

    /**
     * @var ObjectProphecy
     */
    private $description;

    /**
     * @test
     */
    public function prepareVersion()
    {
        $event = $this->prophesize(PreparedEvent::class);
        $request = $this->prophesize(Request::class);

        $request->getUrl()->willReturn('{apiVersion}/other');
        $request->setUrl('1.0/other')->shouldBeCalled();

        $event->getRequest()->willReturn($request->reveal());

        $this->description->getApiVersion()->willReturn('1.0');

        $this->prepareApiVersion->onPrepared($event->reveal());
    }

    /**
     * @test
     */
    public function prepareVersionWithNoTemplate()
    {
        $event = $this->prophesize(PreparedEvent::class);
        $request = $this->prophesize(Request::class);

        $request->getUrl()->willReturn('apiVersion/other');
        $request->setUrl('apiVersion/other')->shouldBeCalled();

        $event->getRequest()->willReturn($request->reveal());

        $this->description->getApiVersion()->willReturn('1.0');

        $this->prepareApiVersion->onPrepared($event->reveal());
    }

    /**
     * @test
     */
    public function getEvents()
    {
        self::assertInternalType('array', $this->prepareApiVersion->getEvents());
    }

    protected function setUp()
    {
        $this->description = $this->prophesize(Description::class);
        $this->prepareApiVersion = new PrepareApiVersion($this->description->reveal());
    }
}
