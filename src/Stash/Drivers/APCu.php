<?php

namespace Stash\Drivers;

class APCu extends Driver
{
    /**
     * Put an item into the cache for a specified duration
     *
     * @param  string $key     Unique item identifier
     * @param  mixed  $data    Data to cache
     * @param  int    $minutes Time in minutes until item expires
     *
     * @return bool            True on sucess, otherwise false
     */
    public function put($key, $data, $minutes = 0)
    {
        return apcu_store($this->prefix($key), $data, ($minutes * 60));
    }

    /**
     * Put an item into the cache permanently
     *
     * @param  string $key  Unique identifier
     * @param  mixed  $data Data to cache
     *
     * @return bool         True on sucess, otherwise false
     */
    public function forever($key, $data)
    {
        return $this->put($key, $data);
    }

    /**
     * Get an item from the cache
     *
     * @param  string $key     Uniqe item identifier
     * @param  mixex  $default Default data to return
     *
     * @return mixed           Cached data or $default value
     */
    public function get($key, $default = false)
    {
        return apcu_fetch($this->prefix($key)) ?: $default;
    }

    /**
     * Check if an item exists in the cache
     *
     * @param  string $key Unique item identifier
     *
     * @return bool        True if item exists, otherwise false
     */
    public function has($key)
    {
        return apcu_exists($this->prefix($key));
    }

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results for a
     * specified duration
     *
     * @param  string $key     Unique item identifier
     * @param  int    $minutes Time in minutes until item expires
     * @param  mixed  $closure Anonymous closure function
     *
     * @return mixed           Cached data or $closure results
     */
    public function remember($key, $minutes, \Closure $closure)
    {
        if ($this->has($key)) return $this->get($key);

        $data = $closure();

        return $this->put($key, $data, $minutes) ? $data : false;
    }

    /**
     * Retrieve item from cache or, when item does not exist, execute the
     * provided closure and return and store the returned results permanently
     *
     * @param  string $key     Unique item identifier
     * @param  mixed  $closure Anonymous closure function
     *
     * @return mixed           Cached data or $closure results
     */
    public function rememberForever($key, \Closure $closure)
    {
        return $this->remember($key, 0, $closure);
    }

    /**
     * Increase the value of a stored integer
     *
     * @param  string $key   Unique item identifier
     * @param  int    $value The ammount by which to increment
     *
     * @return mixed         Item's new value on success, otherwise false
     */
    public function increment($key, $value = 1)
    {
        // Check for key existance first as a temporary workaround
        // for this bug: https://github.com/krakjoe/apcu/issues/183
        if (apcu_exists($this->prefix($key))) {
            return apcu_inc($this->prefix($key), $value, $result);
        }

        return false;
    }

    /**
     * Decrease the value of a stored integer
     *
     * @param  string $key   Unique item identifier
     * @param  int    $value The ammount by which to decrement
     *
     * @return mixed         Item's new value on success, otherwise false
     */
    public function decrement($key, $value = 1)
    {
        // Check for key existance first as a temporary workaround
        // for this bug: https://github.com/krakjoe/apcu/issues/183
        if (apcu_exists($this->prefix($key))) {
            return apcu_dec($this->prefix($key), $value);
        }

        return false;
    }

    /**
     * Removes an item from the cache
     *
     * @param  string $key Unique item identifier
     *
     * @return bool        True on success, otherwise false
     */
    public function forget($key)
    {
        return apcu_delete($this->prefix($key));
    }

    /**
     * Remove all items from the cache
     *
     * @return bool True on success, otherwise false
     */
    public function flush()
    {
        return apcu_clear_cache();
    }
}