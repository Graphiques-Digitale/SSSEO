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

class SSSEO_SiteTree_DataExtension extends DataExtension {


	/* Overload Variable
	 ------------------------------------------------------------------------------*/

	private static $db = array(
		//
		'MetaTitle' => 'Varchar(128)',
		'MetaDescription' => 'Text', // redundant, but included for backwards-compatibility
		'ExtraMeta' => 'HTMLText', // redundant, but included for backwards-compatibility
		//
		'OpenGraphData' => 'Text',
		//
		'TwitterCardsData' => 'Text',
		//
		'SchemaDotOrgData' => 'Text',
	);
	private static $has_one = array(
		//
		'OpenGraphImage' => 'Image',
		//
		'TwitterCardsImage' => 'Image',
		//
		'SchemaDotOrgImage' => 'Image',
	);
	private static $many_many = array(
		//
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
			if ($head = $self->Metahead()) {
				$fields->addFieldsToTab($tab, array(
					LiteralField::create('HeaderMetahead', '<pre class="bold">$Metahead()</pre>'),
					LiteralField::create('LiteralMetahead', '<pre><span style="background-color: white;">' . htmlentities($head) . '</span></pre>')
				));
			}
		}
		$fields->addFieldsToTab($tab, array(
			LiteralField::create('HeaderMetadata', '<pre class="bold">$Metadata()</pre>'),
			LiteralField::create('LiteralMetadata', '<pre>' . nl2br(htmlentities(trim($self->Metadata()), ENT_QUOTES)) . '</pre>')
		));

		//// Metadata

		$tab = 'Root.SSSEO.Metadata';

		// Canonical
		if ($config->CanonicalEnabled()) {
			$fields->addFieldsToTab($tab, array(
				ReadonlyField::create('ReadonlyMetaCanonical', 'link rel="canonical"', $self->AbsoluteLink())
			));
		}

		// Title
		if ($config->TitleEnabled()) {
			$fields->addFieldsToTab($tab, array(
				TextField::create('MetaTitle', 'meta title')
					->setAttribute('placeholder', $self->GenerateTitle())
			));
		}

		// Description
		$fields->addFieldsToTab($tab, array(
			TextareaField::create('MetaDescription', 'meta description')
				->setAttribute('placeholder', $self->GenerateDescriptionFromContent())
		));

		// ExtraMeta
		if ($config->ExtraMetaEnabled()) {
			$fields->addFieldsToTab($tab, array(
				TextareaField::create('ExtraMeta', 'Custom Metadata')
			));
		}

		//// Open Graph

		if ($config->OpenGraphEnabled()) {

			$tab = 'Root.SSSEO.OpenGraph';

			$fields->addFieldsToTab($tab, array());

		}

		//// Twitter Cards

		if ($config->TwitterCardsEnabled()) {

			$tab = 'Root.SSSEO.TwitterCards';

			$fields->addFieldsToTab($tab, array());

		}

		//// Schema.org

		if ($config->SchemaDotOrgEnabled()) {

			$tab = 'Root.SSSEO.SchemaDotOrg';

			$fields->addFieldsToTab($tab, array());

		}

		//// Authorship

		// Authors
		if ($config->AuthorshipEnabled()) {

			$tab = 'Root.SSSEO.Authors';

			$fields->addFieldsToTab($tab, array(
			GridField::create('Authors', 'Authors', $self->Authors())
				->setConfig(GridFieldConfig_RelationEditor::create())
			));
		}

	}


	/* Template Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name Metahead
	 */
	public function Metahead() {

		$self = $this->owner;
		$metadata = '';

		//// Schema.org

		if ($self->hasExtension('SSSEO_SchemaDotOrg_SiteTree_DataExtension')) {
			$metadata .= $self->SchemaDotOrgItemscope();
		}

		return $metadata;

	}

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
			$metadata .= $self->MarkupRel('canonical', $self->AbsoluteLink());
		}

		// Title
		if ($config->TitleEnabled()) {

			// ternary operation
			$title = ($self->MetaTitle) ? $self->MetaTitle : $self->GenerateTitle();
			//
			$metadata .= '<title>' . htmlentities($title, ENT_QUOTES, $config->Charset) . '</title>' . PHP_EOL;

		}

		// Description
		$metadata .= $self->Markup('description', $self->GenerateDescription(), true, $config->Charset);

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

		if ($config->TouchIconEnabled()) {

			$image = $config->TouchIconImage();

			if ($image->exists()) {

				// variables
				$metadata .= $self->MarkupHeader('Touch Icon');

				// 192 x 192
// 				$metadata .= '<!-- For Chrome for Android: -->';
				$metadata .= '<link rel="icon" sizes="192x192" href="' . $image->SetSize(192, 192)->getAbsoluteURL() . '">' . PHP_EOL;

				// 180 x 180
// 				$metadata .= '<!-- For iPhone 6 Plus with @3× display: -->';
				$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="180x180" href="' . $image->SetSize(180, 180)->getAbsoluteURL() . '">' . PHP_EOL;

				// 152 x 152
// 				$metadata .= '<!-- For iPad with @2× display running iOS ≥ 7: -->';
				$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="152x152" href="' . $image->SetSize(152, 152)->getAbsoluteURL() . '">' . PHP_EOL;

				// 144 x 144
// 				$metadata .= '<!-- For iPad with @2× display running iOS ≤ 6: -->';
				$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="' . $image->SetSize(144, 144)->getAbsoluteURL() . '">' . PHP_EOL;

				// 120 x 120
// 				$metadata .= '<!-- For iPhone with @2× display running iOS ≥ 7: -->';
				$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="120x120" href="' . $image->SetSize(120, 120)->getAbsoluteURL() . '">' . PHP_EOL;

				// 114 x 114
// 				$metadata .= '<!-- For iPhone with @2× display running iOS ≤ 6: -->';
				$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="' . $image->SetSize(114, 114)->getAbsoluteURL() . '">' . PHP_EOL;

				// 76 x 76
// 				$metadata .= '<!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≥ 7: -->';
				$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="76x76" href="' . $image->SetSize(76, 76)->getAbsoluteURL() . '">' . PHP_EOL;

				// 72 x 72
// 				$metadata .= '<!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≤ 6: -->';
				$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="' . $image->SetSize(72, 72)->getAbsoluteURL() . '">' . PHP_EOL;

				// 57 x 57
// 				$metadata .= '<!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->';
				$metadata .= '<link rel="apple-touch-icon-precomposed" href="' . $image->SetSize(57, 57)->getAbsoluteURL() . '"><!-- 57×57px -->' . PHP_EOL;

			}

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

		//// Facebook Insights

		if ($config->FacebookInsightsEnabled()) {

			// Facebook App ID
			if ($config->FacebookAppID) {

				$metadata .= $self->MarkupHeader('Facebook Insights');
				$metadata .= $self->MarkupFacebook('fb:app_id', $config->FacebookAppID, false);

				// Admins (if App ID)
				foreach ($config->FacebookAdmins() as $admin) {
					if ($admin->FacebookProfileID) {
						$metadata .= $self->MarkupFacebook('fb:admins', $admin->FacebookProfileID, false);
					}
				}

			}

		}

		//// Open Graph

		if ($config->OpenGraphEnabled()) {
// 			$metadata .= $self->OpenGraphMetadata();
		}

		//// Twitter Cards

		if ($config->TwitterCardsEnabled()) {
// 			$metadata .= $self->TwitterCardsMetadata();
		}

		//// Schema.org

		if ($config->SchemaDotOrgEnabled()) {
// 			$metadata .= $self->SchemaDotOrgMetadata();
		}

		//// ExtraMeta

		if ($config->ExtraMetaEnabled()) {
			if ($extraMeta = $self->ExtraMeta != '') {
				$metadata .= $self->MarkupHeader('Extra Metadata');
				$metadata .= $self->ExtraMeta . PHP_EOL;
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
	 * @name MetaTitle
	 */
	public function GenerateTitle() {

		$self = $this->owner;

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
		if ($self->Title) {
			array_push($titles, $self->Title);
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
		return $title;

	}

	/**
	 * @name GenerateDescription
	 * default limit: 155 characters
	 */
	public function GenerateDescription() {

		//
		if ($this->owner->MetaDescription) {
			return $description = $this->owner->MetaDescription;
		} else {
			return $this->owner->GenerateDescriptionFromContent();
		}

	}

	/**
	 * @name GenerateDescription
	 * default limit: 155 characters
	 */
	public function GenerateDescriptionFromContent() {

		// pillage content
		if ($content = trim($this->owner->Content)) {
			if (preg_match( '/<p>(.*?)<\/p>/i', $content, $match)) {
				$content = $match[0];
			} else {
				$content = explode("\n", $content);
				$content = $content[0];
			}
			return trim(html_entity_decode(strip_tags($content)));
		} else {
			return false;
		}


	}

}