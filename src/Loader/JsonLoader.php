<?php

namespace Mi\Guzzle\ServiceBuilder\Loader;

use GuzzleHttp\Command\Guzzle\Description;
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriRetriever;
use Mi\Guzzle\ServiceBuilder\Configuration\ServiceConfiguration;
use Mi\Guzzle\ServiceBuilder\Configuration\ServicesConfiguration;
use Puli\Repository\Api\ResourceRepository;
use Webmozart\Json\JsonDecoder;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class JsonLoader implements LoaderInterface
{
    private $repository;
    private $loadedFiles;
    private $jsonDecoder;
    private $servicesSchema;
    private $descriptionSchema;

    /**
     * @param ResourceRepository $repository
     * @param string             $servicesSchemaPath
     * @param string             $descriptionSchemaPath
     */
    public function __construct(
        ResourceRepository $repository,
        $servicesSchemaPath = null,
        $descriptionSchemaPath = null
    ) {
        $this->repository = $repository;
        $this->jsonDecoder = new JsonDecoder();

        if ($servicesSchemaPath !== null) {
            $this->servicesSchema = $this->getSchema($servicesSchemaPath);
        }

        if ($descriptionSchemaPath !== null) {
            $this->descriptionSchema = $this->getSchema($descriptionSchemaPath);
        }
    }

    /**
     * @param string $resource
     *
     * @throws \Webmozart\Json\ValidationFailedException
     * @throws \Webmozart\Json\DecodingFailedException
     * @throws \Webmozart\Json\InvalidSchemaException
     * @throws \Puli\Repository\Api\ResourceNotFoundException
     * @throws \InvalidArgumentException
     *
     * @return ServicesConfiguration
     */
    public function loadServices($resource)
    {
        $services = $this->jsonDecoder->decode($this->repository->get($resource)->getBody(), $this->servicesSchema);

        $this->loadedFiles[$resource] = true;

        return $this->buildServicesConfiguration($services);
    }

    /**
     * @param object $servicesObject
     *
     * @throws \Webmozart\Json\ValidationFailedException
     * @throws \Webmozart\Json\DecodingFailedException
     * @throws \Webmozart\Json\InvalidSchemaException
     * @throws \Puli\Repository\Api\ResourceNotFoundException
     * @throws \InvalidArgumentException
     *
     * @return ServicesConfiguration
     */
    private function buildServicesConfiguration($servicesObject)
    {
        $services = new ServicesConfiguration();
        if (property_exists($servicesObject, 'services')) {
            foreach ($servicesObject->services as $name => $service) {
                $services->addService(
                    new ServiceConfiguration($name, $service->class, $this->loadDescription($service->description))
                );
            }
        }

        if (property_exists($servicesObject, 'includes')) {
            foreach ($servicesObject->includes as $path) {
                if (!array_key_exists($path, $this->loadedFiles)) {
                    $this->loadedFiles[$path] = true;
                    $services->mergeServicesConfiguration($this->loadServices($path));
                }
            }
        }

        return $services;
    }

    /**
     * @param string $resource
     *
     * @throws \Webmozart\Json\ValidationFailedException
     * @throws \Webmozart\Json\DecodingFailedException
     * @throws \Webmozart\Json\InvalidSchemaException
     * @throws \Puli\Repository\Api\ResourceNotFoundException
     * @throws \InvalidArgumentException
     *
     * @return Description
     */
    private function loadDescription($resource)
    {
        $descriptionObject = $this->jsonDecoder->decode(
            $this->repository->get($resource)->getBody(),
            $this->descriptionSchema
        );

        return new Description((array) $descriptionObject);
    }


    /**
     * @param string $schemaPath
     *
     * @return object
     */
    private function getSchema($schemaPath)
    {
        $schemaUri = 'file://' . realpath($schemaPath);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve($schemaUri);

        $refResolver = new RefResolver($retriever);
        $refResolver->resolve($schema, $schemaUri);

        return $schema;
    }
}
