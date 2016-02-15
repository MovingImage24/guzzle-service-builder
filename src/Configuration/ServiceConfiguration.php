<?php

namespace Mi\Guzzle\ServiceBuilder\Configuration;

use GuzzleHttp\Command\Guzzle\Description;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class ServiceConfiguration
{
    private $name;
    private $fqcn;
    private $description;

    /**
     * @param string      $name
     * @param string      $fqcn
     * @param Description $description
     */
    public function __construct($name, $fqcn, Description $description)
    {
        $this->name = $name;
        $this->fqcn = $fqcn;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFqcn()
    {
        return $this->fqcn;
    }

    /**
     * @return Description
     */
    public function getDescription()
    {
        return $this->description;
    }
}
