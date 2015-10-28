<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\FilesystemPersistenceManager
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
use AppserverIo\Collections\MapInterface;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A thread which pre-initializes SFSB instances and adds them to the
 * the session pool.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property array                                                                            $loggers                     The logger instances
 * @property \AppserverIo\Storage\StorageInterface                                            $checksums                   The checksums
 * @property \AppserverIo\Psr\Application\ApplicationInterface                                $application                 The application instance
 * @property \AppserverIo\Appserver\PersistenceContainer\SessionMarshallerInterface           $sessionMarshaller           The session marshaller instance
 * @property \AppserverIo\Collection\MapInterface                                             $statefulSessionBeans        The sessions
 * @property \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanSettingsInterface $statefulSessionBeanSettings Settings for the session handling
 * @property string                                                                           $user                        The user used for file system interactions
 * @property string                                                                           $group                       The user group used for file system interactions
 * @property string                                                                           $umask                       The umask used for file system interactions
 */
class FilesystemPersistenceManager extends AbstractDaemonThread implements PersistenceManagerInterface
{

    /**
     * Initializes the session persistence manager.
     */
    public function __construct()
    {

        // initialize the class members
        $this->checksums = null;
        $this->application = null;
        $this->sessionMarshaller = null;
        $this->statefulSessionBeans = null;
        $this->statefulSessionBeanSettings = null;

        // initialize the class members with default values
        $this->user = 'nobody';
        $this->group = 'nobody';
        $this->umask = 0002;
    }

    /**
     * Injects the available logger instances.
     *
     * @param array $loggers The logger instances
     *
     * @return void
     */
    public function injectLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    /**
     * Injects the checksums.
     *
     * @param \AppserverIo\Storage\StorageInterface $checksums The checksums
     *
     * @return void
     */
    public function injectChecksums($checksums)
    {
        $this->checksums = $checksums;
    }

    /**
     * Injects the sessions.
     *
     * @param \AppserverIo\Collection\MapInterface $statefulSessionBeans The sessions
     *
     * @return void
     */
    public function injectStatefulSessionBeans($statefulSessionBeans)
    {
        $this->statefulSessionBeans = $statefulSessionBeans;
    }

    /**
     * Injects the session settings.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\StatefullSessionBeanSettingsInterface $statefulSessionBeanSettings Settings for the session handling
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
     * Injects the user.
     *
     * @param string $user The user
     *
     * @return void
     */
    public function injectUser($user)
    {
        $this->user = $user;
    }

    /**
     * Injects the group.
     *
     * @param string $group The group
     *
     * @return void
     */
    public function injectGroup($group)
    {
        $this->group = $group;
    }

    /**
     * Injects the umask.
     *
     * @param integer $umask The umask
     *
     * @return void
     */
    public function injectUmask($umask)
    {
        $this->umask = $umask;
    }

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
     * Returns the session checksum storage to watch changed sessions.
     *
     * @return \AppserverIo\Storage\StorageInterface The session checksum storage
     */
    public function getChecksums()
    {
        return $this->checksums;
    }

    /**
     * Returns all SFSBs actually attached to the session manager.
     *
     * @return \AppserverIo\Collection\MapInterface The container with the SFSBs
     */
    public function getStatefulSessionBeans()
    {
        return $this->statefulSessionBeans;
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
     * Returns the system user.
     *
     * @return string The system user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the system group.
     *
     * @return string The system user
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Returns the preferred umask.
     *
     * @return integer The preferred umask
     */
    public function getUmask()
    {
        return $this->umask;
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
     * This method will be invoked before the while() loop starts and can be used
     * to implement some bootstrap functionality.
     *
     * @return void
     */
    public function bootstrap()
    {

        // setup autoloader
        require SERVER_AUTOLOADER;

        // synchronize the application instance and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // try to load the profile logger
        if (isset($this->loggers[LoggerUtils::PROFILE])) {
            $this->profileLogger = $this->loggers[LoggerUtils::PROFILE];
            $this->profileLogger->appendThreadContext('persistence-container-filesystem-persistence-manager');
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

        // now persist inactive sessions
        $this->persist();

        // profile the size of the sessions
        if ($this->profileLogger) {
            $this->profileLogger->debug(
                sprintf('Persisted SFSBs to filesystem with size: %d', sizeof($this->getStatefulSessionBeans()))
            );
        }
    }

    /**
     * This method will be invoked by the engine after the
     * servlet has been serviced.
     *
     * @return void
     */
    public function persist()
    {

        // we want to know what inactivity timeout we've to check the sessions for
        $inactivityTimeout = $this->getStatefulSessionBeanSettings()->getInactivityTimeout();

        // load the lifetime in seconds from the SFSB settings
        $lifetime = $this->getStatefulSessionBeanSettings()->getLifetime();

        // iterate over all the checksums (session that are active and loaded)
        foreach ($this->getStatefulSessionBeans()->getAllKeys() as $id) {
            // load the SFSB instance
            $statefulSessionBean = $this->getStatefulSessionBeans()->get($id);

            // query whether we've a SFSB or not
            if ($statefulSessionBean == null) {
                continue;
            }

            // if we don't have a checksum, this is a new session
            $checksum = null;
            if ($this->getChecksums()->has($id)) {
                $checksum = $this->getChecksums()->get($id);
            }

            // create a new session wrapper
            $wrapper = new StatefulSessionBeanWrapper($id);
            $wrapper->fromStatefulSessionBean($statefulSessionBean);

            // load the SFSB's last activity timestamp
            $lastActivitySecondsAgo = ($this->getStatefulSessionBeans()->getLifetime($id) - $lifetime) - time();

            // if the SFSB doesn't change
            if ($checksum === $wrapper->checksum() && $lastActivitySecondsAgo < $inactivityTimeout) {
                continue;
            }

            // we want to detach the session (to free memory), when the last activity is > the inactivity timeout (1440 by default)
            if ($checksum === $wrapper->checksum()  && $lastActivitySecondsAgo > $inactivityTimeout) {
                // prepare the session filename
                $sessionFilename = $this->getSessionSavePath($this->getStatefulSessionBeanSettings()->getSessionFilePrefix() . $id);

                // update the checksum and the file that stores the session data
                file_put_contents($sessionFilename, $this->marshall($wrapper));

                // remove the session instance from the session manager
                $this->getChecksums()->remove($id);
                $this->getStatefulSessionBeans()->remove($id);
                continue;
            }

            // we want to persist the session because its data has been changed
            if ($checksum !== $wrapper->checksum()) {
                // prepare the session filename
                $sessionFilename = $this->getSessionSavePath($this->getStatefulSessionBeanSettings()->getSessionFilePrefix() . $id);

                // update the checksum and the file that stores the session data
                file_put_contents($sessionFilename, $this->marshall($wrapper));
                $this->getChecksums()->set($id, $wrapper->checksum());
                continue;
            }
        }
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
     * Initializes the session manager instance and unpersists the all sessions that has
     * been used during the time defined with the last inactivity timeout defined in the
     * session configuration.
     *
     * If the session data could not be loaded, because the files data is corrupt, the
     * file with the session data will be deleted.
     *
     * @return void
     */
    public function initialize()
    {

        // prepare the glob to load the session
        $glob = $this->getSessionSavePath($this->getStatefulSessionBeanSettings()->getSessionFilePrefix() . '*');

        // we want to filter the session we initialize on server start
        $sessionFilter = new SessionFilter(new \GlobIterator($glob), $this->getStatefulSessionBeanSettings()->getInactivityTimeout());

        // iterate through all session files and initialize them
        foreach ($sessionFilter as $sessionFile) {
            try {
                // unpersist the session data itself
                $this->loadSessionFromFile($sessionFile->getPathname());

            } catch (SessionDataNotReadableException $sdnre) {
                // this maybe happens when the session file is corrupt
                $this->removeSessionFile($sessionFile->getPathname());
            }
        }
    }

    /**
     * Unpersists the session with the passed ID from the persistence layer and
     * reattaches it to the internal session storage.
     *
     * @param string $id The ID of the session we want to unpersist
     *
     * @return void
     */
    protected function unpersist($id)
    {

        try {
            // try to load the session with the passed ID
            if ($this->getStatefulSessionBeans()->exists($id) === false) {
                // prepare the pathname to the file containing the session data
                $filename = $this->getStatefulSessionBeanSettings()->getSessionFilePrefix() . $id;
                $pathname = $this->getSessionSavePath($filename);

                // unpersist the session data itself
                $this->loadSessionFromFile($pathname);
            }

        } catch (SessionDataNotReadableException $sdnre) {
            // this maybe happens when the session file is corrupt
            $this->removeSessionFile($pathname);
        }
    }

    /**
     * Checks if a file with the passed name containing session data exists.
     *
     * @param string $pathname The path of the file to check
     *
     * @return boolean TRUE if the file exists, else FALSE
     */
    public function sessionFileExists($pathname)
    {
        return file_exists($pathname);
    }

    /**
     * Removes the session file with the passed name containing session data.
     *
     * @param string $pathname The path of the file to remove
     *
     * @return boolean TRUE if the file has successfully been removed, else FALSE
     */
    public function removeSessionFile($pathname)
    {
        if (file_exists($pathname)) {
            return unlink($pathname);
        }
        return false;
    }

    /**
     * Tries to load the session data from the passed filename.
     *
     * @param string $pathname The path of the file to load the session data from
     *
     * @return void
     * @throws \AppserverIo\Appserver\ServletEngine\SessionDataNotReadableException Is thrown if the file containing the session data is not readable
     */
    public function loadSessionFromFile($pathname)
    {

        // the requested session file is not a valid file
        if ($this->sessionFileExists($pathname) === false) {
            return;
        }

        // decode the session from the filesystem
        if (($marshalled = file_get_contents($pathname)) === false) {
            throw new SessionDataNotReadableException(sprintf('Can\'t load session data from file %s', $pathname));
        }

        // create a new session instance from the marshaled object representation
        $wrapper = $this->unmarshall($marshalled);

        // load session ID and checksum
        $id = $wrapper->getId();
        $checksum = $wrapper->checksum();

        // add the sessions checksum
        $this->getChecksums()->set($id, $checksum);

        // add the session to the sessions
        $this->getStatefulSessionBeans()->add($id, $wrapper->toStatefulSessionBean());
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
