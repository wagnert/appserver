<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\DefaultStatefulSessionBeanSettings
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

use AppserverIo\Storage\GenericStackable;

/**
 * Interface for all session storage implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property string  $sessionFilePrefix            The session file prefix
 * @property string  $sessionSavePath              The default path to persist sessions
 * @property integer $sessionCookieLifetime        The session cookie lifetime
 * @property integer $sessionMaximumAge            The maximum age in seconds, or NULL if none has been defined
 * @property integer $lifetime                     The stateful session bean lifetime
 * @property float   $garbageCollectionProbability The garbage collector probability
 */
class DefaultStatefulSessionBeanSettings extends GenericStackable implements StatefulSessionBeanSettingsInterface
{

    /**
     * The default session prefix.
     *
     * @var string
     */
    const DEFAULT_SESSION_FILE_PREFIX = 'sfsb_';

    /**
     * The default inactivity timeout.
     *
     * @var string
     */
    const DEFAULT_INACTIVITY_TIMEOUT = 1440;

    /**
     * The default probability the garbage collection will be invoked.
     *
     * @var string
     */
    const DEFAULT_GARBAGE_COLLECTION_PROBABILITY = 0.1;

    /**
     * Initialize the default session settings.
     */
    public function __construct()
    {
        // initialize the default values
        $this->setSessionMaximumAge(1440);
        $this->setInactivityTimeout(DefaultStatefulSessionBeanSettings::DEFAULT_INACTIVITY_TIMEOUT);
        $this->setSessionFilePrefix(DefaultStatefulSessionBeanSettings::DEFAULT_SESSION_FILE_PREFIX);
        $this->setGarbageCollectionProbability(DefaultStatefulSessionBeanSettings::DEFAULT_GARBAGE_COLLECTION_PROBABILITY);
    }

    /**
     * Sets the inactivity timeout until the session will be invalidated.
     *
     * @param integer $inactivityTimeout The inactivity timeout in seconds
     *
     * @return void
     */
    public function setInactivityTimeout($inactivityTimeout)
    {
        $this->inactivityTimeout = $inactivityTimeout;
    }

    /**
     * Returns the inactivity timeout until the session will be invalidated.
     *
     * @return integer The inactivity timeout in seconds
     */
    public function getInactivityTimeout()
    {
        return $this->inactivityTimeout;
    }

    /**
     * Sets the probability the garbage collector will be invoked on the session.
     *
     * @param float $garbageCollectionProbability The garbage collector probability
     *
     * @return void
     */
    public function setGarbageCollectionProbability($garbageCollectionProbability)
    {
        $this->garbageCollectionProbability = $garbageCollectionProbability;
    }

    /**
     * Returns the probability the garbage collector will be invoked on the session.
     *
     * @return float The garbage collector probability
     */
    public function getGarbageCollectionProbability()
    {
        return $this->garbageCollectionProbability;
    }

    /**
     * Sets the number of seconds until the session expires, if defined.
     *
     * @param integer $sessionMaximumAge The maximum age in seconds, or NULL if none has been defined.
     *
     * @return void
     */
    public function setSessionMaximumAge($sessionMaximumAge)
    {
        $this->sessionMaximumAge = $sessionMaximumAge;
    }

    /**
     * Returns the number of seconds until the SFSB expires, if defined.
     *
     * @return integer The maximum age in seconds, or NULL if none has been defined.
     */
    public function getSessionMaximumAge()
    {
        return $this->sessionMaximumAge;
    }

    /**
     * Returns the number of seconds until the SFSB expires, if defined.
     *
     * @return integer The maximum age in seconds, or NULL if none has been defined.
     * @deprecated since 1.1.0-beta6
     * @see \AppserverIo\Appserver\PersistenceContainer\DefaultStatefulSessionBeanSettings::getSessionMaximumAge()
     */
    public function getLifetime()
    {
        return $this->getSessionMaximumAge();
    }

    /**
     * Set the session file prefix we use.
     *
     * @param string $sessionFilePrefix The session file prefix
     *
     * @return void
     */
    public function setSessionFilePrefix($sessionFilePrefix)
    {
        $this->sessionFilePrefix = $sessionFilePrefix;
    }

    /**
     * Returns the session file prefix to use.
     *
     * @return string The session file prefix
     */
    public function getSessionFilePrefix()
    {
        return $this->sessionFilePrefix;
    }

    /**
     * Set the default path to persist sessions.
     *
     * @param string $sessionSavePath The default path to persist sessions
     *
     * @return void
     */
    public function setSessionSavePath($sessionSavePath)
    {
        $this->sessionSavePath = $sessionSavePath;
    }

    /**
     * Returns the default path to persist sessions.
     *
     * @return string The default path to persist session
     */
    public function getSessionSavePath()
    {
        return $this->sessionSavePath;
    }

    /**
     * Merge the passed params with the default settings.
     *
     * @param array $params The associative array with the params to merge
     *
     * @return void
     */
    public function mergeWithParams(array $params)
    {
        // merge the passed properties with the default settings for the stateful session beans
        foreach (array_keys(get_object_vars($this)) as $propertyName) {
            if (array_key_exists($propertyName, $params)) {
                $this->$propertyName = $params[$propertyName];
            }
        }
    }
}
