<?php

namespace Mi\Guzzle\ServiceBuilder\Loader;

use Mi\Guzzle\ServiceBuilder\Configuration\ServicesConfiguration;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
interface LoaderInterface
{
    /**
     * @param string $resource file path
     *
     * @return ServicesConfiguration
     */
    public function loadServices($resource);
}
