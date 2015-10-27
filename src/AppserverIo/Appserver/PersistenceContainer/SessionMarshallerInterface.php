<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\SessionMarshallerInterface
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
 * Interface for all SFSB marshaller implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SessionMarshallerInterface
{

    /**
     * Marshalls the passed SFSB instance.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanWrapperInterface $wrapper The SFSB we want to marshall
     *
     * @return string The marshalled SFSB representation
     */
    public function marshall(StatefulSessionBeanWrapperInterface $wrapper);

    /**
     * Un-marshals the marshaled SFSB representation.
     *
     * @param string $marshalled The marshaled SFSB representation
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanWrapperInterface The SFSB wrapper instance
     */
    public function unmarshall($marshalled);
}
