<?php

/**
 * SSSEO_LeftAndMain_DataExtension
 *
 * @todo add description
 *
 * @package SSSEO
 * @subpackage Main
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 * @todo lots
 *
 */

class SSSEO_LeftAndMain_DataExtension extends DataExtension
{

    //
    public function init()
    {

        // get the module root folder name based on the location of this file
        $moduleRoot = basename(dirname(dirname(__FILE__)));

        // include CSS for the CMS
        Requirements::css("$moduleRoot/css/LeftAndMain.css");
    }
}
