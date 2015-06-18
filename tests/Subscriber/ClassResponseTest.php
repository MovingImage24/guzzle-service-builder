<?php

namespace Mi\Guzzle\ServiceBuilder\Tests\Common\Subscriber;

use GuzzleHttp\Command\Command;
use GuzzleHttp\Command\Event\ProcessEvent;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\Operation;
use GuzzleHttp\Message\Response;
use JMS\Serializer\SerializerInterface;
use Mi\Guzzle\ServiceBuilder\Subscriber\ClassResponse;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 * 
 * @covers Mi\Guzzle\ServiceBuilder\Subscriber\ClassResponse
 */
class ClassResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassResponse
     */
    private $classResponse;

    /**
     * @var ObjectProphecy
     */
    private $description;

    /**
     * @var ObjectProphecy
     */
    private $serializer;

    /**
     * @test
     */
    public function processWithNoResponseModel()
    {
        $event = $this->prophesize(ProcessEvent::class);
        $command = $this->prophesize(Command::class);
        $operation = $this->prophesize(Operation::class);

        $event->getCommand()->willReturn($command->reveal());

        $command->getName()->willReturn('commandName');

        $operation->getResponseModel()->willReturn(null);

        $this->description->getOperation('commandName')->willReturn($operation->reveal());

        $this->classResponse->onProcess($event->reveal());
    }

    /**
     * @test
     */
    public function processWithNoClass()
    {
        $event = $this->prophesize(ProcessEvent::class);
        $command = $this->prophesize(Command::class);
        $operation = $this->prophesize(Operation::class);
        $serviceDescription = $this->prophesize(Description::class);

        $event->getCommand()->willReturn($command->reveal());

        $command->getName()->willReturn('commandName');

        $operation->getResponseModel()->willReturn('modelName');
        $operation->getServiceDescription()->willReturn($serviceDescription->reveal());

        $serviceDescription->getModel('modelName')->willReturn(new \stdClass());

        $this->description->getOperation('commandName')->willReturn($operation->reveal());

        $this->classResponse->onProcess($event->reveal());
    }

    /**
     * @test
     */
    public function process()
    {
        $event = $this->prophesize(ProcessEvent::class);
        $command = $this->prophesize(Command::class);
        $operation = $this->prophesize(Operation::class);
        $response = $this->prophesize(Response::class);
        $serviceDescription = $this->prophesize(Description::class);

        $event->getCommand()->willReturn($command->reveal());
        $event->getResponse()->willReturn($response->reveal());
        $event->setResult('deserialized')->shouldBeCalled();
        $event->stopPropagation()->shouldBeCalled();

        $response->getBody()->willReturn('body');

        $command->getName()->willReturn('commandName');

        $operation->getResponseModel()->willReturn('modelName');
        $operation->getServiceDescription()->willReturn($serviceDescription->reveal());

        $serviceDescription->getModel('modelName')->willReturn((object) ['class' => 'dummy']);

        $this->serializer->deserialize('body', 'dummy', 'json')->willReturn('deserialized');

        $this->description->getOperation('commandName')->willReturn($operation->reveal());

        $this->classResponse->onProcess($event->reveal());
    }

    /**
     * @test
     */
    public function getEvents()
    {
        self::assertInternalType('array', $this->classResponse->getEvents());
    }

    protected function setUp()
    {
        $this->description = $this->prophesize(Description::class);
        $this->serializer = $this->prophesize(SerializerInterface::class);
        $this->classResponse = new ClassResponse($this->description->reveal(), $this->serializer->reveal());
    }
}
