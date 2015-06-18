<?php

namespace Mi\Guzzle\ServiceBuilder\Loader;

use Webmozart\Json\JsonDecoder;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class JsonLoader implements LoaderInterface
{
    private $loadedFiles;

    /**
     * @param string $resource
     *
     * @return array
     *
     * @throws \Webmozart\Json\FileNotFoundException
     * @throws \Webmozart\Json\InvalidSchemaException
     * @throws \Webmozart\Json\ValidationFailedException
     */
    public function load($resource)
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->setObjectDecoding(JsonDecoder::ASSOC_ARRAY);

        $config = $jsonDecoder->decodeFile($resource);

        // Keep track of this file being loaded to prevent infinite recursion
        $this->loadedFiles[$resource] = true;

        $this->includeDesc($config, dirname($resource));
        $this->mergeIncludes($config, dirname($resource));

        return $config;
    }

    private function includeDesc(&$config, $basePath = null)
    {
        if (!empty($config['services'])) {
            foreach ($config['services'] as &$service) {
                if (!empty($service['description'])) {
                    $path = $service['description'];

                    if ($basePath && $path[0] !== DIRECTORY_SEPARATOR) {
                        $path = "{$basePath}/{$path}";
                    }

                    // Don't load the same files more than once
                    $service['description'] = $this->load($path);
                }
            }
        }
    }

    /**
     * Merges in all include files
     *
     * @param array  $config   Config data that contains includes
     * @param string $basePath Base path to use when a relative path is encountered
     *
     * @return array Returns the merged and included data
     */
    protected function mergeIncludes(&$config, $basePath = null)
    {
        if (!empty($config['includes'])) {
            foreach ($config['includes'] as $path) {

                if ($basePath && $path[0] !== DIRECTORY_SEPARATOR) {
                    $path = "{$basePath}/{$path}";
                }

                // Don't load the same files more than once
                if (!array_key_exists($path, $this->loadedFiles)) {
                    $this->loadedFiles[$path] = true;
                    $config                   = array_merge_recursive($this->load($path), $config);
                }
            }
            unset($config['includes']);
        }
    }

}
