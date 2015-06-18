<?php

namespace Mi\Guzzle\ServiceBuilder;

use GuzzleHttp\Command\Guzzle\GuzzleClient;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
interface ServiceBuilderInterface
{
    /**
     * @param string $name
     *
     * @return GuzzleClient
     */
    public function get($name);
}
