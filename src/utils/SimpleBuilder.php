<?php

namespace Utils;

class SimpleBuilder
{
    use CachedLoader;

    /** @var string */
    protected $class;

    /** @var array|null */
    protected $construct_params = [];

    /** @var array */
    protected $options = [];

    /**
     * @param string $class
     * @param mixed ...$construct_params
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Set constructor parameters
     *
     * @param array|null $construct_params
     * @return SimpleBuilder
     */
    public function constructorParams(?array $construct_params = null)
    {
        $this->construct_params = $construct_params;
        return $this;
    }

    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == 'set') {
            $property = Helper::snake(substr($method, 3));

            return static::set($property, $args[0]);
        }
        return static::setter($method, ...$args);
    }

    /**
     * Properties setter
     *
     * @param string $property
     * @param mixed $value
     * @return Builder
     */
    public function set($property, $value)
    {
        $this->options[] = [
            'type' => 'property',
            'key' => $property,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * Setter caller
     *
     * @param string $property
     * @param mixed $value
     * @return Builder
     */
    public function setter($setter, ...$args)
    {
        $this->options[] = [
            'type' => 'setter',
            'key' => $setter,
            'value' => ($args),
        ];
        return $this;
    }

    /**
     * Build the instance of the given class
     *
     * @param mixed ...$construct_params
     * @return mixed
     */
    public function build(...$construct_params)
    {
        $instance = new $this->class(...(count($construct_params) ? $construct_params : $this->construct_params));
        foreach ($this->options as $option) {
            $key = $option['key'];
            $value = $option['value'];
            if ($option['type'] == 'property') {
                $instance->$key = $value;
            } else {
                $instance->$key(...$value);
            }
        }
        return $instance;
    }

    /**
     * Get the factory of the builder. (It simply wrapped the
     * `build()` method as a closure.)
     *
     * @return \Closure
     */
    public function getFactory()
    {
        return $this->cachedLoading('factory', function () {
            return function (...$construct_params) {
                return $this->build(...$construct_params);
            };
        });
    }
}
