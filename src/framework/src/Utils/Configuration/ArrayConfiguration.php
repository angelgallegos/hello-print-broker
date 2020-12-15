<?php

namespace Framework\Utils\Configuration;

use __\__;

/**
 * Class ArrayConfig
 *
 * Simple array representation of a config
 */
class ArrayConfiguration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private array $config;

    /**
     * ArrayConfig constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * return config value
     *
     * @param string              $key get a certain configuration key
     * @param null|mixed|callable $default resolve a default value
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return __::get($this->config, $key, $default);
    }

    /**
     * get config based on a configuration flag, when the flag key is true
     * the desired config is read. when the flag is false or undefined
     * the default logic will be applied
     *
     * @param string              $flag    the flag we want to check
     * @param string              $key     get a certain configuration key
     * @param null|mixed|callable $default resolve a default value
     *
     * @return mixed
     */
    public function getUsingFlag(string $flag, string $key, $default = null)
    {
        $flag = $this->get($flag);
        if(!$flag || null === $flag){
            if(__::isFunction($default)){
                return $default();
            }

            return $default;
        }
        return $this->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        return __::has($this->config, $key);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->config;
    }
}