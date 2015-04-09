<?php

/**
 * SSSEO_FacebookInsights_SiteTree_DataExtension
 *
 * @todo add description
 *
 * @package SSSEO
 * @subpackage Facebook Insights
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 * @todo lots
 *
 */

class SSSEO_FacebookInsights_Member_DataExtension extends DataExtension {

	/* Overload Variable
	 ------------------------------------------------------------------------------*/

	private static $has_one = array(
		'FacebookAdmin' => 'SiteConfig'
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// SiteConfig
// 		$config = SiteConfig::current_site_config();

		// SSSEO Tabset
// 		$fields->findOrMakeTab('Root.SSSEO');
// 		$fields->addFieldToTab('Root', new TabSet('SSSEO'));

		//
		$tab = 'Root.SSSEO.Configuration';
		$fields->addFieldsToTab($tab, array(
// 			GridField::create('FacebookAdmin', 'Facebook Administrator', $this->owner->FacebookAdmin())
// 				->setConfig(GridFieldConfig_RelationEditor::create())
		));

	}

}