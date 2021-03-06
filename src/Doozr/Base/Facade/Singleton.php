<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Base - Facade - Singleton
 *
 * Singleton.php - Base-Facade-Singleton for all ...
 *
 * PHP versions 5.5
 *
 * LICENSE:
 * Doozr - The lightweight PHP-Framework for high-performance websites
 *
 * Copyright (c) 2005 - 2016, Benjamin Carl - All rights reserved.
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
 *   must display the following acknowledgment: This product includes software
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
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Facade
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */

require_once DOOZR_DOCUMENT_ROOT.'Doozr/Base/Class/Singleton.php';

/**
 * Doozr - Base - Facade - Singleton
 *
 * Base-Facade-Singleton for all ...
 *
 * @category   Doozr
 * @package    Doozr_Base
 * @subpackage Doozr_Base_Facade
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Base_Facade_Singleton extends Doozr_Base_Class_Singleton
{
    /**
     * contains an instance of the class/object decorated
     *
     * @var object
     * @access protected
     */
    protected $decoratedObject;

    /**
     * This method is intend to act as setter for $decoratedObject.
     *
     * @param object $instance An instance of a class to decorate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setDecoratedObject($instance)
    {
        $this->decoratedObject = $instance;
    }

    /**
     * This method is intend to act as setter for $decoratedObject.
     *
     * @param object $instance An instance of a class to decorate
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function decoratedObject($instance)
    {
        $this->decoratedObject = $instance;
        return $this;
    }

    /**
     * This method is intend to act as getter for $decoratedObject.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return object An instance of a class
     * @access protected
     */
    protected function getDecoratedObject()
    {
        return $this->decoratedObject;
    }

    /**
     * generic facade - for all non-implemented methods
     *
     * This method is intend to act as generic facade - for all non-implemented methods
     *
     * @param string $signature The signature (name of the method) originally called
     * @param mixed  $arguments The arguments used for call (can be either an ARRAY of values or NULL)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of called method if exists, otherwise NULL
     * @access public
     */
    public function __call($signature, $arguments)
    {
        if ($arguments) {
            $result = call_user_func_array(
                array($this->decoratedObject, $signature),
                $arguments
            );
        } else {
            $result = call_user_func(
                array($this->decoratedObject, $signature)
            );
        }

        return $result;
    }

    /**
     * This method is intend to act as generic facade - for all non-implemented static methods
     *
     * @param string $signature The signature (name of the method) originally called
     * @param mixed  $arguments The arguments used for call (can be either an ARRAY of values or NULL)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Result of called method if exists, otherwise NULL
     * @access public
     * @static
     */
    public static function __callStatic($signature, $arguments)
    {
        $targetClassName = get_class(self::$decoratedObject);

        if ($arguments) {
            $result = call_user_func_array(
                $targetClassName.'::'.$signature,
                $arguments
            );
        } else {
            $result = call_user_func(
                array($targetClassName, $signature)
            );
        }

        //
        return $result;
    }

    /**
     * generic getter for dispatching to decorated object
     *
     * This method is intend to act as generic getter for dispatching to decorated object.
     *
     * @param string $property The property to return
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from property
     * @access public
     */
    public function __get($property)
    {
        if ($property != 'decoratedObject') {
            return $this->decoratedObject->{$property};
        }
    }

    /**
     * generic setter for dispatching to decorated object
     *
     * This method is intend to act as generic setter for dispatching to decorated object.
     *
     * @param string $property The property to set
     * @param mixed  $value    The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The data from property
     * @access public
     */
    public function __set($property, $value)
    {
        if ($property != 'decoratedObject') {
            return $this->decoratedObject->{$property} = $value;
        }
    }

    /**
     * generic isset dispatch to decorated object
     *
     * @param $property The property being checked for existence
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean|null TRUE if isset, otherwise FALSE
     * @access public
     */
    public function __isset($property)
    {
        if ($property != 'decoratedObject') {
            return isset($this->decoratedObject->{$property});
        }
    }
}
