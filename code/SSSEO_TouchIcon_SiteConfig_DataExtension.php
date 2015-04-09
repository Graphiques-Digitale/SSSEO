<?php

/**
 * SSSEO_TouchIcon_SiteConfig_DataExtension
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

class SSSEO_TouchIcon_SiteConfig_DataExtension extends DataExtension {


	/* Overload Model
	------------------------------------------------------------------------------*/

	private static $db = array(
// 		'AppleTouchIconPrecomposed' => 'Boolean',
	);
	private static $has_one = array(
		'TouchIconImage' => 'Image',
	);


	/* Overload Methods
	------------------------------------------------------------------------------*/

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// vars
		$config = SiteConfig::current_site_config();
		$self = $this->owner;
		$tab = 'Root.SSSEO.TouchIcon';

		//
		$fields->addFieldsToTab($tab, array(
			ReadonlyField::create('AppleTouchIconPrecomposed', 'apple-touch-icon-precomposed', 'on'),
			UploadField::create('TouchIconImage', 'Touch Icon Image')
				->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'))
				->setFolderName('SSSEO/TouchIcon/')
				->setDescription('file format: JPG, PNG, GIF<br />pixel dimensions: 400 x 400 (recommended, minimum 192)<br />pixel ratio: 1:1')
		));

	}


	/* Template Methods
	------------------------------------------------------------------------------*/

	/**
	 * @name TouchIconMetadata
	 * outputs Twitter metadata
	 */
	public function TouchIconMetadata() {

		$self = $this->owner;
		$image = $self->TouchIconImage();

		if ($image->exists()) {

			// variables
			$config = SiteConfig::current_site_config();
			$metadata = '<!-- Touch Icon -->' . PHP_EOL;

			// 192 x 192
// 			$image192 = $image->SetSize(192, 192)->getAbsoluteURL();
// 			$metadata .= '<!-- For Chrome for Android: -->';
			$metadata .= '<link rel="icon" sizes="192x192" href="' . $image->SetSize(192, 192)->getAbsoluteURL() . '">' . PHP_EOL;

			// 180 x 180
// 			$image180 = $image->SetSize(180, 180)->getAbsoluteURL();
// 			$metadata .= '<!-- For iPhone 6 Plus with @3× display: -->';
			$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="180x180" href="' . $image->SetSize(180, 180)->getAbsoluteURL() . '">' . PHP_EOL;

			// 152 x 152
// 			$image152 = $image->SetSize(152, 152)->getAbsoluteURL();
// 			$metadata .= '<!-- For iPad with @2× display running iOS ≥ 7: -->';
			$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="152x152" href="' . $image->SetSize(152, 152)->getAbsoluteURL() . '">' . PHP_EOL;

			// 144 x 144
// 			$image144 = $image->SetSize(144, 144)->getAbsoluteURL();
// 			$metadata .= '<!-- For iPad with @2× display running iOS ≤ 6: -->';
			$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="' . $image->SetSize(144, 144)->getAbsoluteURL() . '">' . PHP_EOL;

			// 120 x 120
// 			$image120 = $image->SetSize(120, 120)->getAbsoluteURL();
// 			$metadata .= '<!-- For iPhone with @2× display running iOS ≥ 7: -->';
			$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="120x120" href="' . $image->SetSize(120, 120)->getAbsoluteURL() . '">' . PHP_EOL;

			// 114 x 114
// 			$image114 = $image->SetSize(114, 114)->getAbsoluteURL();
// 			$metadata .= '<!-- For iPhone with @2× display running iOS ≤ 6: -->';
			$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="' . $image->SetSize(114, 114)->getAbsoluteURL() . '">' . PHP_EOL;

			// 76 x 76
// 			$image76 = $image->SetSize(76, 76)->getAbsoluteURL();
// 			$metadata .= '<!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≥ 7: -->';
			$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="76x76" href="' . $image->SetSize(76, 76)->getAbsoluteURL() . '">' . PHP_EOL;

			// 72 x 72
// 			$image72 = $image->SetSize(72, 72)->getAbsoluteURL();
// 			$metadata .= '<!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≤ 6: -->';
			$metadata .= '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="' . $image->SetSize(72, 72)->getAbsoluteURL() . '">' . PHP_EOL;

			// 57 x 57
// 			$image57 = $image->SetSize(57, 57)->getAbsoluteURL();
// 			$metadata .= '<!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->';
			$metadata .= '<link rel="apple-touch-icon-precomposed" href="' . $image->SetSize(57, 57)->getAbsoluteURL() . '"><!-- 57×57px -->' . PHP_EOL;

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