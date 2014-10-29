<?php
/***********************************************************************************************************************
 *
* DEMONSTRATION
* Core: Error- and Exception-Handling
*
**********************************************************************************************************************/

/**
 * bootstrap
 */
require_once '../Framework/DoozR/Bootstrap.php';


/**
 * Instantiate DoozR
 */
$DoozR = DoozR_Core::getInstance();


/**
 * Show error handling example error is triggered but exception
 * is thrown and get catched afterwards
 */
try {
    // trigger an error manually
    trigger_error('Aloha @ '.microtime(), E_USER_ERROR);

} catch (Exception $e) {
    // throw a new DoozR Exception and look at the nice new exeption screen
    throw new DoozR_Exception($e->getMessage(), $e->getCode(), $e);

}
