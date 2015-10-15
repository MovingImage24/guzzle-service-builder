<?php

namespace Mi\Guzzle\ServiceBuilder\Subscriber;

use GuzzleHttp\Command\Event\PreparedEvent;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\UriTemplate;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class PrepareApiVersion implements SubscriberInterface
{
    private $description;

    /**
     * @param Description $description
     */
    public function __construct(Description $description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['prepared' => ['onPrepared']];
    }

    /**
     * @param PreparedEvent $event
     */
    public function onPrepared(PreparedEvent $event)
    {
        $request = $event->getRequest();

        $uriTemplate = new UriTemplate();

        $request->setUrl(
            $uriTemplate->expand(urldecode($request->getUrl()), ['apiVersion' => $this->description->getApiVersion()])
        );
    }
}
