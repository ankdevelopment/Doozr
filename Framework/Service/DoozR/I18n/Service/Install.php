<?php


$i18n = null;


function _()
{
    $arguments = func_get_args();
    DoozR_I18n_Service::_($arguments);
}

function __()
{
    global $i18n;
    $arguments = func_get_args();
    /* @var $i18n DoozR_I18n_Service */
    $i18n = DoozR_Loader_Serviceloader::load('i18n');
    $i18n->__($arguments);
}

function ___()
{
    global $i18n;
    $arguments = func_get_args();
    /* @var $i18n DoozR_I18n_Service */
    $i18n = DoozR_Loader_Serviceloader::load('i18n');
    $i18n->___($arguments);
}
