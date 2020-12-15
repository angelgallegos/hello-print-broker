<?php

namespace App;

use Exception;
use Framework\Application as BaseApplication;
use Framework\Http\Handler;
use Framework\ORM\OrmConfiguration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class Application extends BaseApplication
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function register(ContainerBuilder $container): ContainerBuilder
    {
        $container = parent::register($container);

        if (is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $fileLocator = new FileLocator(__DIR__ . '/../config');
            $loader = new YamlFileLoader($container, $fileLocator);
            $loader->load('services.yaml');
        }

        $container->set("http_handler", new Handler($container));

        return $container;
    }

    /**
     * @inheritDoc
     */
    public function loadOrmConfig(): OrmConfiguration
    {
        return OrmConfiguration::create(
            Yaml::parseFile(__DIR__ . '/../config/packages/doctrine.yaml'),
            $this->getProjectDir()
        );
    }
}