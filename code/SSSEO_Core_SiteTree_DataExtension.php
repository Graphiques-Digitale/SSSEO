<?php

/**
 * SSSEO_Metadata_SiteTree_DataExtension
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

class SSSEO_Core_SiteTree_DataExtension extends DataExtension {


	/* Overload Variable
	 ------------------------------------------------------------------------------*/

	private static $db = array(
		// HTML
		'MetaDescription' => 'Text', // redundant, but included for backwards-compatibility
		'ExtraMeta' => 'HTMLText', // redundant, but included for backwards-compatibility
	);
	private static $many_many = array(
		'Authors' => 'Member',
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// variables
		$config = SiteConfig::current_site_config();
		$self = $this->owner;

		// SSSEO Tabset
		$fields->addFieldToTab('Root', new TabSet('SSSEO'));

		// remove
		$fields->removeByName(array('Metadata'));

		//// Full Output

		$tab = 'Root.SSSEO.FullOutput';

		if ($self->hasExtension('SSSEO_SchemaDotOrg_SiteTree_DataExtension')) {
			if ($itemscope = $self->SchemaDotOrgItemscope()) {
				$fields->addFieldsToTab($tab, array(
					LiteralField::create('LiteralItemscope', '<pre style="font-weight: bold;">' . nl2br(htmlentities('<head' . $itemscope . '>')) . '</pre>')
				));
			}
		}
		$fields->addFieldsToTab($tab, array(
			LiteralField::create('LiteralFullOutput', '<pre>' . nl2br(htmlentities($self->Metadata(), ENT_QUOTES)) . '</pre>')
		));

		//// Metadata

		$tab = 'Root.SSSEO.Metadata';

		// MetaCanonical
		if ($config->CanonicalEnabled()) {
			$fields->addFieldsToTab($tab, array(
				ReadonlyField::create('ReadonlyMetaCanonical', 'link rel="canonical"', $self->MetaCanonical())
			));
		}

		// MetaTitle
		if ($config->TitleEnabled()) {
			$fields->addFieldsToTab($tab, array(
				ReadonlyField::create('ReadonlyMetaTitle', 'meta title', $self->MetaTitle())
			));
		}

		// MetaDescription
		$fields->addFieldsToTab($tab, array(
			TextareaField::create('MetaDescription', 'meta description')
				->setAttribute('placeholder', $self->MetaContent())
		));

		// ExtraMeta
		if ($config->ExtraMetaEnabled()) {
			$fields->addFieldsToTab($tab, array(
				TextareaField::create('ExtraMeta', 'Custom Metadata')
			));
		}

		//// Authorship

		$tab = 'Root.SSSEO.Authors';

		// Authors
		if ($config->AuthorshipEnabled()) {

			$fields->addFieldsToTab($tab, array(
			GridField::create('Authors', 'Authors', $self->Authors())
				->setConfig(GridFieldConfig_RelationEditor::create())
			));
		}

	}


	/* Template Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name Metadata
	 */
	public function Metadata() {

		// variables
		$config = SiteConfig::current_site_config();
		$self = $this->owner;
		$metadata = PHP_EOL . $self->MarkupHeader('SSSEO');

		//// Basic
		$metadata .= $self->MarkupHeader('HTML');

		// Charset
		if ($config->CharsetEnabled()) {
			$metadata .= '<meta charset="' . $config->Charset . '" />' . PHP_EOL;
		}

		// Canonical
		if ($config->CanonicalEnabled()) {
			$metadata .= $self->MarkupRel('canonical', $self->MetaCanonical());
		}

		// Title
		if ($config->TitleEnabled()) {
			$metadata .= '<title>' . $self->MetaTitle() . '</title>' . PHP_EOL;
		}

		// Description
		$metadata .= $self->Markup('description', $self->MetaDescription(), true, $config->Charset);

		// Favicon
		if ($config->FaviconEnabled()) {

			$ico = Director::fileExists('favicon.ico');

			// PNG + ICO
			if ($config->FaviconPNG()->exists()) {

				//
				$pngURL = $config->FaviconPNG()->SetSize(152, 152)->getAbsoluteURL();
				$pngBG = ($config->FaviconBG) ? $config->FaviconBG : $config->faviconBGDefault();

				//
				$metadata .= $self->MarkupHeader('Favicon');

				// 1. favicon.png
				$metadata .= $self->MarkupRel('icon', $pngURL);

				// 2. favicon.ico
				if ($ico) {
					// IE all-but-10
					$metadata .= '<!--[if IE]><link rel="shortcut icon" href="/favicon.ico" /><![endif]-->' . PHP_EOL;
				}

				// IE 10
				$metadata .= $self->Markup('msapplication-TileColor', $pngBG, false);
				$metadata .= $self->Markup('msapplication-TileImage', $pngURL, false);

			}

			// ICO only
			else {
				if ($ico) {
				$metadata .= $self->MarkupHeader('Favicon');
					$metadata .= $self->MarkupRel('shortcut icon', '/favicon.ico');
				}
			}

		}

		//// Touch Icon

		if ($config->hasExtension('SSSEO_TouchIcon_SiteConfig_DataExtension')) {
			$metadata .= $config->TouchIconMetadata($self);
		}

		//// Facebook Insights

		if ($config->hasExtension('SSSEO_FacebookInsights_SiteConfig_DataExtension')) {
			$metadata .= $config->FacebookInsightsMetadata($self);
		}

		//// Open Graph

		if ($self->hasExtension('SSSEO_OpenGraph_SiteTree_DataExtension')) {
			$metadata .= $self->OpenGraphMetadata();
		}

		// Facebook Authorship
		if ($config->AuthorshipEnabled()) {

			$authors = $self->Authors();
			$metadata .= $self->MarkupHeader('Facebook Authorship');

			// Facebook Authors
			foreach ($authors as $author) {
				if ($author->FacebookProfileID) {
					$metadata .= $self->MarkupFacebook('article:author', $author->FacebookProfileID, false);
				}
			}

			// Facebook Publisher
			if ($config->FacebookProfileID) {
				$metadata .= $self->MarkupFacebook('article:publisher', $config->FacebookProfileID, false);
			}

		}

		//// Twitter Cards

		if ($self->hasExtension('SSSEO_TwitterCards_SiteTree_DataExtension')) {
			$metadata .= $self->TwitterCardsMetadata();
		}

		//// Schema.org

		if ($self->hasExtension('SSSEO_SchemaDotOrg_SiteTree_DataExtension')) {
			$metadata .= $self->SchemaDotOrgMetadata();
		}

		// Google+ Authorship
		if ($config->AuthorshipEnabled()) {

			$authors = $self->Authors();
			$metadata .= $self->MarkupHeader('Google+ Authorship');

			// Google+ Authors
			foreach ($authors as $author) {
				if ($author->GoogleProfileID) {
					$profile = 'https://plus.google.com/' . $author->GoogleProfileID . '/';
					$metadata .= $self->MarkupRel('author', $profile);
					// @todo kinda - Google+ does not support multiple authors - break loop
					break;
				}

			}

			// Google+ Publisher
			if ($config->GoogleProfileID) {
				$profile = 'https://plus.google.com/' . $config->GoogleProfileID . '/';
				$metadata .= $self->MarkupRel('publisher', $profile);
			}

		}

		//// ExtraMeta

		if ($config->ExtraMetaEnabled()) {
			if ($extraMeta = $this->MetaExtraMeta()) {
				$metadata .= $self->MarkupHeader('Extra Metadata');
				$metadata .= $this->MetaExtraMeta() . PHP_EOL;
			}
		}

		// end
		$metadata .= $self->MarkupHeader('end SSSEO');

		// return
		return $metadata;

	}


	/* Helper Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name Markup (basic)
	 */
	public function Markup($name, $content, $encode, $charset = 'UTF-8') {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $charset);
		// return
		return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
	}

	/**
	 * @name Markup Header
	 */
	public function MarkupHeader($title) {
		// return
		return '<!-- ' . $title . ' -->' . PHP_EOL;
	}

	/**
	 * @name Markup Rel
	 */
	public function MarkupRel($rel, $href, $type = null) {
		if ($type) {
			return '<link rel="' . $rel . '" href="' . $href . '" type="' . $type . '" />' . PHP_EOL;
		} else {
			return '<link rel="' . $rel . '" href="' . $href . '" />' . PHP_EOL;
		}
	}

	/**
	 * @name Markup Facebook
	 */
	public function MarkupFacebook($property, $content, $encode, $charset = 'UTF-8') {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $charset);
		//
		return '<meta property="' . $property . '" content="' . $content . '" />' . PHP_EOL;
	}

	/**
	 * @name Markup Twitter
	 */
	public function MarkupTwitter($name, $content, $encode, $charset = 'UTF-8') {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $charset);
		// return
		return '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;
	}

	/**
	 * @name Markup Schema
	 */
	public function MarkupSchema($itemprop, $content, $encode, $charset = 'UTF-8') {
		// encode content
		if ($encode) $content = htmlentities($content, ENT_QUOTES, $charset);
		// return
		return '<meta itemprop="' . $itemprop . '" content="' . $content . '" />' . PHP_EOL;
	}


	/* Meta Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name MetaCharset
	 */
	public function MetaCharset() {

		// variables
		$config = SiteConfig::current_site_config();

		//
		return $config->Charset;

	}

	/**
	 * @name MetaCanonical
	 */
	public function MetaCanonical() {

		return $this->owner->AbsoluteLink();

	}

	/**
	 * @name MetaTitle
	 * default limit: 70 characters
	 */
	public function MetaTitle($length = 70) {

		// variables
		$config = SiteConfig::current_site_config();

		// collect title parts
		$titles = array();
		// Title WHERE TitlePosition = first
		if ($config->TitlePosition == 'first' && $config->Title) {
			$titleSeparator = ($config->TitleSeparator) ? $config->TitleSeparator : $config->titleSeparatorDefault();
			array_push($titles, $config->Title);
			array_push($titles, $titleSeparator);
		}
		// Title
		if ($this->owner->Title) {
			array_push($titles, $this->owner->Title);
		}
		// Tagline
		if ($config->Tagline) {
			$taglineSeparator = ($config->TaglineSeparator) ? $config->TaglineSeparator : $config->taglineSeparatorDefault();
			array_push($titles, $taglineSeparator);
			array_push($titles, $config->Tagline);
		}
		// Title WHERE TitlePosition = last
		if ($config->TitlePosition == 'last' && $config->Title) {
			$titleSeparator = ($config->TitleSeparator) ? $config->TitleSeparator : $config->titleSeparatorDefault();
			array_push($titles, $titleSeparator);
			array_push($titles, $config->Title);
		}

		// implode to create title
		$title = implode(' ', $titles);

		// return
// 			return substr($title, 0, $length);
		return $title;

	}

	/**
	 * @name MetaContent
	 * no limit
	 *
	 * returns first paragraph of page content
	 */
	public function MetaContent() {

		//
		$content = null;

		// content
		if ($content = trim($this->owner->Content)) {
			if (preg_match( '/<p>(.*?)<\/p>/i', $content, $match)) {
				$content = $match[0];
			} else {
				$content = explode("\n", $content);
				$content = $content[0];
			}
		}

		// return
		if ($content) {
			// found - strip & decode value
			return html_entity_decode(strip_tags($content));
		} else {
			// not found
			return false;
		}

	}

	/**
	 * @name MetaDescription
	 * default limit: 155 characters
	 */
	public function MetaDescription($length = 155) {

		$description = null;

		//
		if ($this->owner->MetaDescription) {
			$description = $this->owner->MetaDescription;
		} else {
			$description = $this->MetaContent();
		}

		// return
		if ($description) {
			// found - truncate value
// 			return substr($description, 0, $length);
			return $description;
		} else {
			// not found
			return false;
		}

	}

	/**
	 * @name MetaExtraMeta
	 */
	public function MetaExtraMeta() {

		return $this->owner->ExtraMeta;

	}

}