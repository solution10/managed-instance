<?php

namespace Solution10\ManagedInstance;

/**
 * Trait ManagedInstance
 *
 * Adding this trait will allow your classes to manage instances of themselves, without
 * dumping everything globally or in DI containers.
 *
 * @package Solution10\ManagedInstance
 */
trait ManagedInstance
{
    /**
     * @var     ManagedInstance[]
     */
    protected static $instances = [];

    /**
     * @var     string
     */
    protected $instanceName = 'default';

    /**
     * Returns an instance of this object, by a given name (null for default). If one doesn't exist, it will
     * create one for you using instanceFactory()
     *
     * @param   string|null  Either the instance by a given name, or the default one for singletons.
     * @return  self
     * @throws  Exception\InstanceException
     */
    public static function instance($name = null)
    {
        if ($name === null) {
            $name = 'default';
        }

        if (!array_key_exists($name, self::$instances)) {
            throw new Exception\InstanceException(
                'Unknown instance "'.$name.'"',
                Exception\InstanceException::UNKNOWN_INSTANCE
            );
        }

        return self::$instances[$name];
    }

    /**
     * Returns all instances associated with this object in a name => instance map.
     *
     * @return  array
     */
    public static function instances()
    {
        return self::$instances;
    }

    /**
     * Clears out all of the instances in the manager
     */
    public static function clearInstances()
    {
        self::$instances = [];
    }

    /**
     * Get/Set the name of this instance
     *
     * @param   string|null     $name   String to set, null to get
     * @return  $this|string
     */
    public function instanceName($name = null)
    {
        if ($name === null) {
            return $this->instanceName;
        }
        $this->instanceName = $name;
        return $this;
    }

    /**
     * This function manually registers an instance of this object with the management routines.
     * Helpful if you have hand-cranked this object by calling construct explicitly.
     *
     * @param   string  $name   Name to use, null for 'default'
     * @return  $this
     */
    public function registerInstance($name = null)
    {
        if ($name === null) {
            $name = 'default';
        }
        self::$instances[$name] = $this;
        $this->instanceName($name);
        return $this;
    }

    /**
     * Unregisters an instance with the manager.
     *
     * @return  $this
     */
    public function unregisterInstance()
    {
        if (array_key_exists($this->instanceName, self::$instances)) {
            unset(self::$instances[$this->instanceName]);
        }
        return $this;
    }
}