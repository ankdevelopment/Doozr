<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Doozr - Form - Service.
 *
 * Json.php - JSON Renderer. Renders a components setup to JSON.
 * Useful for example if you only need a skeleton for client side
 * Javascript e.g. AngularJS or some jQuery based projects.
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
require_once DOOZR_DOCUMENT_ROOT.'Service/Doozr/Form/Service/Renderer/Abstract.php';

/**
 * Doozr - Form - Service.
 *
 * JSON Renderer. Renders a components setup to JSON.
 * Useful for example if you only need a skeleton for client side
 * Javascript e.g. AngularJS or some jQuery based projects.
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
class Doozr_Form_Service_Renderer_Json extends Doozr_Form_Service_Renderer_Abstract
{
    /*------------------------------------------------------------------------------------------------------------------
    | PUBLIC API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Renders a passed templates with variables children and attributes.
     *
     * @param bool   $force      TRUE to force rerendering, FALSE to accept cached result
     * @param array  $template   The template to render
     * @param string $tag        The tag of the element to render
     * @param array  $variables  The variables to use for rendering
     * @param array  $children     The child elements
     * @param array  $attributes The attributes
     * @param string $innerJson
     *
     * @internal param $string %innerJson  The JSON of the component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return $this Instance for chaining
     */
    public function render(
        $force = false,
        $template = [],
        $tag = '',
        array $variables = [],
        array $children = [],
        array $attributes = [],
        $innerJson = ''
    ) {
        $json               = [];
        $json['tag']        = $tag;
        $json['children']     = [];
        $json['attributes'] = $attributes;

        //
        foreach ($children as $child) {
            $json['children'][] = $child->render()->get(false);
        }

        $this->rendered = $json;

        /* @var $this Doozr_Form_Service_Renderer_Json */
        return $this;
    }

    /**
     * Return JSON-encoded result! So we prevent double JSON encoding.
     *
     * @param bool $encode TRUE to return JSON encoded, FALSE to return unencoded
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The rendered result JSON encoded
     */
    public function get($encode = true)
    {
        return ($encode === true) ? json_encode($this->rendered) : $this->rendered;
    }

    /*-----------------------------------------------------------------------------------------------------------------+
    | Tools & Helper
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Prepares attributes.
     *
     * @param array $attributes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     *
     * @return string The rendered result
     */
    protected function prepareAttributes(array $attributes)
    {
        $attributesCollected = '';

        foreach ($attributes as $attribute => $value) {
            // Check value-less attributes to be embedded properly
            if ($value === null) {
                $attributesCollected .= ' '.$attribute;
            } else {
                $value = (is_array($value)) ? $value[0] : $value;
                $attributesCollected .= ' '.$attribute.'="'.$value.'"';
            }
        }

        return $attributesCollected;
    }
}
