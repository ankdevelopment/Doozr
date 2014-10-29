<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Config - Container - Abstract
 *
 * Abstract.php - Abstract base for Config container usable by config manager
 * of the DoozR Framework (e.g. DoozR_Config).
 *
 * PHP versions 5
 *
 * LICENSE:
 * DoozR - The PHP-Framework
 *
 * Copyright (c) 2005 - 2014, Benjamin Carl - All rights reserved.
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
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Container
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT . 'DoozR/Base/Class/Singleton/Strict.php';

/**
 * DoozR - Config - Container - Abstract
 *
 * Abstract base for Config container usable by config manager
 * of the DoozR Framework (e.g. DoozR_Config).
 *
 * @category   DoozR
 * @package    DoozR_Config
 * @subpackage DoozR_Config_Container
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Config_Container_Abstract extends DoozR_Base_Class_Singleton_Strict
{
    /**
     * The configuration
     *
     * @var array
     * @access protected
     */
    protected $configuration = array();

    /**
     * The list of valid resources
     * to prevent further filesystem accesses
     *
     * @var array
     * @access protected
     */
    protected $validResources = array();

    /**
     * The list replacements
     *
     * @var array
     * @access protected
     */
    protected $replacementMatrix = array();

    /**
     * An instance of cache
     *
     * @var DoozR_Cache_Service
     * @access protected
     */
    protected $cache;

    /**
     * An instance of DoozR_Path
     *
     * @var object
     * @access protected
     */
    protected $path;

    /**
     * An instance of DoozR_Logger
     *
     * @var object
     * @access protected
     */
    protected $logger;

    /**
     * The current part of the chain
     *
     * @var object
     * @access protected
     */
    protected $currentChainlink;

    /**
     * Marks a configuration as changed (dirty)
     * So it can be written to filesystem on __destruct
     *
     * @var boolean
     * @access protected
     */
    protected $dirty = false;

    /**
     * The marker for the begin of a replacement/placeholder
     * e.g. {{REPLACE_ME}}
     *
     * @var string
     * @access const
     */
    const PLACEHOLDER_BEGIN = '{{';

    /**
     * The marker for the end of a replacement/placeholder
     * e.g. {{REPLACE_ME}}
     *
     * @var string
     * @access const
     */
    const PLACEHOLDER_END = '}}';


    /**
     * This method is the constructor of the class.
     *
     * @param DoozR_Path_Interface   $path          An instance of DoozR_Path
     * @param DoozR_Logger_Interface $logger        An instance of DoozR_Logger
     * @param boolean                $enableCaching TRUE to enable caching, otherwise FALSE to do not
     *
     * @throws DoozR_Exception
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Config_Container_Abstract
     * @access protected
     */
    protected function __construct(DoozR_Path_Interface $path, DoozR_Logger_Interface $logger, $enableCaching = false)
    {
        $this->path   = $path;
        $this->logger = $logger;
        $this->cache  = DoozR_Loader_Serviceloader::load('cache', DOOZR_UNIX);

        // try to load file container
        try {
            $this->cache->setContainer('filesystem');

        } catch (DoozR_Cache_Service_Exception $e) {
            var_dump($e->getMessage());
            die;

            throw new DoozR_Exception(
                'Error while initializing cache! Neither file nor memcache container can be used.',
                null,
                $e
            );
        }

        /*
        // try to use memcache as container
        try {
            $this->cache->setContainer('memcache');

        } catch (Exception $e) {
            // Use file-container as fallback
            $this->cache->setContainerOptions(
                array(
                    'directory'      => $path->get('cache'),
                    'filenamePrefix' => 'cache_'
                )
            );

            // try to load file container
            try {
                $this->cache->setContainer('filesystem');

            } catch (DoozR_Cache_Service_Exception $e) {
                throw new DoozR_Exception(
                    'Error while initializing cache! Neither file nor memcache container can be used.',
                    null,
                    $e
                );
            }
        }
        */

        $this->attachDefaultReplacements();
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN OVERLOADABLE METHODS OF CONTAINER
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is called from constructor to attach the default transformations
     * like DOCUMENT_ROOT or SERVERNAME (DOMAIN). The so called "transforms" are applied to each
     * part of the config (section or value) and replaces placeholder with runtime information(s).
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function attachDefaultReplacements()
    {
        // get all constants
        $constants = get_defined_constants(true);

        // cut off from us defined ones
        $contants = $constants['user'];

        // add for replacement
        foreach ($contants as $constant => $value) {
            $this->attachReplacement(self::PLACEHOLDER_BEGIN.$constant.self::PLACEHOLDER_END, $value);
        }

        // server-name
        $this->attachReplacement(
            self::PLACEHOLDER_BEGIN.'DOOZR_SERVERNAME'.self::PLACEHOLDER_END,
            (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : 'SERVER_NAME'
        );
    }

    /**
     * This method is intend to replace the current defined replaces in a given mixed var
     *
     * @param mixed $configuration The content to replace the replacements in
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The content including the concrete replaces
     * @access protected
     * @throws DoozR_Exception
     */
    protected function doReplacement($configuration)
    {
        // only string work for current versions
        if (!is_string($configuration)) {
            throw new DoozR_Exception(
                'Error while replacing placeholder in configuration. Replacement currently only works with strings but'.
                ' a "'.gettype($configuration).'" was passed.'
            );
        }

        foreach ($this->replacementMatrix as $search => $replace) {
            $configuration = str_replace($search, $replace, $configuration);
        }

        // return the replaced content
        return $configuration;
    }

    /**
     * This method is intend to generate and return an unique Id for a given resource.
     *
     * @param mixed $resource Any type of variable
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The generated unique Id
     * @access public
     */
    public function getUid($resource)
    {
        return md5(serialize($resource));
    }

    /**
     * This method is intend to store the raw configuration of the current instance.
     *
     * @param mixed $configuration The configuration to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRaw($configuration)
    {
        $this->configuration['raw'] = $configuration;
    }

    /**
     * This method is intend to store the raw configuration of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The raw configuration of the current instance
     * @access public
     */
    public function getRaw()
    {
        return $this->configuration['raw'];
    }

    /**
     * This method is intend to store the processed configuration of the current instance.
     *
     * @param mixed $configuration The configuration to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setProcessed($configuration)
    {
        $this->configuration['processed'] = $configuration;
    }

    /**
     * This method is intend to store the processed configuration of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The processed configuration of the current instance
     * @access public
     */
    public function getProcessed()
    {
        return $this->configuration['processed'];
    }

    /**
     * This method is intend to store the parsed configuration of the current instance.
     *
     * @param mixed $configuration The configuration to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setParsed($configuration)
    {
        $this->configuration['parsed'] = $configuration;
    }

    /**
     * This method is intend to store the processed configuration of the current instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The processed configuration of the current instance
     * @access public
     */
    public function getParsed()
    {
        return $this->configuration['parsed'];
    }

    /**
     * This method is intend to merge two objects of configurations recursive to a new one.
     * Where new keys are created in configuration-1 and existing values! are overwritten
     * by values of configuration-2 (smart override).
     *
     * @param mixed $configuration1 The configuration to merge the second configuration in
     * @param mixed $configuration2 The configuration to merge into the first one
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object The resulting + merged configuration object
     * @access public
     */
    public function mergeConfigurations($configuration1, $configuration2)
    {
        $a1 = object_to_array($configuration1);
        $a2 = object_to_array($configuration2);
        $r1 = array_replace_recursive($a1, $a2);
        $r2 = array_to_object($r1);

        // merge configurations and return result as object
        return $r2;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * This method is used to attach a transformation (replacement) for a given string
     *
     * @param string $from The value which should be replaced
     * @param string $to   The value which should be inserted for every occurance of $from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function attachReplacement($from, $to)
    {
        $this->replacementMatrix[$from] = addcslashes($to, '\\');
    }

    /**
     * This method is intend to store a complete configuration array with all it parts (e.g.
     * raw, processed, parsed).
     *
     * @param array   $configuration The configuration array to store
     * @param boolean $merge         TRUE if configuration should be merged with existing
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setConfiguration(array $configuration, $merge)
    {
        if (empty($this->configuration) || !$merge) {
            // simple store
            $this->configuration = $configuration;
        } else {
            // merge (currently only the parsed get merged!
            $this->configuration['parsed'] = $this->mergeConfigurations(
                $this->configuration['parsed'],
                $configuration['parsed']
            );
        }
    }

    /**
     * This method is intend to return either the full configuration or parts of it.
     *
     * @param array $part The configuration array to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Complete configuration or value of a part
     * @access public
     */
    public function getConfiguration($part = null)
    {
        if ($part) {
            $data = (isset($this->configuration[$part])) ? $this->configuration[$part] : null;
        } else {
            $data = $this->configuration;
        }

        if ($data && is_array($data)) {
            $data = array_to_object($data);
        }

        return $data;
    }

    /*------------------------------------------------------------------------------------------------------------------
    | BEGIN CHAINING SUPPORT FOR READING CONFIG
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns a node/value from config
     *
     * This method is intend to return a node/value from config. The magic-method __get
     * is used for generic chaining and returning values.
     *
     * @param string $node The node to return
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Requested node/value
     * @access public
     * @throws DoozR_Config_Container_Exception
     */
    public function __get($node)
    {
        // first of all get an active chain
        if (!$this->currentChainlink) {
            $this->resetChainlink();
        }

        // check if the node exists
        if (!isset($this->currentChainlink->{$node})) {

            pred($this->getConfiguration('parsed'));

            // throw exception
            throw new DoozR_Config_Container_Exception('Config entry "' . $node . '" does not exist!');
        }

        // retrieve next requested chain relative to base
        $this->currentChainlink = $this->currentChainlink->{$node};

        // return active instance for chaining ...
        if (is_object($this->currentChainlink)) {
            return $this;

        } else {
            // or value if no more chaining possible + reset chain for following calls
            $value = $this->currentChainlink;

            // reset
            $this->currentChainlink = null;

            // return
            return $value;
        }
    }

    protected function resetChainlink()
    {
        $this->currentChainlink = $this->getConfiguration('parsed');
    }

    /**
     * Returns a node/value from config
     *
     * This method is intend to return a node/value from config. The magic-method __call
     * is used for generic chaining and returning values.
     *
     * @param string $node The node to return
     * @param $value
     * @throws DoozR_Config_Container_Exception
     * @internal param array $returnAsArray TRUE to return node/value as array, otherwise FALSE to return object (default)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Requested node/value
     * @access public
     */
    public function __call($node, $value)
    {
        // get active chain
        if (!$this->currentChainlink) {
            $this->resetChainlink();
        }

        // check if the node exists
        if (!isset($this->currentChainlink->{$node})) {
            // throw exception
            pred($node);

            throw new DoozR_Config_Container_Exception('Config entry "' . $node . '" does not exist!');
        }

        // check SET (key,value) or GET (key)
        if (count($value)) {
            $value = $value[0];
            $result = $this->currentChainlink->{$node} = $value;

        } else {
            $result = $this->currentChainlink->{$node};
        }

        // reset after __call()
        $this->currentChainlink = null;

        // return the result
        return $result;
    }

    /**
     * Checks if a property exists
     *
     * @param string $property The name of the property to check existence of
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if isset, otherwise FALSE
     * @access public
     */
    public function __isset($property)
    {
        $base = $this->getConfiguration('parsed');
        return isset($base->{$property});
    }
}
