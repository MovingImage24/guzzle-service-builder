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
    private $config;

    /**
     * @param LoaderInterface         $loader
     * @param ServiceFactoryInterface $serviceFactory
     * @param string                  $resource
     */
    public function __construct(LoaderInterface $loader, ServiceFactoryInterface $serviceFactory, $resource)
    {
        $this->loader = $loader;
        $this->serviceFactory = $serviceFactory;
        $this->config = $this->loader->load($resource)['services'];
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->config)) {
            throw new \RuntimeException('No service is registered as ' . $name);
        }

        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        }

        $builder = &$this->config[$name];

        $service = $this->serviceFactory->factory($builder);
        $this->services[$name] = $service;

        return $service;
    }
}
