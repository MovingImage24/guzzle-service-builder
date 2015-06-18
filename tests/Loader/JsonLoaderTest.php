<?php

namespace Mi\Guzzle\ServiceBuilder\Tests\Common\Loader;

use Mi\Guzzle\ServiceBuilder\Loader\JsonLoader;


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
        $config = $this->loader->load(__DIR__.'/fixtures//no_includes.json');

        self::assertArrayHasKey('services', $config);
        self::assertArraySubset(['test' => ['class' => "Mi\\Guzzle\\Test\\DummyService"]], $config['services']);
    }

    /**
     * @test
     */
    public function loadWithIncludes()
    {
        $config = $this->loader->load(__DIR__.'/fixtures//includes.json');

        self::assertArrayHasKey('services', $config);
        self::assertArraySubset(['test' => ['class' => "Mi\\Guzzle\\Test\\DummyService"]], $config['services']);
        self::assertArraySubset(['dummy' => ['class' => "Mi\\Guzzle\\Test\\TestService"]], $config['services']);
        self::assertArrayNotHasKey('includes', $config);
    }

    /**
     * @test
     */
    public function loadWithIncludesAndDescription()
    {
        $config = $this->loader->load(__DIR__.'/fixtures/includes_desc.json');

        self::assertArrayHasKey('services', $config);
        self::assertArraySubset(['test' => ['class' => "Mi\\Guzzle\\Test\\DummyService"]], $config['services']);
        self::assertArraySubset(['dummy' => ['class' => "Mi\\Guzzle\\Test\\TestService", 'description' => ['name' =>'dummy client']]], $config['services']);
        self::assertArrayNotHasKey('includes', $config);
    }

    protected function setUp()
    {
        $this->loader = new JsonLoader();
    }

}
