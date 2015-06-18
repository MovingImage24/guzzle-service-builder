<?php

namespace Mi\Guzzle\ServiceBuilder;

use GuzzleHttp\Command\Guzzle\GuzzleClient;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
interface ServiceFactoryInterface
{
    /**
     * @param array $config
     *
     * @return GuzzleClient
     */
    public function factory($config);
}