<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\StandardGarbageCollector
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

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The garbage collector for the stateful session beans.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardGarbageCollector extends AbstractDaemonThread
{

    /**
     * Injects the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Injects the SFSB settings.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\StatefullSessionBeanSettingsInterface $statefulSessionBeanSettings Settings for the SFSB handling
     *
     * @return void
     */
    public function injectStatefulSessionBeanSettings($statefulSessionBeanSettings)
    {
        $this->statefulSessionBeanSettings = $statefulSessionBeanSettings;
    }

    /**
     * Injects the session marshaller.
     *
     * @param \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface $sessionMarshaller The session marshaller instance
     *
     * @return void
     */
    public function injectSessionMarshaller($sessionMarshaller)
    {
        $this->sessionMarshaller = $sessionMarshaller;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the session settings.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanSettingsInterface The session settings
     */
    public function getStatefulSessionBeanSettings()
    {
        return $this->statefulSessionBeanSettings;
    }

    /**
     * Returns the session marshaller.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface The session marshaller
     */
    public function getSessionMarshaller()
    {
        return $this->sessionMarshaller;
    }

    /**
     * This method will be invoked before the while() loop starts and can be used
     * to implement some bootstrap functionality.
     *
     * @return void
     */
    public function bootstrap()
    {

        // setup autoloader
        require SERVER_AUTOLOADER;

        // enable garbage collection
        gc_enable();

        // synchronize the application instance and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($this->profileLogger = $this->getApplication()->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $this->profileLogger->appendThreadContext('persistence-container-garbage-collector');
        }
    }

    /**
     * This is invoked on every iteration of the daemons while() loop.
     *
     * @param integer $timeout The timeout before the daemon wakes up
     *
     * @return void
     */
    public function iterate($timeout)
    {

        // call parent method and sleep for the default timeout
        parent::iterate($timeout);

        // collect the SFSBs that timed out
        $this->collectGarbage();
    }

    /**
     * Returns the default path to persist sessions.
     *
     * @param string $toAppend A relative path to append to the session save path
     *
     * @return string The default path to persist session
     */
    public function getSessionSavePath($toAppend = null)
    {
        // load the default path
        $sessionSavePath = $this->getStatefulSessionBeanSettings()->getSessionSavePath();

        // check if we've something to append
        if ($toAppend != null) {
            $sessionSavePath = $sessionSavePath . DIRECTORY_SEPARATOR . $toAppend;
        }

        // return the session save path
        return $sessionSavePath;
    }

    /**
     * Collects the SFSBs that has been timed out
     *
     * @return void
     */
    public function collectGarbage()
    {

        // we need the bean manager that handles all the beans
        /** @var \AppserverIo\Psr\EnterpriseBeans\BeanContextInterface $beanManager */
        $beanManager = $this->getApplication()->search('BeanContextInterface');

        // load the map with the stateful session beans
        /** @var \AppserverIo\Storage\StorageInterface $statefulSessionBeans */
        $statefulSessionBeans = $beanManager->getStatefulSessionBeans();

        // initialize the timestamp with the actual time
        $actualTime = time();

        // prepare the glob to load the session
        $glob = $this->getSessionSavePath($this->getStatefulSessionBeanSettings()->getSessionFilePrefix() . '*');

        // iterate through all session files and initialize them
        foreach (glob($glob) as $pathname) {

            // the requested session file is not a valid file
            if ($this->sessionFileExists($pathname) === false) {
                return;
            }

            // decode the session from the filesystem
            if (($marshalled = file_get_contents($pathname)) === false) {
                throw new SessionDataNotReadableException(sprintf('Can\'t load session data from file %s', $pathname));
            }

            // un-marshall the wrapper for the SFSB instance
            $wrapper = $this->unmarshall($marshalled);

            // check the lifetime of the stateful session beans
            if ($lifetime < $actualTime) {
                // if the stateful session bean has timed out, remove it
                $statefulSessionBeans->remove($identifier, array($beanManager, 'destroyBeanInstance'));
                // write a log message
                $this->getApplication()
                     ->getNamingDirectory()
                     ->search('php:global/log/System')
                     ->debug(sprintf('Successfully removed SFSB %s', $identifier));
                // reduce CPU load
                usleep(1000);
            }
        }

        /*
        // initialize the timestamp with the actual time
        $actualTime = time();

        // iterate over the applications sessions with stateful session beans
        foreach ($statefulSessionBeans->getLifetime() as $identifier => $lifetime) {
            // check the lifetime of the stateful session beans
            if ($lifetime < $actualTime) {
                // if the stateful session bean has timed out, remove it
                $statefulSessionBeans->remove($identifier, array($beanManager, 'destroyBeanInstance'));
                // write a log message
                $this->getApplication()
                     ->getNamingDirectory()
                     ->search('php:global/log/System')
                     ->debug(sprintf('Successfully removed SFSB %s', $identifier));
                // reduce CPU load
                usleep(1000);
            }
        }
        */

        // profile the size of the sessions
        if ($this->profileLogger) {
            $this->profileLogger->debug(
                sprintf('Processed standard garbage collector, handling %d SFSBs', sizeof($statefulSessionBeans))
            );
        }
    }

    /**
     * This is a very basic method to log some stuff by using the error_log() method of PHP.
     *
     * @param mixed  $level   The log level to use
     * @param string $message The message we want to log
     * @param array  $context The context we of the message
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $this->getApplication()->getInitialContext()->getSystemLogger()->log($level, $message, $context);
    }

    /**
     * Initializes the SFSB instance from the passed JSON string and returns it
     *
     * @param string $marshalled The marshaled SFSB representation
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanWrapperInterface $wrapper The SFSB wrapper
     */
    public function unmarshall($marshalled)
    {
        return $this->getSessionMarshaller()->unmarshall($marshalled);
    }

    /**
     * Transforms the passed SFSB into a JSON encoded string and returns it.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanWrapperInterface $wrapper The SFSB to be transformed
     *
     * @return string The marshalled SFSB representation
     */
    public function marshall(StatefulSessionBeanWrapperInterface $wrapper)
    {
        return $this->getSessionMarshaller()->marshall($wrapper);
    }
}
