<?php

/**
 * SSSEO_TwitterCards_SiteTree_DataExtension
 *
 * @todo add description
 *
 * @namespace SSSEO
 * @package Twitter Cards
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 * @todo lots
 *
 */

class SSSEO_TwitterCards_SiteTree_DataExtension extends DataExtension {


	/* Overload Model
	------------------------------------------------------------------------------*/

	private static $db = array(
		// HTML
		'TwitterCardsType' => 'Enum(array("off", "summary"), "off")',
		'TwitterCardsTitle' => 'Text',
		'TwitterCardsDescription' => 'Text',
	);
	private static $has_one = array(
		'TwitterCardsImage' => 'Image'
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// SiteConfig
		$config = SiteConfig::current_site_config();

		// SSSEO Tabset
// 		$fields->addFieldToTab('Root', new TabSet('SSSEO'));

		// Open Graph tab
		$tab = 'Root.SSSEO.TwitterCards';

		//// Type
		$fields->addFieldsToTab($tab, array(
			DropdownField::create('TwitterCardsType', 'twitter:card', $this->owner->dbObject('TwitterCardsType')->enumValues()),
		));

		// if NOT off
		if ($this->owner->TwitterCardsType != 'off') {
			//
			$fields->addFieldsToTab($tab, array(
				ReadonlyField::create('ReadonlyTwitterCardsSite', 'twitter:site', $config->Title),
				TextField::create('TwitterCardsTitle', 'twitter:title')
					->setAttribute('placeholder', $this->owner->Title),
				ReadonlyField::create('ReadonlyTwitterCardsURL', 'twitter:url', $this->owner->AbsoluteLink()),
				TextareaField::create('TwitterCardsDescription', 'twitter:description')
					->setAttribute('placeholder', $this->owner->MetaDescription),
				UploadField::create('TwitterCardsImage', 'twitter:image')
					->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'))
					->setFolderName('SSSEO/TwitterCards/')
					->setDescription('file format: JPG, PNG, GIF<br />pixel dimensions: 1200 x 630')
			));
		} else {
			// add error class
			$tabset = $fields->findOrMakeTab($tab);
			$tabset->addExtraClass('error');
		}

	}


	/* Template Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name TwitterCardsMetadata
	 * outputs Twitter metadata
	 */
	public function TwitterCardsMetadata() {

		if ($this->owner->TwitterCardsType != 'off') {

			// variables
			$config = SiteConfig::current_site_config();
			$metadata = '<!-- Twitter Cards -->' . PHP_EOL;

			//// Type

			$metadata .= $this->Markup('twitter:card', $this->owner->TwitterCardsType, false);

			//// Site Name

			$metadata .= $this->Markup('twitter:site', $config->Title, true);

			//// URL

			$metadata .= $this->Markup('twitter:url', $this->owner->AbsoluteLink(), false);

			//// Title

			$title = ($this->owner->TwitterCardsTitle) ? $this->owner->TwitterCardsTitle : $this->owner->Title;
			$metadata .= $this->Markup('twitter:title', $title, true);

			//// Description

			$description = ($this->owner->TwitterCardsDescription) ? $this->owner->TwitterCardsDescription : $this->owner->MetaDescription;
			$metadata .= $this->Markup('twitter:description', $description, true);

			//// Image

			if ($this->owner->TwitterCardsImage()->exists()) {
				$metadata .= $this->Markup('twitter:image', $this->owner->TwitterCardsImage()->getAbsoluteURL(), false);
			}

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
	public function Markup($name, $content, Boolean $encode) {
		if ($encode) $content = htmlentities($content, ENT_QUOTES);
		return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
	}


	/* Class Methods
	------------------------------------------------------------------------------*/

	// none

}