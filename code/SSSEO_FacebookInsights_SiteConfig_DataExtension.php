<?php

/**
 * SSSEO_FacebookInsights_SiteConfig_DataExtension
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

class SSSEO_FacebookInsights_SiteConfig_DataExtension extends DataExtension {


	/* Overload Model
	------------------------------------------------------------------------------*/

	private static $db = array(
		'FacebookAppID' => 'Varchar(128)',
	);
	private static $has_many = array(
		'FacebookAdmins' => 'Member',
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		//// Facebook Insights

		// new tab
		$tab = 'Root.SSSEO.FacebookInsights';

		$fields->addFieldsToTab($tab, array(
			TextField::create('FacebookAppID', 'Facebook Application ID'),
			GridField::create('FacebookAdmins', 'Facebook Administrators', $this->owner->FacebookAdmins())
				->setConfig(GridFieldConfig_RelationEditor::create())
		));

	}


	/* Class Methods
	------------------------------------------------------------------------------*/

	//
	public function FacebookInsightsMetadata($page) {

		// variables
		$config = $this->owner;

		// Facebook App ID
		if ($config->FacebookAppID) {

			$metadata = $page->MarkupHeader('Facebook Insights');
			$metadata .= $page->MarkupFacebook('fb:app_id', $config->FacebookAppID, false);

			// Admins (if App ID)
			foreach ($config->FacebookAdmins() as $admin) {
				if ($admin->FacebookProfileID) {
					$metadata .= $page->MarkupFacebook('fb:admins', $admin->FacebookProfileID, false);
				}
			}

			return $metadata;

		}	return false;

	}

}