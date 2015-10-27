<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanWrapper
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

/**
 * A wrapper to handle SFSB persistence.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StatefulSessionBeanWrapper implements StatefulSessionBeanWrapperInterface
{

    /**
     * The session ID the SFSB is bound to.
     *
     * @var string
     */
    protected $id = null;

    /**
     * The SFSB type.
     *
     * @var string
     */
    protected $type = null;

    /**
     * The SFSB instance.
     *
     * @var object
     */
    protected $data = null;

    /**
     * Initialize the wrapper with the session ID,  if passed.
     *
     * @param string $id The session ID to initialize the wrapper with
     */
    public function __construct($id = null)
    {
        if ($id != null) {
            $this->setId($id);
        }
    }

    /**
     * Set's the session ID the SFSB is bound to.
     *
     * @param string $id The session ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Return's the session ID the SFSB is bound to.
     *
     * @return string The session ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set's the SFSB instance.
     *
     * @param object $data The SFSB instance
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Return's the SFSB instance.
     *
     * @return object The SFSB instance
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set's the SFSB type.
     *
     * @param object $data The SFSB type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Return's the SFSB type.
     *
     * @return object The SFSB type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return's the checksum for the SFSB instance.
     *
     * @return string The checksum
     */
    public function checksum()
    {
        return md5($this->id . serialize($this->data));
    }

    /**
     * Creates a new instance of th SFSB, initializes it with the
     * data found in the wrapper and returns it.
     *
     * @return object The SFSB instance
     */
    public function toStatefulSessionBean()
    {

        // create the SFSB instance
        $className = $this->getType();
        $instance = new $className();

        // create a reflection object from the SFSB instance
        $reflectionObject = new \ReflectionObject($instance);

        // append the SFSB properties from the wrapper
        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            // query whether we've a value for the property or not
            if (isset($this->data[$reflectionProperty->getName()])) {
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($instance, $this->data[$reflectionProperty->getName()]);
            }
        }

        // return the initialized SFSB instance
        return $instance;
    }

    /**
     * Initializes the wrapper with the data from the passed SFSB instance.
     *
     * @param object $statefulSessionBean The SFSB instance
     *
     * @return void
     */
    public function fromStatefulSessionBean($statefulSessionBean)
    {

        // initialize the class name
        $this->setType(get_class($statefulSessionBean));

        // create a reflection object
        $reflectionObject = new \ReflectionObject($statefulSessionBean);

        // load the SFSB properties that has to be persisted
        foreach ($reflectionObject->getProperties() as $reflectionProperty) {

            // we need to make the property accessible
            $reflectionProperty->setAccessible(true);
            $value = $reflectionProperty->getValue($statefulSessionBean);

            // we can't serialize instances of \Thread
            if ($value instanceof \Thread) {
                continue;
            }

            // we can't serialize instances of \Stackable
            if ($value instanceof \Stackable) {
                continue;
            }

            // we want to serialize all other properties
            $this->data[$reflectionProperty->getName()] = $value;
        }
    }
}
