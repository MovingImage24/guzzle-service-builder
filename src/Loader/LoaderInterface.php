<?php

namespace Mi\Guzzle\ServiceBuilder\Loader;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
interface LoaderInterface
{
    /**
     * @param string $resource file path
     *
     * @return array
     */
    public function load($resource);
}
