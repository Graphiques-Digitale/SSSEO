<?php

/**
 * SSSEO_SchemaDotOrg_SiteTree_DataExtension
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

class SSSEO_SchemaDotOrg_SiteTree_DataExtension extends DataExtension {

	//// Model Variables
	private static $db = array(
		'SchemaDotOrgType' => 'Enum(array(
			"off",
			"Article",
			"Blog",
			"Book",
			"Event",
			"LocalBusiness",
			"Organisation",
			"Person",
			"Product",
			"Review",
			"Other"
		), "Article")',
		'SchemaDotOrgTitle' => 'Text',
		'SchemaDotOrgDescription' => 'Text',
	);
	private static $has_one = array(
		'SchemaDotOrgImage' => 'Image',
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// vars
		$config = SiteConfig::current_site_config();
		$self = $this->owner;
		$tab = 'Root.SSSEO.SchemaDotOrg';

		//// Type
		$fields->addFieldsToTab($tab, array(
			DropdownField::create('SchemaDotOrgType', 'itemscope itemtype', $self->dbObject('SchemaDotOrgType')->enumValues()),
		));

		// if NOT off
		if ($self->SchemaDotOrgType != 'off') {
			//
			$fields->addFieldsToTab($tab, array(
				TextField::create('SchemaDotOrgTitle', 'itemprop name')
					->setAttribute('placeholder', $self->Title),
				TextareaField::create('SchemaDotOrgDescription', 'itemprop description')
					->setAttribute('placeholder', $self->MetaDescription),
				UploadField::create('SchemaDotOrgImage', 'itemprop image')
					->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'))
					->setFolderName('SSSEO/SchemaDotOrg/')
					->setDescription('file format: JPG, PNG, GIF<br />pixel dimensions for thumbnail: < 400 px wide<br />pixel dimensions for full-bleed image >= 400 px wide<br />Please see <a href="https://developers.google.com/+/web/snippet/article-rendering">Article Rendering</a> for more information.')
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
	 * @name SchemaDotOrgItemscope
	 */
	public function SchemaDotOrgItemscope() {

		$self = $this->owner;

		if ($self->SchemaDotOrgType != 'off') {
			return ' itemscope itemtype="http://schema.org/' . $self->SchemaDotOrgType . '" ';
		}

	}

	/**
	 * @name SchemaDotOrgMetadata
	 */
	public function SchemaDotOrgMetadata() {

		$self = $this->owner;

		if ($self->SchemaDotOrgType != 'off') {

			// variables
			$config = SiteConfig::current_site_config();
			$metadata = '<!-- Schema.org -->' . PHP_EOL;

			//// Title

			$title = ($self->SchemaDotOrgTitle) ? $self->SchemaDotOrgTitle : $self->Title;
			$metadata .= $self->MarkupSchema('name', $title, true);

			//// Description

			$description = ($self->SchemaDotOrgDescription) ? $self->SchemaDotOrgDescription : $self->MetaDescription;
			$metadata .= $self->MarkupSchema('description', $description, true);

			//// Image

			if ($self->SchemaDotOrgImage()->exists()) {
				$metadata .= $self->MarkupSchema('image', $self->SchemaDotOrgImage()->getAbsoluteURL(), false);
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