<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DoozR - Form - Service
 *
* Html.php - Generic renderable HTML component like <p>...</p> or
 * <span>...</span> ...
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

require_once DOOZR_DOCUMENT_ROOT . 'Service/DoozR/Form/Service/Component/Html/Html.php';

/**
 * DoozR - Form - Service
 *
 * Generic renderable HTML component like <p>...</p> or
 * <span>...</span> ...
 *
 * @category   DoozR
 * @package    DoozR_Service
 * @subpackage DoozR_Service_Form
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2005 - 2013 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       http://clickalicious.github.com/DoozR/
 * @abstract
 */
abstract class DoozR_Form_Service_Component_Html extends DoozR_Form_Service_Component_Html_Html
    implements
    Iterator
{
    /**
     * This is the tag-name for HTML output.
     * e.g. "input" or "form". Default empty string ""
     *
     * @var string
     * @access protected
     */
    protected $tag = DoozR_Form_Service_Constant::HTML_TAG_NONE;

    /**
     * Child components added to this component
     *
     * @var array
     * @access protected
     */
    protected $childs = array();

    /**
     * This is the pointer which points to the last
     * component in the loop.
     *
     * @var int
     * @access protected
     */
    protected $pointer = 0;

    /**
     * This is the type of the component
     *
     * @var string
     * @access protected
     */
    protected $type;

    /**
     * The attached renderer to render the component
     *
     * @var DoozR_Form_Service_Renderer_Interface
     * @access protected
     */
    protected $renderer;


    /*------------------------------------------------------------------------------------------------------------------
    | Public API
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param string                                 $tag      The tag name of the component
     * @param string                                 $template The template used for rendering component
     * @param DoozR_Form_Service_Renderer_Interface  $renderer A renderer instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return \DoozR_Form_Service_Component_Html
     * @access public
     */
    public function __construct(
        $tag      = null,
        $template = null,
        DoozR_Form_Service_Renderer_Interface $renderer = null
    ) {
        if ($tag !== null) {
            $this->tag = $tag;
        }

        if ($template !== null) {
            $this->template = $template;
        }

        if ($renderer !== null) {
            $this->setRenderer($renderer);
        }

        parent::__construct();
    }

    /**
     * Hook on default renderer for some slighty required modifications on input
     *
     * @param boolean $force TRUE to force rerendering, otherwise FALSE to use cached result
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The rendered result
     * @access public
     */
    public function render($force = false)
    {
        $template   = $this->getTemplate();
        $tag        = $this->getTag();
        $variables  = array();
        $childs     = $this->getChilds();
        $attributes = $this->getAttributes();
        $innerHtml  = $this->getInnerHtml();

        $result = $this->getRenderer()->render(
            $force,
            $template,
            $tag,
            $variables,
            $childs,
            $attributes,
            $innerHtml
        );

        return $result;
    }

    /**
     * Sets a renderer instance.
     *
     * @param DoozR_Form_Service_Renderer_Interface $renderer A renderer instance
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRenderer(DoozR_Form_Service_Renderer_Interface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Getter for renderer instance.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return DoozR_Form_Service_Renderer_Interface A renderer instance
     * @access public
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Magic shortcut to renderer
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The resulting HTML code
     * @access public
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Aplies the passed stylesheet/css string to the component
     *
     * @param string $style The style to apply
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setStyle($style)
    {
        $this->setAttribute('style', $style);
    }

    public function getStyle()
    {
        return $this->getAttribute('style');
    }

    /**
     * Adds a child to the component.
     *
     * @param DoozR_Form_Service_Component_Interface_Html $child A child component to add to component
     * @param string                                      $id    An id to used as index
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return integer The index of the child component
     * @access public
     */
    public function addChild(DoozR_Form_Service_Component_Interface_Html $child, $id = null)
    {
        $id = ($id === null) ? count($this->childs) : $id;

        $this->childs[$id] = $child;

        // Notify all attached components -> render again
        $this->notify();

        return $id;
    }

    /**
     * Removes a child from the component.
     *
     * @param integer $index The index of the child component to remove from component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean|null TRUE  if child was removed successfully,
     *                      FALSE if child could not be removed,
     *                      NULL  if child wasn't found
     * @access public
     */
    public function removeChild($index)
    {
        if (!isset($this->childs[$index])) {
            $result = null;

        } else {
            $result = (array_splice($this->childs, $index, 1) !== null);
        }

        // Notify all attached components -> render again
        $this->notify();

        return $result;
    }

    /**
     * Returns all attached childs
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array An array containing the attached childs
     * @access public
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Returns a child by passed Id.
     *
     * @param string $id The id to return child for
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The child
     * @access public
     */
    public function getChild($id)
    {
        return (isset($this->childs[$id]) ? $this->childs[$id] : null);
    }

    /**
     * Returns the parent status of a component.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return boolean TRUE if the component has childs, otherwise FALSE
     * @access public
     */
    public function hasChilds()
    {
        return (count($this->getChilds()) > 0);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Getter & Setter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for Id
     *
     * @param string $id The id to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setId($id)
    {
        $this->setAttribute('id', $id);
    }

    /**
     * Getter for Id
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The attribute id
     * @access public
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Setter for inner-HTML of the component
     *
     * @param string $html The HTML to set
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setInnerHtml($html)
    {
        $this->innerHtml = $html;
    }

    /**
     * Getter for inner-HTML.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The inner-HTML if set, otherwise NULL
     * @access public
     */
    public function getInnerHtml()
    {
        return $this->innerHtml;
    }

    /**
     * Setter for tag.
     *
     * @param string $tag The tag of this component
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * Getter for tag.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The tag if set, otherwise NULL
     * @access public
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Setter for arguments.
     *
     * @param array|DoozR_Request_Arguments $arguments The arguments
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Getter for arguments.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array|DoozR_Request_Arguments $arguments The arguments
     * @access public
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Setter for registry.
     *
     * @param array $registry The registry
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Getter for registry.
     *
     * @param string $key     The key to return from registry
     * @param mixed  $default The default value to return if key does not exist
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The value from registry if key passed, otherwise the whole registry
     * @access public
     */
    public function getRegistry($key = null, $default = null)
    {
        $result = $this->registry;

        if ($key !== null) {
            $result = (isset($result[$key])) ? $result[$key] : $default;
        }

        return $result;
    }

    /*-----------------------------------------------------------------------------------------------------------------*
     | Iterator Pattern Implementation
     *----------------------------------------------------------------------------------------------------------------*/

    /**
     * Returns the current element where
     * pointer points to.
     *
     * @return mixed|null
     * @access public
     */
    public function current()
    {
        return $this->childs[$this->pointer];
    }

    /**
     * Steps to next element
     *
     * @return void
     * @access public
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * Returns the current positions pointer
     *
     * @return integer The current position
     * @access public
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * Returns the validity of the current pointer
     * position as boolean (TRUE|FALSE)
     *
     * @return boolean TRUE of pointer is valid, otherwise FALSE
     * @access public
     */
    public function valid()
    {
        return $this->pointer < count($this->childs);
    }

    /**
     * Rewinds the pointer to position 0 (1st)
     *
     * @return void
     * @access public
     */
    public function rewind()
    {
        $this->pointer = 0;
    }
}
