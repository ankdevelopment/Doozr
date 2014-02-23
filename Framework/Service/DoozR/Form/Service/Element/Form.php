<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
 * Form.php - The Form element control layer which adds validation,
 * and so on to an HTML element.
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
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */

require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Html/Form.php';
require_once DOOZR_DOCUMENT_ROOT.'Service/DoozR/Form/Service/Element/Interface.php';

/**
 * DoozR - Form - Service
 *
 * The Form element control layer which adds validation,
 * and so on to an HTML element.
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 */
class DoozR_Form_Service_Element_Form extends DoozR_Form_Service_Element_Html_Form
    implements DoozR_Form_Service_Element_Interface, SplObserver
{
    /**
     * The validity of this element
     *
     * @var boolean TRUE if element is valid, otherwise FALSE if not
     * @access protected
     */
    protected $valid;

    /**
     * The validations of this field
     *
     * @var array
     * @access protected
     */
    protected $validation = array();


    /**
     * Constructor.
     *
     * @param string $name The name of the form
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Html_Form $this
     * @access public
     */
    public function __construct($name = '')
    {
        $this->setAttribute('name', $name);
    }


    public function enableUpload()
    {
        $this->setEncodingType(DoozR_Form_Service_Constant::ENCODING_TYPE_FILEUPLOAD);

        return $this;
    }

    public function setEncodingType($encodingType = DoozR_Form_Service_Constant::ENCODING_TYPE_DEFAULT)
    {
        $this->setAttribute('enctype', $encodingType);

        return $this;
    }

    public function getEncodingType()
    {
        return $this->getAttribute('enctype');
    }



    /**
     * Returns the validity state of the element.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if valid, otherwise FALSE
     * @access public
     */
    public function isValid(
        $arguments = array(),
        $store = array(),
        DoozR_Form_Service_Validate_Validator $validator = null
    ) {
        if ($this->valid === null) {
            // assume a valid state for boolean operator
            $valid = true;

            /* @var DoozR_Form_Service_Element_Interface $child */
            foreach ($this->childs as $child) {
                $valid = $valid && $child->isValid($arguments, $store, $validator);
            }

            $this->valid = $valid;
        }

        // here we would iterate elements of the form to check the validity
        return $this->valid;
    }

    /**
     * Stores/adds the passed validation information.
     *
     * @param string      $validation The type of validation
     * @param null|string $value      The value for validation or NULL
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Element_Input
     * @access public
     */
    public function addValidation($validation, $value = null)
    {
        if (!isset($this->validation[$validation])) {
            $this->validation[$validation] = array();
        }

        $this->validation[$validation][] = $value;
    }

    /**
     * Getter for validation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array Validations as array
     * @access public
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * Setter for value.
     *
     * @param mixed $value The value to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        // intentionally left blank
    }

    /**
     * Getter for value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed Value of this element
     * @access public
     */
    public function getValue()
    {
        return null;
    }

    /**
     * Update method for SplObserver Interface.
     *
     * @param SplSubject $subject The subject
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function update(SplSubject $subject)
    {
        var_dump($subject);
        pred(__METHOD__);
    }
}
