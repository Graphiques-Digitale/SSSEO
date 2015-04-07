<?php

/**
 * SSSEO_Authorship_SiteConfig_DataExtension
 *
 * @todo add description
 *
 * @package SSSEO
 * @subpackage Authorship
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 * @todo lots
 *
 */

class SSSEO_Authorship_SiteConfig_DataExtension extends DataExtension {


	/* Overload Model
	------------------------------------------------------------------------------*/

	private static $db = array(

		// Publisher
		'PublisherGooglePlusID' => 'Varchar(256)',
		'PublisherFacebookID' => 'Varchar(256)'

	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// owner
		$owner = $this->owner;

		//// Authorship

// 		if ($this->AuthorshipEnabled()) {

			// Publisher
			$tab = 'Root.SSSEO.Publisher';

			$fields->addFieldsToTab($tab, array(
				TextField::create('PublisherGooglePlusID', 'Google+ Profile ID'),
				TextField::create('PublisherFacebookID', 'Facebook Profile ID')
			));

// 		}

	}


	/* Accessor Methods
	------------------------------------------------------------------------------*/

	//
// 	public function AuthorshipEnabled() {
// 		return ($this->owner->AuthorshipStatus == 'off') ? false : true;
// 	}

}