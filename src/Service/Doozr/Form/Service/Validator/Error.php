<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service - Error.
 *
 * Error.php - Form Error class
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
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */

/**
 * Doozr - Service - Form - Error.
 *
 * Error.php - ...
 *
 * @category   Doozr
 *
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2016 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *
 * @version    Git: $Id$
 *
 * @link       http://clickalicious.github.com/Doozr/
 */
class Doozr_Form_Service_Validator_Error
{
    /**
     * The error.
     *
     * @var string
     */
    protected $error;

    /**
     * Additional info to error.
     *
     * @var mixed
     */
    protected $info;

    /**
     * The value which triggered this error.
     *
     * @var mixed
     */
    private $value;

    /**
     * The error-code.
     *
     * @var int
     */
    protected $errorCode = 0;

    /**
     * The error-message.
     *
     * @var string
     */
    protected $errorMessage = '';

    /**
     * The "error to error-code" translation-matrix.
     *
     * @var array
     * @static
     */
    protected $errorCodeMatrix = [];

    /**
     * The "error to error-message" translation-matrix.
     *
     * @var array
     * @static
     */
    protected $errorMessageMatrix = [
         0 => 'UNKNOWN_ERROR',
         1 => 'N.A.',
         2 => 'This field is required.',
         3 => 'This field must not be empty.',
         4 => 'The input should only contain alphabetic characters in the range a-z or A-Z.',
         5 => 'This field must be checked.',
         6 => 'The minimum input length for this field is:',
         7 => 'The maximum input length for this field is:',
         8 => 'This emailaddress seems to be invalid.',
         9 => 'This emailaddress seems to be non-existent. We couldn\'t deliver our email.',
        10 => 'The input should only contain numbers in the range 0-9.',
        11 => 'The input must be a valid value. 0 and empty-values (Null) are invalid.',
        12 => 'The input must be a valid IP-Address (e.g. 192.168.0.1 or 10.8.2.216)',
        13 => 'The input must be lowercase. Only Characters in range a-z are allowed',
        14 => 'The input must be uppercase. Only Characters in range A-Z are allowed',
        15 => 'The input must be a valid postcode of the country:',
        16 => 'The input must be a valid USTID of the country:',
        17 => 'This field must be empty.',
        18 => 'This field can be either TRUE or FALSE.',
        19 => 'This field must be of type double (e.g. 1.0 or 2.3 ...).',
        20 => 'This field must be of type integer (e.g. 1 or 2 or 374 or 9384984)',
    ];

    /**
     * Constructor.
     *
     * @param string|null $error Error to set
     * @param string|null $value Value which is responsible for this error
     * @param array|null  $info  Additional information to error (e.g. the count of given chars on error min-length ...)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function __construct($error = null, $value = null, $info = null)
    {
        $this
            ->init()
            ->error($error)
            ->value($value)
            ->info($info);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for error.
     *
     * @param string $error The error
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * Fluent: Setter for error.
     *
     * @param string $error The error
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function error($error)
    {
        $this->setError($error);

        return $this;
    }

    /**
     * Getter for error.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The error if set, otherwise NULL
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Setter for Value.
     *
     * @param string $value The value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Fluent: Setter for Value.
     *
     * @param string $value The value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function value($value)
    {
        $this->setValue($value);

        return $this;
    }

    /**
     * Getter for value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The value if set, otherwise NULL
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Setter for info.
     *
     * @param string $info The info
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * Fluent: Setter for info.
     *
     * @param string $info The info
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function info($info)
    {
        $this->setInfo($info);

        return $this;
    }

    /**
     * Returns the additional info.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return array The additional info
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Getter for the I18N-error-identifier.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The I18N-error-identifier
     */
    public function getI18nIdentifier()
    {
        // set identifier for localization
        return strtolower(__CLASS__.'_'.$this->getError());
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Initializes the validation matrix.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    protected function init()
    {
        // build the local matrix just once
        if (empty($this->errorCodeMatrix)) {
            // get type and order from Doozr_Form_Service_Validate so we don't need to define it manually again
            $typeMatrix = Doozr_Form_Service_Validator_Generic::getValidationTypeMatrix();

            // iterate over types and construct error-code-matrix of it
            foreach ($typeMatrix as $type => $order) {
                $this->errorCodeMatrix[$type] = ($order + 1);
            }
        }

        return $this;
    }

    /**
     * Makes the input safe for output (remove xss and so on).
     *
     * @param string $value The string to make safe for output
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The input safe for output
     */
    protected function safeOutput($value)
    {
        return urlencode(strip_tags($value));
    }

    /**
     * Returns error-code by error.
     *
     * @param string $error The error to return error-code for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return int The error-code
     */
    protected function getErrorCode($error)
    {
        return (isset($this->errorCodeMatrix[$error])) ? $this->errorCodeMatrix[$error] : 0;
    }

    /**
     * Returns error-message by error-code.
     *
     * @param int $errorCode The error-code to return error-message for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The error-message
     */
    protected function getErrorMessage($errorCode)
    {
        if (!isset($this->errorMessageMatrix[$errorCode])) {
            $this->errorMessageMatrix[$errorCode] = 'ERROR_MSG_UNKNOWN';
        }

        // if additional information exist -> add it here
        if ($this->info) {
            if (is_string($this->info)) {
                $this->errorMessageMatrix[$errorCode] .= ' '.$this->info;
            } elseif (count($this->info) != 1 || $this->info[0] != null) {
                foreach ($this->info as $info) {
                    $info = serialize($info);
                    $this->errorMessageMatrix[$errorCode] .= ' '.$info;
                }
            }
        }

        // Return constructed error message
        return $this->errorMessageMatrix[$errorCode];
    }
}
