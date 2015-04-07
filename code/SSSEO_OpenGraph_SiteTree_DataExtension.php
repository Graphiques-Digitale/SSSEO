<?php

/**
 * SSSEO_OpenGraph_SiteTree_DataExtension
 *
 * @todo add description
 *
 * @namespace SSSEO
 * @package Open Graph
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 * @todo lots
 *
 */

class SSSEO_OpenGraph_SiteTree_DataExtension extends DataExtension {

	//// Model Variables
	private static $db = array(
		// HTML
		'OpenGraphType' => 'Enum(array("off", "article"), "off")',
		'OpenGraphTitle' => 'Text',
		'OpenGraphDescription' => 'Text',
	);
	private static $has_one = array(
		'OpenGraphImage' => 'Image'
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		//
		$config = SiteConfig::current_site_config();

		// SSSEO Tabset
// 		$fields->addFieldToTab('Root', new TabSet('SSSEO'));

		// Open Graph tab
		$tab = 'Root.SSSEO.OpenGraph';

		//// Type
		$fields->addFieldsToTab($tab, array(
			DropdownField::create('OpenGraphType', 'og:type', $this->owner->dbObject('OpenGraphType')->enumValues()),
		));

		// if NOT off
		if ($this->owner->OpenGraphType != 'off') {
			//
			$fields->addFieldsToTab($tab, array(
				ReadonlyField::create('ReadonlyOpenGraphSiteName', 'og:site_name', $config->Title),
				TextField::create('OpenGraphTitle', 'og:title')
					->setAttribute('placeholder', $this->owner->Title),
				ReadonlyField::create('ReadonlyOpenGraphURL', 'og:url', $this->owner->AbsoluteLink()),
				TextareaField::create('OpenGraphDescription', 'og:description')
					->setAttribute('placeholder', $this->owner->MetaDescription),
				UploadField::create('OpenGraphImage', 'og:image')
					->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'))
					->setFolderName('SSSEO/OpenGraph/')
					->setDescription('file format: JPG, PNG, GIF<br />pixel dimensions: 1200 x 630')
			));
			//
// 			if ($this->owner->hasExtension('SSSEO_Authorship_SiteTree_DataExtension')
// 					&& $config->hasExtension('SSSEO_Authorship_SiteConfig_DataExtension')
// 					&& Member::has_extension('SSSEO_Authorship_Member_DataExtension'))
// 			{

// 			}
		} else {
			//
			$tabset = $fields->findOrMakeTab($tab);
			$tabset->addExtraClass('error');
		}

	}


	/* Template Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name OpenGraphMetadata
	 */
	public function OpenGraphMetadata() {

		if ($this->owner->OpenGraphType != 'off') {

			// variables
			$config = SiteConfig::current_site_config();
			$metadata = '<!-- Open Graph -->' . PHP_EOL;

			//// Type

			$metadata .= $this->Markup('og:type', $this->owner->OpenGraphType, false);

			//// Site Name

			$metadata .= $this->Markup('og:site_name', $config->Title, true);

			//// URL

			$metadata .= $this->Markup('og:url', $this->owner->AbsoluteLink(), false);

			//// Title

			$title = ($this->owner->OpenGraphTitle) ? $this->owner->OpenGraphTitle : $this->owner->Title;
			$metadata .= $this->Markup('og:title', $title, true);

			//// Description

			$description = ($this->owner->OpenGraphDescription) ? $this->owner->OpenGraphDescription : $this->owner->MetaDescription;
			$metadata .= $this->Markup('og:description', $description, true);

			//// Image

			if ($this->owner->OpenGraphImage()->exists()) {
				$metadata .= $this->Markup('og:image', $this->owner->OpenGraphImage()->getAbsoluteURL(), false);
			}

			//// fb:app_id

			//// og:locale

			//// article:author

			//// article:publisher

			// return
			return $metadata;

		} else {

			return false;

		}

	}


	/* Helper Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name Markup
	 */
	public function Markup($property, $content, Boolean $encode) {
		if ($encode) $content = htmlentities($content, ENT_QUOTES);
		return '<meta property="' . $property . '" content="' . $content . '" />' . PHP_EOL;
	}


	/* Class Methods
	------------------------------------------------------------------------------*/

	// none

}