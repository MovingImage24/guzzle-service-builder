<?php

namespace Mi\Guzzle\ServiceBuilder\Subscriber;

use GuzzleHttp\Command\Event\ProcessEvent;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Event\SubscriberInterface;
use JMS\Serializer\SerializerInterface;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class ClassResponse implements SubscriberInterface
{

    private $serializer;
    private $description;

    /**
     * @param Description         $description
     * @param SerializerInterface $serializer
     */
    public function __construct(Description $description, SerializerInterface $serializer = null)
    {
        $this->description = $description;
        $this->serializer  = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['process' => ['onProcess', 100]];
    }

    /**
     * @param ProcessEvent $event
     */
    public function onProcess(ProcessEvent $event)
    {
        $command = $event->getCommand();
        $operation = $this->description->getOperation($command->getName());
        if (!($modelName = $operation->getResponseModel())) {
            return;
        }

        $model = $operation->getServiceDescription()->getModel($modelName);

        if (property_exists($model, 'class') === false || !($className = $model->class)) {
            return;
        }

        $event->setResult($this->serializer->deserialize($event->getResponse()->getBody(), $className, 'json'));

        $event->stopPropagation();
    }
}
