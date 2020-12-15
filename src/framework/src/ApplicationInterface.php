<?php

namespace Framework;

use Doctrine\ORM\EntityManager;
use Exception;
use Framework\ORM\OrmConfiguration;
use Framework\Utils\Configuration\ConfigurationInterface;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApplicationInterface
 *
 * Interface with underlying framework
 */
interface ApplicationInterface
{
    /**
     * Handles a Request to convert it to a Response.
     *
     * @param Request $request
     *
     * @throws Exception When an Exception occurs during processing
     */
    public function handle(Request $request);

    /**
     * Boots the current kernel.
     */
    public function boot();

    /**
     * Register components to the DI Container
     *
     * @param ContainerBuilder $container
     *
     * @return ContainerBuilder
     */
    public function register(ContainerBuilder $container): ContainerBuilder;

    /**
     * get the configuration of the application
     *
     * @return ConfigurationInterface
     */
    public function getConfig(): ConfigurationInterface;

    /**
     * Gets the Application logger
     *
     * @return Logger
     */
    public function getLogger(): Logger;

    /**
     * Gets the project dir (path of the project's composer file).
     *
     * @return string
     */
    public function getProjectDir(): string;

    /**
     * Load OrmConfiguration which is an Object
     * that contains configuration information
     * of the ORM
     *
     * @return OrmConfiguration
     */
    public function loadOrmConfig(): OrmConfiguration;

    /**
     * access the primary database connection
     *
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager;

    /**
     * Get a list of the current application version(s)
     * - app
     * - base
     * - ops
     *
     * @return array
     */
    public function getVersion(): array;
}