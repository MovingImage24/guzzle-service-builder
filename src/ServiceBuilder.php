<?php

namespace Mi\Guzzle\ServiceBuilder;

use Mi\Guzzle\ServiceBuilder\Loader\LoaderInterface;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class ServiceBuilder implements ServiceBuilderInterface
{
    private $services = [];
    private $loader;
    private $serviceFactory;
    private $servicesConfiguration;

    /**
     * @param LoaderInterface         $loader
     * @param ServiceFactoryInterface $serviceFactory
     * @param string                  $resource
     */
    public function __construct(LoaderInterface $loader, ServiceFactoryInterface $serviceFactory, $resource)
    {
        $this->loader = $loader;
        $this->serviceFactory = $serviceFactory;
        $this->servicesConfiguration = $this->loader->loadServices($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        }

        $service = $this->serviceFactory->factory($this->servicesConfiguration->getServiceConfiguration($name));
        $this->services[$name] = $service;

        return $service;
    }
}
