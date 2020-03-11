<?php

namespace Utils;

trait CachedLoader
{
    /**
     * Callbacks for keys
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * Key Value pairs for storing state of cached key
     *
     * @var array
     */
    protected $cached_key = [];

    /**
     * Key Value pairs for storing cached data
     *
     * @var array
     */
    protected $cached_data = [];

    /**
     * Set callback for key.
     *
     * @param integer|string $key
     * @param \Closure|callback|mixed $callback
     * @return void
     */
    protected function setCallbackForKey($key, $callback)
    {
        $this->callbacks[$key] = $callback;
    }

    /**
     * Lazy load the data from the callback. If the callback
     * successfully executed(did not throw an exception),
     * it'll return the result from the cache(for same key).
     *
     * @param integer|string $key
     * @param \Closure|callback|null|mixed $callback
     * @return mixed
     */
    protected function cachedLoading($key, $callback = null)
    {
        $callback = $this->callbacks[$key] = $callback ?? $this->callbacks[$key];
        if (isset($this->cached_key[$key])) {
            return $this->cached_data[$key];
        }
        return $this->forceLoad($key, $callback);
    }

    /**
     * Force reload the data, and return the result of the callback.
     *
     * @param integer|string $key
     * @param \Closure|callback|mixed $callback
     * @return mixed
     */
    protected function forceLoad($key, $callback = null)
    {
        $callback = $this->callbacks[$key] = $callback ?? $this->callbacks[$key];
        $data = $this->cached_data[$key] = $callback();
        $this->cached_key[$key] = true;
        return $data;
    }

    /**
     * Set the state of the data as not loaded yet.
     *
     * @param integer|string $key
     * @return void
     */
    protected function resetCachedKey($key)
    {
        unset($this->cached_key[$key]);
    }

    /**
     * Remove callback for the key
     *
     * @param integer|string $key
     * @return void
     */
    protected function resetCallback($key)
    {
        unset($this->callbacks[$key]);
    }
}
