<?php

/**
 * Class ReconMetadata_CMSMainExtension
 */

class ReconMetadata_CMSMainExtension extends DataExtension {

	//
	function init() {

		// get the module root folder name based on the location of this file
		$moduleRoot = basename(dirname(dirname(__FILE__)));

		// include CSS for the CMS
		Requirements::css("$moduleRoot/css/CMSMain.css");

	}

}