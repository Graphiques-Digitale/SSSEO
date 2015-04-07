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

class SSSEO_Authorship_SiteTree_DataExtension extends DataExtension {

	/* Overload Variable
	 ------------------------------------------------------------------------------*/

	private static $has_one = array(
		'Author' => 'Member',
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
		$tab = 'Root.SSSEO.Authorship';

		//// Authorship
// 		if ($config->AuthorshipEnabled()) {

			// Author
			$fields->addFieldsToTab($tab, array(
				DropdownField::create('AuthorID', 'Author', Member::get()->map("ID", "Name"))
					->setEmptyString('None')
			));

// 		}

	}


	/* Template Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name Metadata
	 */
	public function AuthorshipMetadata() {

		// variables
		$config = SiteConfig::current_site_config();
		$metadata = '<!-- Authorship -->' . PHP_EOL;

		//// Authorship
		$author = $this->owner->Author();

		// Google Author
		if ($author->exists()) {
			// Google+
			if ($author->GooglePlusID) {
				$metadata .= '<link rel="author" href="https://plus.google.com/' . $this->owner->Author()->GooglePlusID . '/" />' . PHP_EOL;
			}
		}
		// Google Publisher
		if ($config->PublisherGooglePlusID) {
			$metadata .= '<link rel="publisher" href="https://plus.google.com/' . $config->PublisherGooglePlusID . '/" />' . PHP_EOL;
		}

		// Facebook Author
		if ($author->exists()) {
			// Facebook
			if ($this->owner->Author()->FacebookID) {
				$metadata .= '<meta property="article:author" content="' . $this->owner->Author()->FacebookID . '" />' . PHP_EOL;
			}
		}
		// Facebook Publisher
		if ($config->PublisherFacebookID) {
			$metadata .= '<meta property="article:publisher" content="' . $config->PublisherFacebookID . '" />' . PHP_EOL;
		}

		// return
		return $metadata;

	}


	/* Helper Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name Markup
	 */
// 	public function Markup($rel, $href) {
// 		return '<link rel="' . $rel . '" href="' . $href . '" />';
// 	}


	/* Meta Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name MetaCharset
	 */
// 	public function MetaCharset() {

		// variables
// 		$config = SiteConfig::current_site_config();

		//
// 		return $config->Charset;

// 	}

}