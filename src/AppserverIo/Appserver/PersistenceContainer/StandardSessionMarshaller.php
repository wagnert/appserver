<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\StandardSessionMarshaller
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
 * Marshaller for SFSBs.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardSessionMarshaller implements SessionMarshallerInterface
{

    /**
     * Marshalls the passed SFSB instance.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanWrapperInterface $wrapper The SFSB wrapper we want to marshall
     *
     * @return string The marshalled SFSB representation
     * @see \AppserverIo\Appserver\PersistenceContainer\SessionMarshallerInterface::marshall()
     */
    public function marshall(StatefulSessionBeanWrapperInterface $statefulSessionBean)
    {
        return serialize($statefulSessionBean);
    }

    /**
     * Un-marshals the marshaled SFSB representation.
     *
     * @param string $marshalled The marshaled SFSB representation
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanWrapperInterface The SFSB wrapper instance
     * @see \AppserverIo\Appserver\PersistenceContainer\SessionMarshallerInterface::unmarshall()
     */
    public function unmarshall($marshalled)
    {
        return unserialize($marshalled);
    }
}
