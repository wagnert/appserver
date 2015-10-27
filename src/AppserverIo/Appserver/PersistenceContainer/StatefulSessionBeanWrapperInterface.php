<?php
/**
 * \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanWrapperInterface
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
 * Interface for SFSB wrappers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface StatefulSessionBeanWrapperInterface
{

    /**
     * Return's the session ID the SFSB is bound to.
     *
     * @return string The session ID
     */
    public function getId();

    /**
     * Return's the SFSB instance.
     *
     * @return object The SFSB instance
     */
    public function getData();

    /**
     * Return's the checksum for the SFSB instance.
     *
     * @return string The checksum
     */
    public function checksum();
}
