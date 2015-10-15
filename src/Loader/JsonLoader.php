<?php

namespace Mi\Guzzle\ServiceBuilder\Loader;

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
     *
     * @throws \Webmozart\Json\ValidationFailedException
     *
     * @return array
     */
    public function load($resource)
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->setObjectDecoding(JsonDecoder::ASSOC_ARRAY);

        $config = $jsonDecoder->decode($this->repository->get($resource)->getBody());

        // Keep track of this file being loaded to prevent infinite recursion
        $this->loadedFiles[$resource] = true;

        $this->includeDesc($config);
        $this->mergeIncludes($config);

        return $config;
    }

    private function includeDesc(&$config)
    {
        if (!empty($config['services'])) {
            foreach ($config['services'] as &$service) {
                if (!empty($service['description'])) {
                    $service['description'] = $this->load($service['description']);
                }
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
    protected function mergeIncludes(&$config)
    {
        if (!empty($config['includes'])) {
            foreach ($config['includes'] as $path) {

                // Don't load the same files more than once
                if (!array_key_exists($path, $this->loadedFiles)) {
                    $this->loadedFiles[$path] = true;
                    $config = array_merge_recursive($this->load($path), $config);
                }
            }
            unset($config['includes']);
        }
    }
}
