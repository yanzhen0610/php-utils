<?php

namespace Utils;

trait LazyLoading
{
    /**
     * Key Value pairs for storing state of lazy loading
     *
     * @var array
     */
    protected $lazyLoadingLoaded = [];

    /**
     * Key Value pairs for storing lazy loaded data
     *
     * @var array
     */
    protected $lazyLoadingData = [];

    /**
     * Lazy load the data from the callback. If the callback
     * successfully executed(did not throw an exception),
     * it'll return the result from the cache(for same key).
     *
     * @param integer|string $key
     * @param \Closure|callback|mixed $callback
     * @return mixed
     */
    protected function lazyLoad($key, $callback)
    {
        if (isset($this->lazyLoadingLoaded[$key])) {
            return $this->lazyLoadingData[$key];
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
    protected function forceLoad($key, $callback)
    {
        $data = $this->lazyLoadingData[$key] = $callback();
        $this->lazyLoadingLoaded[$key] = true;
        return $data;
    }

    /**
     * Set the state of the data as not loaded yet.
     *
     * @param integer|string $key
     * @return void
     */
    protected function resetLoadingKey($key)
    {
        unset($this->lazyLoadingLoaded[$key]);
    }
}
