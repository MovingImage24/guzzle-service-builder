<?php

namespace Mi\Guzzle\ServiceBuilder\Tests\Common\Loader;

use Mi\Guzzle\ServiceBuilder\Loader\JsonLoader;
use Puli\Repository\FilesystemRepository;
use Webmozart\Json\ValidationFailedException;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 *
 * @covers Mi\Guzzle\ServiceBuilder\Loader\JsonLoader
 */
class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonLoader
     */
    private $loader;

    /**
     * @test
     */
    public function load()
    {
       self::expectException(ValidationFailedException::class);
       self::expectExceptionMessage('services.test.description: The property description is required');

        $this->loader->loadServices('/fixtures/no_includes.json');
    }

    /**
     * @test
     */
    public function loadWithIncludes()
    {
        $config = $this->loader->loadServices('/fixtures/includes.json');

        self::assertTrue($config->hasServiceConfiguration('dummy'));
        self::assertTrue($config->hasServiceConfiguration('dummy-extra'));

        self::assertEquals('Mi\\Guzzle\\Test\\TestService', $config->getServiceConfiguration('dummy')->getFqcn());
        self::assertEquals('Mi\\Guzzle\\Test\\TestService', $config->getServiceConfiguration('dummy-extra')->getFqcn());
    }

    /**
     * @test
     */
    public function loadWithIncludesAndDescription()
    {
        $config = $this->loader->loadServices('/fixtures/includes_desc.json');

        self::assertTrue($config->hasServiceConfiguration('dummy'));
        self::assertEquals('Mi\\Guzzle\\Test\\TestService', $config->getServiceConfiguration('dummy')->getFqcn());
        self::assertEquals('dummy client', $config->getServiceConfiguration('dummy')->getDescription()->getName());
    }

    protected function setUp()
    {
        $repo = new FilesystemRepository(__DIR__, true);

        $this->loader = new JsonLoader(
            $repo,
            __DIR__ . '/../../resources/schemas/services-schema.json',
            __DIR__ . '/../../resources/schemas/description-schema.json'
        );
    }
}
