<?php

namespace Mi\Guzzle\ServiceBuilder\Tests\Common\Loader;

use Mi\Guzzle\ServiceBuilder\Loader\JsonLoader;
use Puli\Repository\FilesystemRepository;

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
     * @expectedException \Webmozart\Json\ValidationFailedException
     * @expectedExceptionMessage services.test: the property description is required
     */
    public function load()
    {
        $this->loader->load('/fixtures/no_includes.json');
    }

    /**
     * @test
     */
    public function loadWithIncludesAndDescription()
    {
        $config = $this->loader->load('/fixtures/includes.json');

        self::assertArrayHasKey('services', $config);
        self::assertArraySubset(['test' => ['class' => 'Mi\\Guzzle\\Test\\DummyService']], $config['services']);
        self::assertArraySubset(['dummy' => ['class' => 'Mi\\Guzzle\\Test\\TestService']], $config['services']);
        self::assertArrayNotHasKey('includes', $config);
    }

    /**
     * @test
     */
    public function loadWithIncludesAndDescription()
    {
        $config = $this->loader->load('/fixtures/includes_desc.json');

        self::assertArrayHasKey('services', $config);
        self::assertArraySubset(['test' => ['class' => 'Mi\\Guzzle\\Test\\DummyService']], $config['services']);
        self::assertArraySubset(
            ['dummy' => ['class' => 'Mi\\Guzzle\\Test\\TestService', 'description' => ['name' => 'dummy client']]],
            $config['services']
        );
        self::assertArrayNotHasKey('includes', $config);
    }

    protected function setUp()
    {
        $repo = new FilesystemRepository(__DIR__, true);

        $this->loader = new JsonLoader($repo);
    }
}
