<?php

/**
 * SSSEO_Metadata_SiteConfig_DataExtension
 *
 * @todo add description
 *
 * @package SSSEO
 * @subpackage Main
 * @author Andrew Gerber <atari@graphiquesdigitale.net>
 * @version 1.0.0
 *
 * @todo lots
 *
 */

class SSSEO_Core_SiteConfig_DataExtension extends DataExtension {


	/* Overload Model
	------------------------------------------------------------------------------*/

	private static $db = array(

		//// Metadata Configuration
		// Charset
		'CharsetStatus' => 'Enum(array("off", "UTF-8", "ISO-8859-1"), "UTF-8")', // default: UTF-8
		// Canonical
		'CanonicalStatus' => 'Enum(array("off", "on"), "on")', // default: on
		// Title
		'TitleStatus' => 'Enum(array("off", "on"), "on")', // default: on
		// Favicon
		'FaviconStatus' => 'Enum(array("off", "on"), "on")', // default: on
		// Authorship
		'AuthorshipStatus' => 'Enum(array("off", "on"), "off")', // default: off
		// ExtraMeta
		'ExtraMetaStatus' => 'Enum(array("off", "on"), "off")', // default: off

		//// Metadata Values
		// Charset
		'Charset' => 'Enum(array("UTF-8"), "UTF-8")',
		// Title
		'Title' => 'Text', // redundant, but included for backwards-compatibility
		'TitleSeparator' => 'Varchar(1)',
		'Tagline' => 'Text', // redundant, but included for backwards-compatibility
		'TaglineSeparator' => 'Varchar(1)',
		'TitlePosition' => 'Enum(array("first", "last"), "first")',
		// Favicon
		'FaviconBG' => 'Varchar(6)',
		// Authorship
		'GoogleProfileID' => 'Varchar(128)',
		'FacebookProfileID' => 'Varchar(128)',

	);
	private static $has_one = array(
		// Favicon
		'FaviconPNG' => 'Image',
	);

	// Require Default Records
	// public function requireDefaultRecords() {
//
		// //
		// parent::requireDefaultRecords();
//
		// //
		// $data = ReconMetadataDataObject::get()->byID(1);
		// if (!$data) {
//
			// // create default object
			// $default = new ReconMetadataDataObject();
			// $default->MetaDescription = "Hello :)";
			// $id = $default->write();
//
			// //
			// DB::alteration_message("ReconMetadata -> default is = $id", "created");
//
			// // assign to $this
			// // need to get siteconfig statically to prevent database wierdness
			// $config = SiteConfig::current_site_config();
			// // $config->ReconMetadata()->add($default);
			// $config->ReconMetadataID = $id;
			// $config->write();
//
			// //
			// DB::alteration_message("ReconMetadata -> created default object", "created");
//
		// } else {
//
			// // ??
//
		// }
//
	// }


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// owner
		$owner = $this->owner;

		// SSSEO Tabset
		$fields->addFieldToTab('Root', new TabSet('SSSEO'));

		//// Configuration

		$tab = 'Root.SSSEO.Configuration';
		$fields->addFieldsToTab($tab, array(
			// Charset
			DropdownField::create('CharsetStatus', 'Character Set', $owner->dbObject('CharsetStatus')->enumValues())
				->setDescription('output: meta charset'),
			// Canonical
			DropdownField::create('CanonicalStatus', 'Canonical Pages', $owner->dbObject('CanonicalStatus')->enumValues())
				->setDescription('output: link rel="canonical"'),
			// Title
			DropdownField::create('TitleStatus', 'Title', $owner->dbObject('TitleStatus')->enumValues())
				->setDescription('output: meta title'),
			// Favicon
			DropdownField::create('FaviconStatus', 'Favicon', $owner->dbObject('FaviconStatus')->enumValues())
				->setDescription('enable enhanced PNG favicon output for modern browsers ...and IE'),
			// ExtraMeta
			DropdownField::create('AuthorshipStatus', 'Authorship', $owner->dbObject('AuthorshipStatus')->enumValues())
				->setDescription('enable authorship of pages'),
			// ExtraMeta
			DropdownField::create('ExtraMetaStatus', 'Custom Metadata', $owner->dbObject('ExtraMetaStatus')->enumValues())
				->setDescription('allow custom metadata on pages<br />please ensure metadata content is entity encoded!') // @todo entity encode content="%s"
		));

		//// Title

		if ($this->TitleEnabled()) {

			// remove
			// @todo move them, don't recreate them
// 			$fields->removeByName(array('Title', 'Tagline'));

			$tab = 'Root.SSSEO.Title';

			// add
			$fields->addFieldsToTab($tab, array(
				// Title
				TextField::create('Title', 'Title'),
				// TitleSeparator
				TextField::create('TitleSeparator', 'Title Separator')
					->setAttribute('placeholder', $this->titleSeparatorDefault())
					->setAttribute('size', 1)
					->setMaxLength(1)
					->setDescription('character limit: 1'),
				// Tagline
				TextField::create('Tagline', 'Tagline')
					->setDescription('optional'),
				// TaglineSeparator
				TextField::create('TaglineSeparator', 'Tagline Separator')
					->setAttribute('placeholder', $this->taglineSeparatorDefault())
					->setAttribute('size', 1)
					->setMaxLength(1)
					->setDescription('character limit: 1'),
				// TitlePosition
				DropdownField::create('TitlePosition', 'Title Position', $owner->dbObject('TitlePosition')->enumValues())
					->setDescription('first: <u>Title</u> | Page - Tagline' . '<br />' . 'last: Page - Tagline | <u>Title</u>')
			));
		}

		//// Favicon

		if ($this->FaviconEnabled()) {

			$tab = 'Root.SSSEO.Favicon';

			// ICO
			if (Director::fileExists('favicon.ico')) {
				$fields->addFieldsToTab($tab, array(
					ReadonlyField::create('ReadonlyFaviconICO', 'Favicon ICO', 'favicon.ico found')
						->addExtraClass('success')
				));
			} else {
				$fields->addFieldsToTab($tab, array(
					ReadonlyField::create('ReadonlyFaviconICO', 'Favicon ICO', 'favicon.ico not found')
						->addExtraClass('error')
				));
			}

			// PNG
			$fields->addFieldsToTab($tab, array(
				UploadField::create('FaviconPNG', 'Favicon PNG')
					->setAllowedExtensions(array('png'))
					->setFolderName('SSSEO/')
					->setDescription('file format: PNG' . '<br />' . 'pixel dimensions: 152 x 152'),
				TextField::create('FaviconBG', 'IE10 Tile Background')
					->setAttribute('placeholder', $this->faviconBGDefault())
					->setAttribute('size', 6)
					->setMaxLength(6)
					->setDescription('format: hexadecimal triplet<br />character limit: 6')
			));

		}

		//// Publisher

		if ($this->AuthorshipEnabled()) {

			$tab = 'Root.SSSEO.Publisher';

			// add fields
			$fields->addFieldsToTab($tab, array(
				TextField::create('GoogleProfileID', 'Google+ Profile ID'),
				TextField::create('FacebookProfileID', 'Facebook Profile ID')
			));



		}

	}


	/* Static Variables
	------------------------------------------------------------------------------*/

	private static $TitleSeparatorDefault = '|';
	private static $TaglineSeparatorDefault = '-';
	private static $FaviconBGDefault = 'FFFFFF';


	/* Static Accessors
	------------------------------------------------------------------------------*/

	//
	public function titleSeparatorDefault() {
		return self::$TitleSeparatorDefault;
	}

	//
	public function taglineSeparatorDefault() {
		return self::$TaglineSeparatorDefault;
	}

	//
	public function faviconBGDefault() {
		return self::$FaviconBGDefault;
	}


	/* Accessor Methods
	------------------------------------------------------------------------------*/

	//
	public function CharsetEnabled() {
		return ($this->owner->CharsetStatus == 'off') ? false : true;
	}

	//
	public function CanonicalEnabled() {
		return ($this->owner->CanonicalStatus == 'off') ? false : true;
	}

	//
	public function TitleEnabled() {
		return ($this->owner->TitleStatus == 'off') ? false : true;
	}

	//
	public function FaviconEnabled() {
		return ($this->owner->FaviconStatus == 'off') ? false : true;
	}

	//
	public function AuthorshipEnabled() {
		return ($this->owner->AuthorshipStatus == 'off') ? false : true;
	}

	public function ExtraMetaEnabled() {
		return ($this->owner->ExtraMetaStatus == 'off') ? false : true;
	}

}