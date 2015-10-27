<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanSettingsInterface
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
 * Interface for the SFSB settings.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface StatefulSessionBeanSettingsInterface
{

    /**
     * Returns the number of seconds until the session expires, if defined.
     *
     * @return integer The maximum age in seconds, or NULL if none has been defined.
     */
    public function getSessionMaximumAge();

    /**
     * Returns the session file prefix to use.
     *
     * @return string The session file prefix
     */
    public function getSessionFilePrefix();

    /**
     * Returns the default path to persist sessions.
     *
     * @return string The default path to persist session
     */
    public function getSessionSavePath();

    /**
     * Returns the number of seconds for a stateful session bean lifetime.
     *
     * @return integer The stateful session bean lifetime
     */
    public function getLifetime();

    /**
     * Returns the probability the garbage collector will be invoked on the session.
     *
     * @return float The garbage collector probability
     */
    public function getGarbageCollectionProbability();
}
