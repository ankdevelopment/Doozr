<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Cache - Module - Container - Memcache
 *
 * Memcache.php - Memcache-Container of the Caching Module.
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2013, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgement: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: fbaf3ff85a82d6335ede132758b0144dd5556599 $
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 */

require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/Cache/Module/Container.php';
require_once DOOZR_DOCUMENT_ROOT.'Module/DoozR/Cache/Module/Container/Interface.php';

/**
 * DoozR - Cache - Module - Container - Memcache
 *
 * Memcache-Container of the Caching Module.
 *
 * @category   DoozR
 * @package    DoozR_Module
 * @subpackage DoozR_Module_Cache
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: fbaf3ff85a82d6335ede132758b0144dd5556599 $
 * @link       http://clickalicious.github.com/DoozR/
 * @see        -
 * @since      -
 * @DoozRType  Multiple
 */
class DoozR_Cache_Module_Container_Memcache extends DoozR_Cache_Module_Container
implements DoozR_Cache_Module_Container_Interface
{
    /**
     * contains the hostname for the connection
     *
     * @var string
     * @access protected
     */
    protected $hostname = '127.0.0.1';

    /**
     * contains the port for the connection
     *
     * @var string
     * @access protected
     */
    protected $port = '11211';

    /**
     * contains the memcache instance (connection)
     *
     * @var object
     * @access private
     */
    private $_memcache;

    /**
     * TRUE  to compress content with zlib from memcache
     * FALSE to store content uncompressed
     *
     * @var boolean
     * @access private
     */
    private $_compress = false;

    /**
     * the allowed options specific for this container
     *
     * @var array
     * @access protected
     */
    protected $thisContainerAllowedOptions = array(
        'hostname',
        'port'
    );

    /**
     * the allowed options specific for this container
     *
     * @var array
     * @access protected
     */
    const UNIQUE_IDENTIFIER = __CLASS__;


    /**
     * constructor
     *
     * This method is intend to act as constructor.
     * If you use custom configuration options -> ensure that they are enabled via $thisContainerAllowedOptions!
     *
     * @param array $options Custom configuration options
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object instance of this class
     * @access public
     * @throws DoozR_Cache_Module_Exception
     */
    public function __construct(array $options = array())
    {
        // do the check and transfer of allowed options
        parent::__construct($options);

        // init a connection to server
        $this->_memcache = $this->_connect($this->hostname, $this->port);

        // if highwater = max -> retrieve configuration from server to define highwater
        if ($this->highwater == 'max') {
            $serverConfiguration = $this->_memcache->getExtendedStats();
            $this->highwater     = $serverConfiguration[$this->hostname.':'.$this->port]['limit_maxbytes'];
        }
    }

    /**
     * stores a dataset
     *
     * This method is intend to write data to cache.
     * WARNING: If you supply userdata it must not contain any linebreaks, otherwise it will break the filestructure.
     *
     * @param string  $id      The dataset Id
     * @param string  $buffer  The data to write to cache
     * @param integer $expires Date/Time on which the cache-entry expires
     * @param string  $group   The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success
     * @access public
     * @throws DoozR_Cache_Module_Exception
     */
    public function create($id, $buffer, $expires, $group)
    {
        // flush
        $this->flushPreload($id, $group);

        // prepare
        $buffer = $this->encode($buffer);
        $flags  = ($this->_compress === true) ? MEMCACHE_COMPRESSED : 0;

        // store in memcache
        if (!$this->_memcache->set(
                         md5(self::UNIQUE_IDENTIFIER.$group.$id),
                         $buffer,
                         $flags,
                         $this->getExpiresAbsolute($expires)
            )
        ) {
            throw new DoozR_Cache_Module_Exception(
                'Error while creating dataset!'
            );
        }

        /*
        if (!$this->_memcache->set(
            md5(self::UNIQUE_IDENTIFIER.$group.$id),
            $buffer,
            $flags,
            $this->getExpiresAbsolute($expires)
        )) {
            throw new DoozR_Cache_Module_Exception(
                'Error while creating dataset!'
            );
        }
        */

        // success
        return true;
    }

    /**
     * reads a dataset
     *
     * This method is intend to read data from cache.
     *
     * @param string $id    The dataset Id
     * @param string $group The dataset group
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Array containing data from cache on success, otherwise FALSE
     * @access public
     */
    public function read($id, $group)
    {
        // try to read from cache (server)
        $result = @$this->_memcache->get(md5(self::UNIQUE_IDENTIFIER.$group.$id));
        return ($result !== false) ? $this->decode($result) : $result;
    }

    /**
     * updates a dataset
     *
     * This method is intend to write data to cache. This is a real implementation
     * Memcached DB supports updates by calling replace().
     *
     * @param string  $id       The dataset Id
     * @param string  $buffer   The data to write to cache
     * @param integer $expires  Date/Time on which the cache-entry expires
     * @param string  $group    The dataset group
     * @param string  $userdata The custom userdata to add
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success
     * @access public
     * @throws DoozR_Cache_Module_Exception
     */
    public function update($id, $buffer, $expires, $group, $userdata)
    {
        return $this->_memcache->replace(
            md5(self::UNIQUE_IDENTIFIER.$group.$id),
            array(
                $this->getExpiresAbsolute($expires),
                $userdata,
                $this->encode($buffer)
            ),
            0,
            $expires
        );
    }

    /**
     * removes a dataset finally from container
     *
     * This method is intend to remove an dataset finally from container.
     *
     * @param string $id    The id of the dataset
     * @param string $group The group of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE on success, otherwise FALSE
     * @access protected
     * @throws DoozR_Cache_Module_Exception
     */
    public function delete($id, $group)
    {
        // IMPORTANT: flush preload
        $this->flushPreload($id, $group);

        // build identifier
        $key = md5(self::UNIQUE_IDENTIFIER.$group.$id);

        if ($this->_memcache->delete($key, 0)) {
            return true;
        } else {
            throw new DoozR_Cache_Module_Exception(
                'Error while deleting key: "'.$key.'" of group: "'.$group.'"!'
            );
        }
    }

    /**
     * returns the current status of the memcache server
     *
     * This method is intend to return the current status of the memcache server.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Status of server as ARRAY, otherwise FALSE
     * @access public
     * @throws DoozR_Cache_Module_Exception
     */
    public function getStatus()
    {
        return $this->_memcache->getExtendedStats();
    }

    /**
     * returns the caching status of a given id and group
     *
     * This method is intend to return the caching status of a given id and group.
     *
     * @param string $id    The Id for lookup
     * @param string $group The group for lookup
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed BOOLEAN false if not found, otherwise the result from cache
     * @access public
     * @throws DoozR_Cache_Module_Exception
     */
    public function isCached($id, $group)
    {
        $result = $this->read($id, $group);
        return $result;
    }

    /**
     * deletes all entries which exceeds highwater level
     *
     * This method is intend to delete all entries which exceed the highwater marker. For
     * expiration we do not care cause memcache has its own garbage collector
     *
     * @param integer $maxlifetime Maximum lifetime in seconds of an no longer used/touched entry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean The result of the operation
     * @access public
     * @throws DoozR_Cache_Module_Exception
     */
    public function garbageCollection($maxlifetime)
    {
        // do the flush
        parent::garbageCollection($maxlifetime);

        //
        $datasets = array();

        // get all entries from server
        $entries = $this->_getEntries();

        // build identifier
        $identifier = md5(self::UNIQUE_IDENTIFIER).$group;

        // caluclate identifier length
        $length = strlen($identifier);

        foreach ($entries as $key => $entry) {
            if (substr($key, 0, $length) == $identifier) {
                $datasets[] = $entry;
                pred($datasets);
            }
        }

        pred($entries);

        /*
        // check the space used by the cache entries
        if ($this->_totalSize > $this->highwater) {
            krsort($this->_entries);
            reset($this->_entries);

            while ($this->_totalSize > $this->lowwater && list($lastmod, $entry) = each($this->_entries)) {
                if (@unlink($entry['file'])) {
                    $this->_totalSize -= $entry['size'];
                } else {
                    throw new DoozR_Cache_Module_Exception(
                        'Can\'t delete '.$entry['file'].'. Check the permissions.'
                    );
                }
            }
        }

        $this->_entries = array();
        $this->_totalSize = 0;
        */

        // return the result of the operation
        return $result;
    }

    /**
     * checks if a dataset exists
     *
     * This method is intend to check if a dataset exists.
     *
     * @param string $id    The id of the dataset
     * @param string $group The group of the dataset
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if file exist, otherwise FALSE
     * @access protected
     * @throws DoozR_Cache_Module_Exception
     */
    protected function idExists($id, $group)
    {
        // build identifier
        $key = md5(self::UNIQUE_IDENTIFIER.$group.$id);

        return ($this->_memcache->get($key) === false) ? false : true;
    }

    /**
     * flushes the cache
     *
     * This method is intend to flush the cache. It removes all caches datasets from the cache.
     *
     * @param string $group The dataset group to flush
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed datasets on success, otherwise FALSE
     * @access public
     */
    public function flush($group)
    {
        // flush
        $this->flushPreload();

        // delete entries and retrieve count
        $removedEntries = $this->_removeEntries($group);

        // return count of removed entries
        return $removedEntries;
    }

    /**
     * deletes a directory and all files in it
     *
     * This method is intend to delete a directory and all files in it.
     *
     * @param string $group The group of entries to remove
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Number of removed entries on success, otherwise FALSE
     * @access private
     * @throws DoozR_Cache_Module_Exception
     */
    private function _removeEntries($group)
    {
        // count of entries removed
        $entriesRemoved = 0;

        // get all entries from server
        $entries = $this->_getEntries();

        // build identifier
        $identifier = md5(self::UNIQUE_IDENTIFIER).$group;

        // caluclate identifier length
        $length = strlen($identifier);

        foreach ($entries as $key => $entry) {
            if (substr($key, 0, $length) == $identifier) {
                if ($this->_memcache->delete($key, 0)) {
                    ++$entriesRemoved;
                } else {
                    throw new DoozR_Cache_Module_Exception(
                        'Error while removing key: "'.$key.'" of group: "'.$group.'" from memcache!'
                    );
                }
            }
        }

        // return the count of removed entries
        return $entriesRemoved;
    }

    /**
     * returns all entries from memcache server
     *
     * This method is intend to return all entries from memcache server.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array All entries form memcache server
     * @access private
     * @throws DoozR_Cache_Module_Exception
     */
    private function _getEntries()
    {
        $list     = array();
        $allSlabs = $this->_memcache->getExtendedStats('slabs');
        $items    = $this->_memcache->getExtendedStats('items');

        foreach ($allSlabs as $server => $slabs) {
            foreach ($slabs as $slabId => $slabMeta) {
                $cdump = $this->_memcache->getExtendedStats('cachedump', (int)$slabId);
                foreach ($cdump as $server => $entries) {
                    if ($entries) {
                        foreach ($entries as $eName => $eData) {
                            $list[$eName] = array(
                                 'key' => $eName,
                                 'server' => $server,
                                 'slabId' => $slabId,
                                 'detail' => $eData,
                                 'age' => $items[$server]['items'][$slabId]['age'],
                            );
                        }
                    }
                }
            }
        }

        ksort($list);
        return $list;
    }

    /**
     * connects to a given server and port
     *
     * This method is intend to connect to a given server and port.
     *
     * @param string $hostname The hostname to connect to
     * @param string $port     The port to connect to
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The created instance of memcache
     * @access private
     * @throws DoozR_Cache_Module_Exception
     */
    private function _connect($hostname, $port)
    {
        $memcache = new Memcache();

        // API requires to add server first
        $memcache->addServer($hostname, $port);

        // then we check if its up
        if ($memcache->getServerStatus($hostname, $port) == 0) {
            throw new DoozR_Cache_Module_Exception(
                'Server seems to be down. Could not connect to hostname: "'.$hostname.'" on Port: "'.$port.'".'
            );
        }

        // and finally we try to connect
        if (!@$memcache->connect($hostname, $port)) {
            throw new DoozR_Cache_Module_Exception(
                'Error while connecting to host: "'.$hostname.'" on Port: "'.$port.'". Connection failed.'
            );
        }

        // return instance on success
        return $memcache;
    }
}

?>
