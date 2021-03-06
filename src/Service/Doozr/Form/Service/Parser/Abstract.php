<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Abstract.php - Abstract base for parser.
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
 * Doozr - Form - Service.
 *
 * Abstract base for parser.
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
abstract class Doozr_Form_Service_Parser_Abstract
{
    /**
     * The input which get parsed by parser.
     *
     * @var string
     */
    protected $input;

    /**
     * The output returned by parser as result.
     *
     * @var mixed
     */
    protected $output;

    /**
     * Configuration object.
     *
     * @var Doozr_Form_Service_Configuration
     */
    protected $configuration;

    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param Doozr_Form_Service_Configuration $configuration The configuration object which is used to store and return
     *                                                        parsed configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return \Doozr_Form_Service_Parser_Abstract
     */
    public function __construct(Doozr_Form_Service_Configuration $configuration)
    {
        $this->setConfiguration($configuration);
    }

    /**
     * Setter for configuration.
     *
     * @param Doozr_Form_Service_Configuration $configuration The configuration to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setConfiguration(Doozr_Form_Service_Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Getter for configuration.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return Doozr_Form_Service_Configuration|null Configuration if set, otherwise NULL
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set input for parser.
     *
     * @param string $input The input to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * Getter for input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The input set, otherwise NULL if not set
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Setter for output.
     *
     * @param string $output The output to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * Getter for output.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string|null The output set, otherwise NULL if not set
     */
    public function getOutput()
    {
        return $this->output;
    }
}
