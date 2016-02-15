<?php

namespace Mi\Guzzle\ServiceBuilder\Configuration;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class ServicesConfiguration
{
    /**
     * @var ServiceConfiguration[]
     */
    private $services = [];

    /**
     * @param ServiceConfiguration $service
     */
    public function addService(ServiceConfiguration $service)
    {
        if ($this->hasServiceConfiguration($service->getName())) {
            //todo throw exception duplicated service
        }

        $this->services[$service->getName()] = $service;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasServiceConfiguration($name)
    {
        return array_key_exists($name, $this->services);
    }

    /**
     * @param string $name
     *
     * @return ServiceConfiguration
     */
    public function getServiceConfiguration($name)
    {
        if (!$this->hasServiceConfiguration($name)) {
            //todo throw exception service not found
        }

        return $this->services[$name];
    }

    /**
     * @return ServiceConfiguration[]
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param ServicesConfiguration $servicesConfiguration
     */
    public function mergeServicesConfiguration(ServicesConfiguration $servicesConfiguration)
    {
        foreach ($servicesConfiguration->getServices() as $service) {
            $this->addService($service);
        }
    }
}
