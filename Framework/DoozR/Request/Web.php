<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Request - Web
 *
 * Web.php - Request-Handler for requests passed through WEB to Front-Controller.
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
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Web
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'DoozR/Base/Request.php';
require_once DOOZR_DOCUMENT_ROOT.'DoozR/Request/Interface.php';

/**
 * DoozR Request-Web
 *
 * Request-Web of the DoozR-Framework
 *
 * @category   DoozR
 * @package    DoozR_Request
 * @subpackage DoozR_Request_Web
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2014 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Request_Web extends DoozR_Base_Request implements DoozR_Request_Interface
{
    /**
     * need to identify HTML Redirect Type
     *
     * @var string
     * @access const
     */
    const REDIRECT_TYPE_HTML = 'html';

    /**
     * need to identify Header Redirect Type
     *
     * @var string
     * @access const
     */
    const REDIRECT_TYPE_HEADER = 'header';

    /**
     * need to identify HTML Redirect Type
     *
     * @var string
     * @access const
     */
    const REDIRECT_TYPE_JS = 'js';

    /**
     * holds the own type of Request
     *
     * @var string
     * @access const
     */
    const TYPE = 'web';

    /**
     * Request-Method-Types
     * supported by this controller
     *
     * @var string
     * @access public
     * @const
     */
    const METHOD_GET     = 'GET';
    const METHOD_PUT     = 'PUT';
    const METHOD_POST    = 'POST';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_TRACE   = 'TRACE';


    /**
     * Constructor.
     *
     * @param DoozR_Config $config An instance of config
     * @param DoozR_Logger $logger An instance of logger
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Request_Web
     * @access public
     */
    public function __construct(DoozR_Config $config, DoozR_Logger $logger)
    {
        // store instance(s)
        $this->logger = $logger;
        $this->config = $config;

        // set type
        self::$type = self::TYPE;

        // list of valid request sources and type (native | emulated)
        $this->_requestSources = array(
            'GET'         => self::NATIVE,
            'POST'        => self::NATIVE,
            'HEAD'        => self::EMULATED,
            'OPTIONS'     => self::EMULATED,
            'PUT'         => self::EMULATED,
            'DELETE'      => self::EMULATED,
            'TRACE'       => self::EMULATED,
            'COOKIE'      => self::NATIVE,
            'REQUEST'     => self::NATIVE,
            'SESSION'     => self::NATIVE,
            'ENVIRONMENT' => self::NATIVE,
            'SERVER'      => self::NATIVE,
            'FILES'       => self::NATIVE,
        );

        // check for ssl forcement
        $this->_checkForceSecureConnection();

        // prepare non-native request for global PHP like access
        $this->emulateRequest();

        // check automatic conversion of input
        $this->arguments = $this->transformToRequestObject(
            $this->getMethod()
        );

        // protocolize the incoming request data
        $this->_protocolize();

        // store URL (the base of all requests)
        $this->url = strtok($_SERVER['REQUEST_URI'], '?');
    }

    /**
     * This method returns TRUE if the current requests type
     * is GET.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if current request is GET, otherwise FALSE
     * @access public
     */
    public function isGet()
    {
        return ($this->getMethod() === self::METHOD_GET);
    }

    /**
     * This method returns TRUE if the current requests type
     * is HEAD.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if current request is HEAD, otherwise FALSE
     * @access public
     */
    public function isHead()
    {
        return ($this->getMethod() === self::METHOD_HEAD);
    }

    /**
     * This method returns TRUE if the current requests type
     * is PUT.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if current request is PUT, otherwise FALSE
     * @access public
     */
    public function isPut()
    {
        return ($this->getMethod() === self::METHOD_PUT);
    }

    /**
     * This method returns TRUE if the current requests type
     * is POST.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if current request is POST, otherwise FALSE
     * @access public
     */
    public function isPost()
    {
        return ($this->getMethod() === self::METHOD_POST);
    }

    /**
     * This method returns TRUE if the current requests type
     * is DELETE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if current request is DELETE, otherwise FALSE
     * @access public
     */
    public function isDelete()
    {
        return ($this->getMethod() === self::METHOD_DELETE);
    }

    /**
     * This method returns TRUE if the current requests type
     * is OPTIONS.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if current request is OPTIONS, otherwise FALSE
     * @access public
     */
    public function isOptions()
    {
        return ($this->getMethod() === self::METHOD_OPTIONS);
    }

    /**
     * This method returns TRUE if the current requests type
     * is TRACE.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if current request is TRACE, otherwise FALSE
     * @access public
     */
    public function isTrace()
    {
        return ($this->getMethod() === self::METHOD_TRACE);
    }

    /**
     * This method is intend to return the global $_GET vars if current request is of type GET.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Request_Parameter ($_GET) if request is of type GET, otherwise NULL
     * @access public
     */
    public function getGet()
    {
        if ($this->isGet()) {
            return $_GET;
        }

        // return null if not GET
        return null;
    }

    /**
     * This method is intend to return the global $_POST vars if current request is of type POST.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Request_Parameter ($_POST) if request is of type POST, otherwise NULL
     * @access public
     */
    public function getPost()
    {
        if ($this->isPost()) {
            return $_POST;
        }

        // return null if not POST
        return null;
    }

    /**
     * This method is intend to return the global $_REQUEST vars if current request is of type REQUEST.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Request_Parameter ($_REQUEST) if request is of type REQUEST, otherwise NULL
     * @access public
     */
    public function getRequest()
    {
        return $_REQUEST;
    }

    /**
     * This method protocolizes request details if running in debug-mode
     * enabled. This should help debugging the application.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access private
     */
    private function _protocolize()
    {
        // this is expensive so only in debug available
        if ($this->config->debug->enabled()) {

            // log Request-Parameter and Request-Header
            $this->logger->debug(
                "Request-Header: \n".self::getRequestHeader(true)
            );

            // if request defined -> log its parameter (all)
            if (count($_REQUEST) > 0) {
                $this->logger->debug(
                    "Request-Parameter: \n".self::getRequestAsString()
                );
            }
        }
    }

    /**
     * If FORCE_SSL is defined in TRANSMISSION part of the core-config and set to true
     * every non-ssl request will be redirected to ssl (https)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean true if successful otherwise false
     * @access private
     */
    private function _checkForceSecureConnection()
    {
        if ($this->config->transmission->ssl->force()) {
            if (!$this->isSsl()) {
                header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
            }
        }
    }

    /**
     * get all request-headers
     *
     * if the $string parameter is setted to true, this function will return
     * an string instead of an array
     *
     * @param bool $string Set to true to retrive header as string, otherwise array is returned
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed All request-headers as: "string" if parameter $string is set to true, otherwise as "array"
     * @access public
     * @static
     */
    public static function getRequestHeader($string = false)
    {
        $header = getallheaders();

        if ($string === true) {
            $allheaders = '';
            foreach ($header as $headerName => $headerValue) {
                $allheaders .= $headerName.' = '.$headerValue."\n";
            }
            $header = $allheaders;
        }

        return $header;
    }

    /**
     * Returns the PHP Global Array $_REQUEST as string
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array All given $_REQUEST-vars as string
     * @access public
     * @static
     */
    public static function getRequestAsString()
    {
        $request = '';

        foreach ($_REQUEST as $parameter => $value) {
            $cleaned  = self::clean($value);
            $request .= $parameter.' = '.((is_array($cleaned)) ? serialize($cleaned) : $cleaned)."\n";
        }

        return $request;
    }

    /**
     * Returns the url of the current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The url
     * @access public
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url of the current request
     *
     * @param string $url The URL to set as active URL of current request
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The url
     * @access public
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Cleans the input variable. This method removes tags with strip_tags and afterwards
     * it turns all non-safe characters to its htmlentities.
     *
     * @param mixed $mixed The input to clean
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The cleaned input
     * @access public
     * @static
     */
    public static function clean($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::clean($value);
            }

        } else {
            $mixed = htmlentities(strip_tags($mixed));
        }

        return $mixed;
    }

    /**
     * This method is intend to return the SSL-status of the current request.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the request is SSL, otherwise FALSE
     * @access public
     */
    public function isSsl()
    {
        return (
            isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on')
        );
    }

    /**
     * This method is intend to check for protocol and returns it
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The protocol used while accessing a resource
     * @access public
     */
    public function getProtocol()
    {
        if ($this->isSsl()) {
            return 'https://';
        } else {
            return 'http://';
        }
    }

    /**
     * This is a shortcut to globals conversion
     *
     * @param string $method    The name of the method called
     * @param array  $arguments The parameter of the method call
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @access magic
     */
    public function __call($method, $arguments)
    {
        // check if init done for requested source;
        if (!isset(self::$initialized[$method])) {
            self::$initialized[$method] = $this->transform($method);
        }

        if (in_array($method, $this->_requestSources) && count($arguments) === 0) {
            return $GLOBALS['_'.$method];
        }
    }
}
