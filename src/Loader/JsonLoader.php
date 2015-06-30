<?php

namespace Mi\Guzzle\ServiceBuilder\Loader;

use JsonSchema\RefResolver;
use JsonSchema\Uri\UriRetriever;
use Puli\Repository\Api\ResourceRepository;
use Webmozart\Json\JsonDecoder;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class JsonLoader implements LoaderInterface
{
    private $repository;
    private $loadedFiles;

    /**
     * @param ResourceRepository $repository
     */
    public function __construct(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $resource
     * @param string $schema
     *
     * @throws \Webmozart\Json\ValidationFailedException
     *
     * @return array
     */
    public function load($resource, $schemaName = 'services-schema.json')
    {
        $jsonDecoder = new JsonDecoder();

        $retriever = new UriRetriever();
        $schema    = $retriever->retrieve('file://' . realpath(__DIR__ . '/../../resources/schemas/' . $schemaName));

        $refResolver = new RefResolver($retriever);
        $refResolver->resolve($schema, 'file://' . __DIR__ . '/../../resources/schemas/' . $schemaName);

        $config = $jsonDecoder->decode($this->repository->get($resource)->getBody(), $schema);

        // Keep track of this file being loaded to prevent infinite recursion
        $this->loadedFiles[$resource] = true;

        $this->includeDesc($config);
        $this->mergeIncludes($config);

        return $config;
    }

    private function includeDesc($config)
    {
        if (property_exists($config, 'services')) {
            foreach ($config->services as $service) {
                $service->description = $this->load($service->description, 'description-schema.json');
            }
        }
    }

    /**
     * Merges in all include files.
     *
     * @param array $config Config data that contains includes
     *
     * @return array Returns the merged and included data
     */
    private function mergeIncludes(&$config)
    {
        if (property_exists($config, 'includes')) {
            foreach ($config->includes as $path) {

                // Don't load the same files more than once
                if (!array_key_exists($path, $this->loadedFiles)) {
                    $this->loadedFiles[$path] = true;
                    $config                   = (object)array_merge_recursive(
                        (array)$this->load($path),
                        (array)$config
                    );
                }
            }
            unset($config->includes);
        }
    }
}
