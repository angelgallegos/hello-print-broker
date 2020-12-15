<?php

namespace Framework\Utils\Configuration;

/**
 *
 */
interface ConfigurationInterface
{
    /**
     * Return config value
     *
     * @param string              $key get a certain configuration key
     * @param null|mixed|callable $default resolve a default value
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

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
    public function getUsingFlag(string $flag, string $key, $default = null);

    /**
     * Has a configuration key been set
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key): bool;

    /**
     * Get the entire configuration
     *
     * @return array
     */
    public function all(): array;
}