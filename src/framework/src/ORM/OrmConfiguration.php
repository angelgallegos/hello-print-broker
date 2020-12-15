<?php

namespace Framework\ORM;

use __\__;
use Doctrine\Common\Cache\Cache;

class OrmConfiguration
{
    /**
     * @var array
     */
    private array $mapped;

    /**
     * @var bool
     */
    private bool $isDevMode = false;

    /**
     * @var bool
     */
    private bool $useSimpleAnnotationReader = false;

    /**
     * @var string|null
     */
    private ?string $proxyDir = null;

    /**
     * @var Cache|null
     */
    private ?Cache $cache = null;

    /**
     * OrmConfiguration constructor.
     * @param array $mapped
     * @param bool $isDevMode
     * @param bool $useSimpleAnnotationReader
     * @param string|null $proxyDir
     * @param Cache|null $cache
     */
    public function __construct(
        array $mapped,
        bool $isDevMode,
        bool $useSimpleAnnotationReader,
        ?string $proxyDir,
        ?Cache $cache
    ) {
        $this->mapped = $mapped;
        $this->isDevMode = $isDevMode;
        $this->useSimpleAnnotationReader = $useSimpleAnnotationReader;
        $this->proxyDir = $proxyDir;
        $this->cache = $cache;
    }

    /**
     * @return bool
     */
    public function isDevMode(): bool
    {
        return $this->isDevMode;
    }

    /**
     * @return string|null
     */
    public function getProxyDir(): ?string
    {
        return $this->proxyDir;
    }

    /**
     * @return Cache|null
     */
    public function getCache(): ?Cache
    {
        return $this->cache;
    }

    /**
     * @return bool
     */
    public function isUseSimpleAnnotationReader(): bool
    {
        return $this->useSimpleAnnotationReader;
    }

    /**
     * @return array
     */
    public function getMapped(): array
    {
        return $this->mapped;
    }

    /**
     * @return array
     */
    public function getPaths(): array
    {
        return array_values($this->mapped);
    }

    /**
     * @param array  $data
     * @param string $basePath
     *
     * @return OrmConfiguration
     */
    public static function create(
        array $data,
        string $basePath = ""
    ): self {
        $mappings = __::get($data, 'doctrine.orm.mappings');
        $paths = [];
        foreach ($mappings as $key => $map) {
            $paths[$key] = str_replace('%app.project_dir%', $basePath, __::get($map,'dir'));
        }

        $isDevMode = __::get($data, "doctrine.orm.isDevMode", false);
        $useSimpleAnnotationReader = __::get($data, "doctrine.orm.useSimpleAnnotationReader", false);
        $proxyDir = __::get($data, "doctrine.orm.proxyDir", null);

        return new self($paths, $isDevMode, $useSimpleAnnotationReader, $proxyDir, null);
    }
}