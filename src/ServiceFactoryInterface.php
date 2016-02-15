<?php

namespace Mi\Guzzle\ServiceBuilder;

use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Mi\Guzzle\ServiceBuilder\Configuration\ServiceConfiguration;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
interface ServiceFactoryInterface
{
    /**
     * @param ServiceConfiguration $config
     *
     * @return GuzzleClient
     */
    public function factory(ServiceConfiguration $config);
}
