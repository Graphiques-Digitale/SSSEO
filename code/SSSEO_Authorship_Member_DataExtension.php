<?php

/**
 * SSSEO_Authorship_SiteTree_DataExtension
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

class SSSEO_Authorship_Member_DataExtension extends DataExtension {

	/* Overload Variable
	 ------------------------------------------------------------------------------*/

	private static $db = array(
		'GooglePlusID' => 'Varchar(256)',
		'FacebookID' => 'Varchar(256)',
	);
	private static $has_many = array(
		'Authorship' => 'SiteTree',
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// SiteConfig
// 		$config = SiteConfig::current_site_config();

		// SSSEO Tabset
// 		$fields->addFieldToTab('Root', new TabSet('SSSEO'));

		// Author
		$tab = 'Root.Authorship';
// 		$fields->removeByName(array('Authorship'));

		//// Authorship
// 		if ($config->AuthorshipEnabled()) {

			// Author
			$fields->addFieldsToTab($tab, array(
				TextField::create('GooglePlusID', 'Google+ Profile ID'),
				TextField::create('FacebookID', 'Facebook Profile ID'),
// 				GridField::create('Authorship', 'Authorship', $this->owner->Authorship())
			), 'Authorship');

// 		}

	}

}