<?php
namespace Framework;

use Cekurte\Environment\Environment;
use Cekurte\Environment\Exception\FilterException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Exception;
use Framework\Utils\Configuration\ArrayConfiguration;
use Framework\Utils\Configuration\ConfigurationInterface;
use LogicException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use ReflectionObject;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use function dirname;

abstract class Application implements ApplicationInterface
{
    /**
     * @var bool
     */
    protected bool $booted = false;

    /**
     * @var string|null
     */
    private ?string $projectDir = null;

    /**
     * @var ContainerInterface|ContainerBuilder|null
     */
    private ?ContainerBuilder $container = null;

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var ArrayConfiguration|ConfigurationInterface|null
     */
    private ?ConfigurationInterface $config;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * Application constructor.
     * @throws FilterException
     */
    public function __construct() {
        $this->config = new ArrayConfiguration(
            Environment::getAll()
        );

        if (!empty($this->config->get('APP_TIMEZONE'))) {
            date_default_timezone_set($this->config->get('APP_TIMEZONE', 'UTC'));
        }
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function handle(Request $request)
    {
        if (!$this->booted) {
            $container = $this->container ?? $this->preBoot();

            if ($container->has('http_cache')) {
                return $container->get('http_cache')->handle();
            }
        }

        $this->boot();

        return $this->getHttpHandler()->handle($request);
    }

    /**
     * Gets a HTTP handler from the container.
     * @throws Exception
     */
    protected function getHttpHandler()
    {
        return $this->container->get('http_handler');
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function boot()
    {
        if (true === $this->booted) {
            return;
        }

        if (null === $this->container) {
            $this->preBoot();
        }

        $this->booted = true;
    }

    /**
     * @return ContainerBuilder|null
     * @throws Exception
     */
    private function preBoot(): ?ContainerBuilder
    {
        $this->initializeLogger();

        $this->initializeEntityManager();

        $this->initializeContainer();

        return $this->container;
    }

    /**
     * @throws ORMException
     */
    protected function initializeEntityManager()
    {
        $ormConfig = $this->loadOrmConfig();

        $config = Setup::createAnnotationMetadataConfiguration(
            $ormConfig->getPaths(),
            $ormConfig->isDevMode(),
            $ormConfig->getProxyDir(),
            $ormConfig->getCache(),
            $ormConfig->isUseSimpleAnnotationReader()
        );

        // database configuration parameters
        $conn = [
            'driver'   => $this->config->get("DB_CONNECTION"),
            'user'     => $this->config->get("DB_USERNAME"),
            'password' => $this->config->get("DB_PASSWORD"),
            'dbname'   => $this->config->get("DB_DATABASE"),
            'host'     => $this->config->get("DB_HOST"),
            'port'     => $this->config->get("DB_PORT")
        ];

        // obtaining the entity manager
        $this->entityManager = EntityManager::create($conn, $config);

    }

    /**
     * @inheritDoc
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * Initializes the service container.
     *
     * The built version of the service container is used when fresh, otherwise the
     * container is built.
     * @throws Exception
     */
    protected function initializeContainer()
    {
        $container = null;
        $container = $this->buildContainer();
        $container->compile();
        $this->container = $container;
        $this->container->set('kernel', $this);
    }

    /**
     * Builds the service container.
     *
     * @return ContainerBuilder The compiled service container
     *
     * @throws RuntimeException
     * @throws Exception
     */
    protected function buildContainer()
    {
        $container = $this->getContainerBuilder();
        $container->addObjectResource($this);
        $this->prepareContainer($container);

        return $container;
    }


    /**
     * Gets a new ContainerBuilder instance used to build the service container.
     *
     * @return ContainerBuilder
     */
    protected function getContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    /**
     * Prepares the ContainerBuilder before it is compiled.
     *
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    protected function prepareContainer(ContainerBuilder $container)
    {
        $container = $this->register($container);

        $this->build($container);
    }

    /**
     * @inheritDoc
     */
    public function register(ContainerBuilder $container): ContainerBuilder
    {
        $container->set(
            'orm.entity.manager',
            $this->getEntityManager()
        );

        $container->set('configs', $this->getConfig());

        $container->set('logger', $this->getLogger());

        return $container;
    }

    /**
     * The extension point similar to the Bundle::build() method.
     *
     * Use this method to register compiler passes and manipulate the container during the building process.
     *
     * @param ContainerBuilder $container
     */
    protected function build(ContainerBuilder $container)
    {
    }

    /**
     * Initializes the Logger
     */
    protected function initializeLogger()
    {
        $this->logger = new Logger('broker_logger');
        $this->logger->pushHandler(new StreamHandler('php://stdout'));
    }

    /**
     * @inheritDoc
     */
    public function getProjectDir(): string
    {
        if (null === $this->projectDir) {
            $r = new ReflectionObject($this);

            if (!is_file($dir = $r->getFileName())) {
                throw new LogicException(sprintf('Cannot auto-detect project dir for Application of class "%s".', $r->name));
            }

            $dir = $rootDir = dirname($dir);
            while (!is_file($dir.'/composer.json')) {
                if ($dir === dirname($dir)) {
                    return $this->projectDir = $rootDir;
                }
                $dir = dirname($dir);
            }
            $this->projectDir = $dir;
        }

        return $this->projectDir;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): ConfigurationInterface
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): array
    {
        return [
            'APP_VERSION' => $this->config->get('APP_VERSION'),
            'BASE_VERSION' => $this->config->get('BASE_VERSION'),
            'OPS_VERSION' => $this->config->get('OPS_VERSION'),
        ];
    }
}