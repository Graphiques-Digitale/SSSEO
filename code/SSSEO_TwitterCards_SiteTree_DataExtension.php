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

		$self = $this->owner;

		if ($self->TwitterCardsType != 'off') {

			// variables
			$config = SiteConfig::current_site_config();
			$metadata = '<!-- Twitter Cards -->' . PHP_EOL;

			//// Type

			$metadata .= $self->MarkupTwitter('twitter:card', $self->TwitterCardsType, false);

			//// Site Name

			$metadata .= $self->MarkupTwitter('twitter:site', $config->Title, true);

			//// URL

			$metadata .= $self->MarkupTwitter('twitter:url', $self->AbsoluteLink(), false);

			//// Title

			// default to SiteTree::$Title
			$title = ($self->TwitterCardsTitle) ? $self->TwitterCardsTitle : $self->Title;
			$metadata .= $self->MarkupTwitter('twitter:title', $title, true);

			//// Description

			// default to SiteTree::$Description
			$description = ($self->TwitterCardsDescription) ? $self->TwitterCardsDescription : $self->MetaDescription;
			$metadata .= $self->MarkupTwitter('twitter:description', $description, true);

			//// Image

			if ($self->TwitterCardsImage()->exists()) {
				$metadata .= $self->MarkupTwitter('twitter:image', $self->TwitterCardsImage()->getAbsoluteURL(), false);
			}

			// return
			return $metadata;

		} else {

			return false;

		}

	}


	/* Class Methods
	------------------------------------------------------------------------------*/

	// none

}