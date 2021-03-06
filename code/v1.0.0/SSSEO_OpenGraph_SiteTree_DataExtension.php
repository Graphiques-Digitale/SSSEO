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
		'OpenGraphType' => 'Enum(array("off", "article"), "article")',
		'OpenGraphTitle' => 'Text',
		'OpenGraphDescription' => 'Text',
	);
	private static $has_one = array(
		'OpenGraphImage' => 'Image',
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// vars
		$config = SiteConfig::current_site_config();
		$self = $this->owner;
		$tab = 'Root.SSSEO.OpenGraph';

		//// Type
		$fields->addFieldsToTab($tab, array(
			DropdownField::create('OpenGraphType', 'og:type', $self->dbObject('OpenGraphType')->enumValues()),
		));

		// if NOT off
		if ($self->OpenGraphType != 'off') {
			//
			$fields->addFieldsToTab($tab, array(
				ReadonlyField::create('ReadonlyOpenGraphSiteName', 'og:site_name', $config->Title),
				TextField::create('OpenGraphTitle', 'og:title')
					->setAttribute('placeholder', $self->Title),
				ReadonlyField::create('ReadonlyOpenGraphURL', 'og:url', $self->AbsoluteLink()),
				TextareaField::create('OpenGraphDescription', 'og:description')
					->setAttribute('placeholder', $self->GenerateDescription()),
				UploadField::create('OpenGraphImage', 'og:image')
					->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'))
					->setFolderName('SSSEO/OpenGraph/')
					->setDescription('file format: JPG, PNG, GIF<br />pixel dimensions: 1200 x 630')
			));
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

		$self = $this->owner;

		if ($self->OpenGraphType != 'off') {

			// variables
			$config = SiteConfig::current_site_config();
			$metadata = $self->MarkupHeader('Open Graph');

			//// Type

			$metadata .= $self->MarkupFacebook('og:type', $self->OpenGraphType, false);

			//// Site Name

			$metadata .= $self->MarkupFacebook('og:site_name', $config->Title, true, $config->Charset);

			//// URL

			$metadata .= $self->MarkupFacebook('og:url', $self->AbsoluteLink(), false);

			//// Title

			$title = ($self->OpenGraphTitle) ? $self->OpenGraphTitle : $self->Title;
			$metadata .= $self->MarkupFacebook('og:title', $title, true, $config->Charset);

			//// Description

			$description = ($self->OpenGraphDescription) ? $self->OpenGraphDescription : $self->GenerateDescription();
			$metadata .= $self->MarkupFacebook('og:description', $description, true, $config->Charset);

			//// Image

			if ($self->OpenGraphImage()->exists()) {
				$metadata .= $self->MarkupFacebook('og:image', $self->OpenGraphImage()->getAbsoluteURL(), false);
			}

			//// og:locale

			//// article:author
			// in Core

			//// article:publisher
			// in Core

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