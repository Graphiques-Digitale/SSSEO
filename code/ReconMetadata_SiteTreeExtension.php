<?php

/**
 * class ReconMetadataExtension
 */

class ReconMetadata_SiteTreeExtension extends DataExtension {

	//// Overload Class Variables
	private static $db = array(
		// HTML
		"MetaDescription" => "Text",
		"MetaKeywords" => "Text",
		// "MetaCustom" => "Text",
		// Open Graph
		"OpenGraphType" => "Enum(array('article'), 'article')",
		"OpenGraphTitle" => "Text",
		"OpenGraphDescription" => "Text",
		// Twitter Cards
		"TwitterCardsType" => "Enum(array('summary'), 'summary')",
		"TwitterCardsTitle" => "Text",
		"TwitterCardsDescription" => "Text",
		// Schema.org
		"SchemaDotOrgType" => "Enum(array('Article'), 'Article')",
		"SchemaDotOrgTitle" => "Text",
		"SchemaDotOrgDescription" => "Text",
		// Google Plus
		"GooglePlusAuthor" => "Text",
		"GooglePlusPublisher" => "Text",
		// Facebook App
		"FacebookAppID" => "Text",
		"FacebookAdmins" => "Text",
	);
	private static $has_one = array(
		//
		"OpenGraphImage" => "Image",
		"TwitterCardsImage" => "Image",
		"SchemaDotOrgImage" => "Image",
	);


	//// Overload Class Functions

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		// variables
		$config = SiteConfig::current_site_config();

		// check for fields
		if ($config->MetaDescriptionStatus ||
			$config->MetaKeywordStatus ||
			$config->OpenGraphStatus ||
			$config->TwitterCardsStatus ||
			$config->SchemaDotOrgStatus ||
			$config->GooglePlusStatus ||
			$config->FacebookAppStatus
		) {

			$metadata = new ArrayObject();
			$disabled = "[ disabled ]";
			$count = 1;

			// remove
			$fields->removeFieldsFromTab("Root.Main", array("Metadata"));

			/*
			 * Title
			 * @TODO add page tagline field support
			 */
			if ($config->TitleStatus) {

				//// Site Title
				// $metadata->append(HeaderField::create("TitleHeader", $count++ . ". Title", 2));
				// title
				$metadata->append(ReadonlyField::create("TitleExample", "Page Title", $this->generateTitle($config)));

			}

			/*
			 * Favicon
			 * @TODO add page favicon field support
			 */
/***
			if ($this->owner->FaviconStatus) {

				//// Favicon
				$metadata->append(HeaderField::create("FaviconHeader", $count++ . ". Favicon", 2));
				// image
				$title = "Icon File<br />[ 16 x 16 px ]";
				if ($this->owner->FaviconStatus == 0) {
					$metadata->append(ReadonlyField::create("FaviconMsg", "Favicon", $disabled));
				} else {
					$favicon = UploadField::create("Favicon", $title);
					$favicon->setFolderName("Metadata/Favicon");
					$favicon->setAllowedExtensions(array("ico"));
					$metadata->append($favicon);
				}

			}
***/

			//// @TODO
			//// working here
			//// fix images
			//// optimise locateParent()
			//// trim whitespace onBeforeWrite()
			////

			/*
			 * Metadata
			 */

			//
			$metadata->append(HeaderField::create("MetadataHeader", $count . ". Metadata", 2));

			//// HTML

			// header
			$metadata->append(HeaderField::create("HTMLHeader", $count . ".1. HTML", 3));

			// description
			if ($config->MetaDescriptionStatus) {
				// remove
				$fields->removeByName("MetaDescription");
				// list
				$metaDescriptionList = new FieldList(array(
					TextareaField::create("MetaDescription", "Meta Description<br />[ 155 chars ]"), // limit ??
					ReadonlyField::create("MetaDescriptionOutput", "meta name='description'", $this->retrieveMetaDescription($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$metaDescriptionHolder = CompositeField::create($metaDescriptionList)->setName("MetaDescriptionHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($metaDescriptionHolder);
			}

			// keywords
			if ($config->MetaKeywordsStatus) {
				// remove
				$fields->removeByName("MetaKeywords");
				// list
				$metaKeywordsList = new FieldList(array(
					TextareaField::create("MetaKeywords", "Meta Keywords<br />[ obsolete ]"),
					ReadonlyField::create("MetaKeywordsOutput", "meta name='keywords'", $this->retrieveMetaKeywords($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$metaKeywordsHolder = CompositeField::create($metaKeywordsList)->setName("MetaKeywordsHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($metaKeywordsHolder);
			}

			//// Open Graph

			//
			if ($config->OpenGraphStatus) {

				// header
				$metadata->append(HeaderField::create("OpenGraphHeader", $count . ".2. Open Graph", 3));

				// type
				// list
				$OGTypeList = new FieldList(array(
					DropDownField::create("OpenGraphType", "Type",  singleton("SiteConfig")->dbObject("OpenGraphType")->enumValues())->performDisabledTransformation(),
					ReadonlyField::create("OpenGraphTypeOutput", "meta property='og:type'", "article")->addExtraClass("ReconMetadataOutput")
				));
				// add
				$OGTypeHolder = CompositeField::create($OGTypeList)->setName("OpenGraphTypeHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($OGTypeHolder);

				// title
				// list
				$OGTitleList = new FieldList(array(
					TextField::create("OpenGraphTitle", "Title<br />[ 95 chars ]")->setMaxLength(95),
					ReadonlyField::create("OpenGraphTitleOutput", "meta property='og:title'", $this->retrieveOpenGraphTitle($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$OGTitleHolder = CompositeField::create($OGTitleList)->setName("OpenGraphTitleHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($OGTitleHolder);

				// description
				// list
				$OGDescriptionList = new FieldList(array(
					TextareaField::create("OpenGraphDescription", "Description<br />[ 297 chars ]"), // limit ??
					ReadonlyField::create("OpenGraphDescriptionOutput", "meta property='og:description'", $this->retrieveOpenGraphDescription($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$OGDescriptionHolder = CompositeField::create($OGDescriptionList)->setName("OpenGraphDescriptionHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($OGDescriptionHolder);

				// image
				$OGImage = UploadField::create("OpenGraphImage", "Image");
				$OGImage->setFolderName("Metadata/OpenGraph");
				$OGImage->setAllowedExtensions(array("jpg", "jpeg", "png", "gif", "bmp"));
				$OGImage->setDescription("- allowed file types: JPG, PNG, GIF, BMP<br />- recommended dimensions: 120 x 120 pixels<hr /><strong>Facebook says:</strong> Use images that are at least 1200 x 630 pixels to display large, beautiful stories on Facebook with high resolution devices. At the minimum, your images should be 600 x 315 pixels to display in the larger format. If your image is smaller than 600 x 315 px, it will still display in a link page post, but the size will be much smaller. Note that images are displayed in 1.91:1 aspect ratio across desktop and mobile News Feed.");
				// list
				$OGImageList = new FieldList(array(
					$OGImage,
					ReadonlyField::create("OpenGraphImageOutput", "meta property='og:image'", $this->retrieveOpenGraphImageURL($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$OGImageHolder = CompositeField::create($OGImageList)->setName("OpenGraphImageHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($OGImageHolder);

			}

			/**
			 * Twitter Cards
			 * @TODO image allowed extensions
			 * @TODO image description
			 */

			//
			if ($config->TwitterCardsStatus) {

				// header
				$metadata->append(HeaderField::create("TwitterCardsHeader", $count . ".3. Twitter Cards", 3));

				// type
				// list
				$TwitterTypeList = new FieldList(array(
					DropDownField::create("TwitterCardsType", "Type",  singleton("SiteConfig")->dbObject("TwitterCardsType")->enumValues())->performDisabledTransformation(),
					ReadonlyField::create("TwitterCardsTypeOutput", "meta name='twitter:card'", "summary")->addExtraClass("ReconMetadataOutput")
				));
				// add
				$TwitterTypeHolder = CompositeField::create($TwitterTypeList)->setName("TwitterCardsTypeHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($TwitterTypeHolder);

				// title
				// list
				$TwitterTitleList = new FieldList(array(
					TextField::create("TwitterCardsTitle", "Title<br />[ 70 chars ]")->setMaxLength(70),
					ReadonlyField::create("TwitterCardsTitleOutput", "meta name='twitter:title'", $this->retrieveTwitterCardsTitle($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$TwitterTitleHolder = CompositeField::create($TwitterTitleList)->setName("TwitterCardsTitleHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($TwitterTitleHolder);

				// description
				// list
				$TwitterDescriptionList = new FieldList(array(
					TextareaField::create("TwitterCardsDescription", "Description<br />[ 200 chars ]"),  // limit ??
					ReadonlyField::create("TiwtterCardsDescriptionOutput", "meta name='twitter:description'", $this->retrieveTwitterCardsDescription($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$TwitterDescriptionHolder = CompositeField::create($TwitterDescriptionList)->setName("TwitterCardsDescriptionHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($TwitterDescriptionHolder);

				// image
				$TwitterImage = UploadField::create("TwitterCardsImage", "Image");
				$TwitterImage->setFolderName("Metadata/TwitterCards");
				$TwitterImage->setAllowedExtensions(array("jpg", "jpeg", "png", "gif"));
				$TwitterImage->setDescription("- allowed file types: JPG, PNG, GIF<br />- recommended dimensions: 120 x 120 pixels<hr /><strong>Twitter says:</strong> URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. The image must be a minimum size of 120px by 120px and must be less than 1MB in file size. For an expanded tweet and its detail page, the image will be cropped to a 4:3 aspect ratio and resized to be displayed at 120px by 90px. The image will also be cropped and resized to 120px by 120px for use in embedded tweets.");
				// list
				$TwitterImageList = new FieldList(array(
					$TwitterImage,
					ReadonlyField::create("TwitterCardsImageOutput", "meta name='twitter:image'", $this->retrieveTwitterCardsImageURL($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$TwitterImageHolder = CompositeField::create($TwitterImageList)->setName("TwitterCardsImageHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($TwitterImageHolder);

			}

			//// Schema.org

			//
			if ($config->SchemaDotOrgStatus) {

				// header
				$metadata->append(HeaderField::create("SchemaDotOrgHeader", $count++ . ".4. Schema.org", 3));

				// type
				$SchemaTypeList = new FieldList(array(
					DropDownField::create("SchemaDotOrgType", "Type",  singleton("SiteConfig")->dbObject("SchemaDotOrgType")->enumValues())->performDisabledTransformation(),
					ReadonlyField::create("SchemaDotOrgTypeOutput", "itemscope", "Article")->addExtraClass("ReconMetadataOutput")
				));
				//
				$SchemaTypeHolder = CompositeField::create($SchemaTypeList)->setName("ScemaDotOrgType")->addExtraClass("ReconMetadataHolder");
				$metadata->append($SchemaTypeHolder);

				// title
				$SchemaTitleList = new FieldList(array(
					TextField::create("SchemaDotOrgTitle", "Title<br />[ 140 chars ]")->setMaxLength(140),
					ReadonlyField::create("SchemaDotOrgTitleOutput", "meta itemprop='name'", $this->retrieveSchemaDotOrgTitle($config))->addExtraClass("ReconMetadataOutput")
				));
				//
				$SchemaTitleHolder = CompositeField::create($SchemaTitleList)->setName("ScemaDotOrgTitle")->addExtraClass("ReconMetadataHolder");
				$metadata->append($SchemaTitleHolder);

				// description
				$SchemaDescriptionList = new FieldList(array(
					TextareaField::create("SchemaDotOrgDescription", "Description<br />[ 185 chars ]"), // limit ??
					ReadonlyField::create("SchemaDotOrgDescriptionOutput", "meta itemprop='description'", $this->retrieveSchemaDotOrgDescription($config))->addExtraClass("ReconMetadataOutput")
				));
				//
				$SchemaDescriptionHolder = CompositeField::create($SchemaDescriptionList)->setName("ScemaDotOrgDescription")->addExtraClass("ReconMetadataHolder");
				$metadata->append($SchemaDescriptionHolder);

				// image
				$SchemaImage = UploadField::create("SchemaDotOrgImage", "Image");
				$SchemaImage->setFolderName("Metadata/SchemaDotOrg");
				$SchemaImage->setAllowedExtensions(array("jpg", "jpeg", "png", "gif"));
				$SchemaImage->setDescription("- allowed file types: JPG, PNG, GIF<br />- recommended dimensions: 120 x 120 pixels<hr /><strong>Google says:</strong> Images that are too small or not square enough are not included in the +Snippet, even if the images are explicitly referenced by schema.org microdata or Open Graph markup. Specifically, the height must be at least 120px, and if the width is less than 100px, then the aspect ratio must be no greater than 3.0.");
				// list
				$SchemaImageList = new FieldList(array(
					$SchemaImage,
					ReadonlyField::create("SchemaDotOrgImageOutput", "meta itemprop='image'", $this->retrieveSchemaDotOrgImageURL($config))->addExtraClass("ReconMetadataOutput")
				));
				// add
				$SchemaImageHolder = CompositeField::create($SchemaImageList)->setName("SchemaDotOrgImageHolder")->addExtraClass("ReconMetadataHolder");
				$metadata->append($SchemaImageHolder);

			}

			//// Transmissions

			//
			$subcount = 1;

			//
			if ($config->GooglePlusStatus || $config->FacebookAppStatus) {

				//
				$metadata->append(HeaderField::create("HeaderTransmissions", $count . ". Transmissions", 2));

				// Google Plus
				if ($config->GooglePlusStatus) {

					//
					$metadata->append(HeaderField::create("GooglePlusHeader", $count . "." . $subcount++ . ". Google Plus", 3));

					// Google Plus Author
					$GoogleAuthorList = new FieldList(array(
						TextField::create("GooglePlusAuthor", "Google Plus Author ID"),
						ReadonlyField::create("GooglePlusAuthorOutput", "link rel='author'", $this->retrieveGooglePlusAuthor($config))->addExtraClass("ReconMetadataOutput")
					));
					//
					$GoogleAuthorHolder = CompositeField::create($GoogleAuthorList)->setName("GooglePlusAuthorHolder")->addExtraClass("ReconMetadataHolder");
					$metadata->append($GoogleAuthorHolder);

					// Publisher Google ID
					$GooglePublisherList = new FieldList(array(
						TextField::create("GooglePlusPublisher", "Google Plus Publisher ID"),
						ReadonlyField::create("GooglePlusPublisherOutput", "link rel='publisher'", $this->retrieveGooglePlusPublisher($config))->addExtraClass("ReconMetadataOutput")
					));
					//
					$GooglePublisherHolder = CompositeField::create($GooglePublisherList)->setName("GooglePlusPublisherHolder")->addExtraClass("ReconMetadataHolder");
					$metadata->append($GooglePublisherHolder);

				}

				// Facebook App
				if ($config->FacebookAppStatus) {

					//
					$metadata->append(HeaderField::create("FacebookAppHeader", $count . "." . $subcount . ". Facebook Application", 3));

					// Facebook App ID
					$FacebookAppIDList = new FieldList(array(
						TextField::create("FacebookAppID", "App ID"),
						ReadonlyField::create("FacebookAppIDOutput", "meta property='fb:app_id'", $this->retrieveFacebookAppID($config))->addExtraClass("ReconMetadataOutput")
					));
					//
					$FacebookAppIDHolder = CompositeField::create($FacebookAppIDList)->setName("FacebookAppIDHolder")->addExtraClass("ReconMetadataHolder");
					$metadata->append($FacebookAppIDHolder);

					// Facebook Admins
					$FacebookAdminsList = new FieldList(array(
						TextField::create("FacebookAdmins", "Admins"),
						ReadonlyField::create("FacebookAdminsOutput", "meta property='fb:admins'", $this->retrieveFacebookAdmins($config))->addExtraClass("ReconMetadataOutput")
					));
					//
					$FacebookAdminsHolder = CompositeField::create($FacebookAdminsList)->setName("FacebookAdminsHolder")->addExtraClass("ReconMetadataHolder");
					$metadata->append($FacebookAdminsHolder);

				}

			}

			// add
			if ($metadata->count() > 0) {
				$fields->addFieldsToTab("Root.ReconMetadata", iterator_to_array($metadata));
			}

		} // else nothing

	}

	/**
	 * Class Functions
	 */

	//
	public function locateParent() {
		if ($this->owner->ParentID != 0) {
			return $this->owner->Parent();
		} else {
			return SiteConfig::current_site_config();
		}
	}

	/**
	 * Generate Title
	 * default limit: 70 characters
	 */
	public function generateTitle($config, $length = 70) {

		// variables
		$config = SiteConfig::current_site_config();

		// collect parts
		$titleArray = array();
		if ($this->owner->Title) array_push($titleArray, $this->owner->Title);
		if ($config->Title && $config->Title != "Your Site Name") array_push($titleArray, $config->Title);
		if ($config->Tagline && $config->Tagline != "your tagline here") array_push($titleArray, $config->Tagline);

		// implode to create title
		$separator = ($config->TitleSeparator) ? " {$config->TitleSeparator} " : " | ";
		$title = implode($separator, $titleArray);

		// return
		return substr($title, 0, $length);

	}

	/**
	 * Retrieve Content
	 * no limit
	 */
	public function retrieveContent() {

		//
		$value = null;

		// content
		if ($content = trim($this->owner->Content)) {
			if (preg_match( '/<p>(.*?)<\/p>/i', $content, $match)) {
				$value = $match[0];
			} else {
				$value = explode("\n", $content);
				$value = $value[0];
			}
		}

		// return
		if ($value) {
			// found - strip & decode value
			return html_entity_decode(strip_tags($value));
		} else {
			// not found
			return false;
		}

	}

	/**
	 * Retrieve Meta Description
	 * default limit: 155 characters
	 */
	public function retrieveMetaDescription($config, $length = 155) {

		//
		$value = null;

		// switch inheritance
		if (trim($this->owner->MetaDescription)) {
			$value = trim($this->owner->MetaDescription);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->MetaDescription)) {
					$value = trim($parent->MetaDescription);
				} else {
					// else return content summary
					$value = $this->retrieveContent($length);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while ($parent = $parent->locateParent()) {
				if (trim($parent->MetaDescription)) {
					$value = trim($parent->MetaDescription);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
			if (trim($config->MetaDescription)) {
				$value = trim($config->MetaDescription);
			}
		}

		// return
		if ($value) {
			// found - truncate value
			return substr($value, 0, $length);
		} else {
			// not found
			return false;
		}

	}

	/**
	 * Retrieve Meta Keywords
	 * no limit
	 */
	public function retrieveMetaKeywords($config) {

		//
		$value = null;

		//
		if (trim($this->owner->MetaKeywords)) {
			$value = trim($this->owner->MetaKeywords);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->MetaKeywords)) {
					$value = trim($parent->MetaKeywords);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->MetaKeywords)) {
					$value = trim($parent->MetaKeywords);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->MetaKeywords)) {
					$value = trim($config->MetaKeywords);
				}
		}

		// return
		if ($value) {
			// found
			return substr($value, 0);
		} else {
			// not found
			return false;
		}

	}

	//// Open Graph

	/**
	 * Retrieve Open Graph Title
	 * default limit: 95 characters
	 */
	public function retrieveOpenGraphTitle($config, $length = 95) {

		//
		$value = null;

		//
		if (trim($this->owner->OpenGraphTitle)) {
			$value = trim($this->owner->OpenGraphTitle);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->OpenGraphTitle)) {
					$value = trim($parent->OpenGraphTitle);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->OpenGraphTitle)) {
					$value = trim($parent->OpenGraphTitle);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->OpenGraphTitle)) {
					$value = trim($config->OpenGraphTitle);
				}
		}

		// return
		if ($value) {
			// found - truncate
			return substr($value, 0, $length);
		} else {
			// not found
			return false;
		}

	}

	/**
	 * Retrieve Open Graph Description
	 * default limit: 297 characters
	 */
	public function retrieveOpenGraphDescription($config, $length = 297) {

		//
		$value = null;

		//
		if (trim($this->owner->OpenGraphDescription)) {
			$value = trim($this->owner->OpenGraphDescription);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->OpenGraphDescription)) {
					$value = trim($parent->OpenGraphDescription);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->OpenGraphDescription)) {
					$value = trim($parent->OpenGraphDescription);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->OpenGraphDescription)) {
					$value = trim($config->OpenGraphDescription);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0, $length);
		} else {
			// not found
			return false;
		}

	}

	/**
	 * Retrieve Open Graph Image
	 */
	public function retrieveOpenGraphImage($config) {

		//
		if ($this->owner->OpenGraphImage()->exists()) {
			return $this->owner->OpenGraphImage();
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if ($parent->OpenGraphImage()->exists()) {
					return $parent->OpenGraphImage();
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while ($parent = $parent->locateParent()) {
				if ($parent->OpenGraphImage()->exists()) {
					return $parent->OpenGraphImage();
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
			if ($config->OpenGraphImage()->exists()) {
				return $config->OpenGraphImage();
			}
		}
		// otherwise
		return false;

	}

	/**
	 * Retrieve Open Graph Image URL (for use in CMS fields)
	 * @TODO rather return thumbnail
	 */
	public function retrieveOpenGraphImageURL($config) {

		if ($image = $this->retrieveOpenGraphImage($config)) {
			return $image->getAbsoluteURL();
		} else {
			return false;
		}

	}

	//// Twitter Cards

	/**
	 * Retrieve Twitter Cards Title
	 * default limit: 70 characters
	 */
	public function retrieveTwitterCardsTitle($config, $length = 70) {

		//
		$value = null;

		//
		if (trim($this->owner->TwitterCardsTitle)) {
			$value = trim($this->owner->TwitterCardsTitle);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->TwitterCardsTitle)) {
					$value = trim($parent->TwitterCardsTitle);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while ($parent = $parent->locateParent()) {
				if (trim($parent->TwitterCardsTitle)) {
					$value = trim($parent->TwitterCardsTitle);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->TwitterCardsTitle)) {
					$value = trim($config->TwitterCardsTitle);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0, $length);
		} else {
			// not found
			return false;
		}

	}

	//
	public function retrieveTwitterCardsDescription($config, $length = 200) {

		//
		$value = null;

		//
		if (trim($this->owner->TwitterCardsDescription)) {
			$value = trim($this->owner->TwitterCardsDescription);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->TwitterCardsDescription)) {
					$value = trim($parent->TwitterCardsDescription);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->TwitterCardsDescription)) {
					$value = trim($parent->TwitterCardsDescription);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->TwitterCardsDescription)) {
					$value = trim($config->TwitterCardsDescription);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0, $length);
		} else {
			// not found
			return false;
		}

	}

	//
	public function retrieveTwitterCardsImage($config) {

		//
		if ($this->owner->TwitterCardsImage()->exists()) {
			return $this->owner->TwitterCardsImage();
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if ($parent->TwitterCardsImage()->exists()) {
					return $parent->TwitterCardsImage();
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while ($parent = $parent->locateParent()) {
				if ($parent->TwitterCardsImage()->exists()) {
					return $parent->TwitterCardsImage();
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
			if ($config->TwitterCardsImage()->exists()) {
				return $config->TwitterCardsImage();
			}
		}
		// otherwise
		return false;

	}

	//
	public function retrieveTwitterCardsImageURL($config) {

		if ($image = $this->retrieveTwitterCardsImage($config)) {
			return $image->getAbsoluteURL();
		} else {
			return false;
		}

	}

	//// Schema.org

	//
	public function retrieveSchemaDotOrgTitle($config, $length = 140) {

		//
		$value = null;

		//
		if (trim($this->owner->SchemaDotOrgTitle)) {
			$value = trim($this->owner->SchemaDotOrgTitle);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->SchemaDotOrgTitle)) {
					$value = trim($parent->SchemaDotOrgTitle);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->SchemaDotOrgTitle)) {
					$value = trim($parent->SchemaDotOrgTitle);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->SchemaDotOrgTitle)) {
					$value = trim($config->SchemaDotOrgTitle);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0, $length);
		} else {
			// not found
			return false;
		}

	}

	//
	public function retrieveSchemaDotOrgDescription($config, $length = 185) {

		//
		$value = null;

		//
		if (trim($this->owner->SchemaDotOrgDescription)) {
			$value = trim($this->owner->SchemaDotOrgDescription);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->SchemaDotOrgDescription)) {
					$value = trim($parent->SchemaDotOrgDescription);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->SchemaDotOrgDescription)) {
					$value = trim($parent->SchemaDotOrgDescription);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->SchemaDotOrgDescription)) {
					$value = trim($config->SchemaDotOrgDescription);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0, $length);
		} else {
			// not found
			return false;
		}

	}

	//
	public function retrieveSchemaDotOrgImage($config) {

		//
		if ($this->owner->SchemaDotOrgImage()->exists()) {
			return $this->owner->SchemaDotOrgImage();
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if ($parent->SchemaDotOrgImage()->exists()) {
					return $parent->SchemaDotOrgImage();
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while ($parent = $parent->locateParent()) {
				if ($parent->SchemaDotOrgImage()->exists()) {
					return $parent->SchemaDotOrgImage();
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
			if ($config->SchemaDotOrgImage()->exists()) {
				return $config->SchemaDotOrgImage();
			}
		}
		// otherwise
		return false;

	}

	//
	public function retrieveSchemaDotOrgImageURL($config) {

		if ($image = $this->retrieveSchemaDotOrgImage($config)) {
			return $image->getAbsoluteURL();
		} else {
			return false;
		}

	}

	//// Public shares

	//
	public function retrieveGooglePlusAuthor($config) {

		//
		$value = null;

		//
		if (trim($this->owner->GooglePlusAuthor)) {
			$value = trim($this->owner->GooglePlusAuthor);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->GooglePlusAuthor)) {
					$value = trim($parent->GooglePlusAuthor);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->GooglePlusAuthor)) {
					$value = trim($parent->GooglePlusAuthor);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->GooglePlusAuthor)) {
					$value = trim($config->GooglePlusAuthor);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0);
		} else {
			// not found
			return false;
		}

	}

	//
	public function retrieveGooglePlusPublisher($config) {

		//
		$value = null;

		//
		if (trim($this->owner->GooglePlusPublisher)) {
			$value = trim($this->owner->GooglePlusPublisher);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->GooglePlusPublisher)) {
					$value = trim($parent->GooglePlusPublisher);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->GooglePlusPublisher)) {
					$value = trim($parent->GooglePlusPublisher);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->GooglePlusPublisher)) {
					$value = trim($config->GooglePlusPublisher);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0);
		} else {
			// not found
			return false;
		}

	}

	//
	public function retrieveFacebookAppID($config) {

		//
		$value = null;

		//
		if (trim($this->owner->FacebookAppID)) {
			$value = trim($this->owner->FacebookAppID);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->FacebookAppID)) {
					$value = trim($parent->FacebookAppID);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->FacebookAppID)) {
					$value = trim($parent->FacebookAppID);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->FacebookAppID)) {
					$value = trim($config->FacebookAppID);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0);
		} else {
			// not found
			return false;
		}

	}

	//
	public function retrieveFacebookAdmins($config) {

		//
		$value = null;

		//
		if (trim($this->owner->FacebookAdmins)) {
			$value = trim($this->owner->FacebookAdmins);
		} else if ($config->Inheritance == "Single") {
			if ($parent = $this->locateParent()) {
				if (trim($parent->FacebookAdmins)) {
					$value = trim($parent->FacebookAdmins);
				}
			}
		} else if ($config->Inheritance == "Multiple") {
			$parent = $this;
			while($parent = $parent->locateParent()) {
				if (trim($parent->FacebookAdmins)) {
					$value = trim($parent->FacebookAdmins);
					break;
				}
			}
		} else if ($config->Inheritance == "SiteConfig") {
				if (trim($config->FacebookAdmins)) {
					$value = trim($config->FacebookAdmins);
				}
		}

		//
		if ($value) {
			// found
			return substr($value, 0);
		} else {
			// not found
			return false;
		}

	}

	//

	/*
	 * Template Functions
	 */

	//
	public function ReconMetadataItemscope() {

		//
		$config = SiteConfig::current_site_config();

		// static for now, until more types are introduced
		if ($config->GooglePlusStatus) {
			return ' itemscope itemtype="http://schema.org/Article" ';
		} else {
			return false;
		}

	}

	//
	public function ReconMetadata() {

		//
		$config = SiteConfig::current_site_config();
		$output = new ArrayObject();

		/**
		 * Charset
		 * @TODO complete BaseHref integration into CMS
		 */

		if ($config->CharsetStatus) {

			//
			$output->append("<meta charset='{$config->Charset}' />");

			//
			$baseURL = Director::absoluteBaseURL();
			$output->append("<base href='$baseURL' />");

		}

		/**
		 * Title
		 */

		// title : 70 chars
		if ($config->TitleStatus) {
			$output->append("<title>{$this->generateTitle($config)}</title>");
		}

		/**
		 * Favicon
		 * @TODO page-specific favicons
		 */

		//
		$favicon = null;
		if ($config->FaviconStatus && $config->Favicon()->exists()) {
			//
			$favicon = $config->Favicon();
		}
		if ($favicon) $output->append("<link rel='shortcut icon' href='{$favicon->AbsoluteURL}' />");

		/**
		 * HTML
		 */

		// description : 155 chars
		if ($config->MetaDescriptionStatus) {
			if ($metaDescription = $this->retrieveMetaDescription($config)) {
				$output->append("<meta name='description' content='{$metaDescription}' />");
			}
		}

		// keywords : obsolete
		if ($config->MetaKeywordsStatus) {
			if ($metaKeywords = $this->retrieveMetaKeywords($config)) {
				$output->append("<meta name='keywords' content='{$metaKeywords}' />");
			}
		}

		/**
		 * Open Graph / Facebook
		 * @TODO resize / render image
		 */

		if ($config->OpenGraphStatus) {

			// type : [ types? ]
			$output->append("<meta property='og:type' content='article' />");
			// url
			$output->append("<meta property='og:url' content='{$this->owner->AbsoluteLink()}' />");
			// title : 95 chars
			if ($OGTitle = $this->retrieveOpenGraphTitle($config)) {
				$output->append("<meta property='og:title' content='{$OGTitle}' />");
			}
			// description : 297 chars
			if ($OGDescription = $this->retrieveOpenGraphDescription($config)) {
				$output->append("<meta property='og:description' content='{$OGDescription}' />");
			}
			// image [ square ? ]
			if ($OGImage = $this->retrieveOpenGraphImage($config)) {
				// resize / render ??
				$output->append("<meta property='og:image' content='{$OGImage->getAbsoluteURL()}' />");
			}

		}

		/**
		 * Twitter Cards
		 * @TODO resize / render image
		 */

		if ($config->TwitterCardsStatus) {

			// type : [ summary | photo | player ]
			$output->append("<meta name='twitter:card' content='summary' />");
			// url
			$output->append("<meta name='twitter:url' content='{$this->owner->AbsoluteLink()}' />");
			// title : 70 chars
			if ($TwitterTitle = $this->retrieveTwitterCardsTitle($config)) {
				$output->append("<meta name='twitter:title' content='{$TwitterTitle}' />");
			}
			// description : 200 chars
			if ($TwitterDescription = $this->retrieveTwitterCardsDescription($config)) {
				$output->append("<meta name='twitter:description' content='{$TwitterDescription}' />");
			}
			// image [ ? ]
			if ($TwitterImage = $this->retrieveTwitterCardsImage($config)) {
				$output->append("<meta name='twitter:image' content='{$TwitterImage->AbsoluteURL}' />");
			}

		}

		/**
		 * Schema.org
		 * @TODO resize / render image
		 */

		if ($config->SchemaDotOrgStatus) {

			// type : [ ? ] << itemscope >> in head element
			// $output->append("<meta itemprop='type' content='Article' />");
			// url
			$output->append("<meta itemprop='url' content='{$this->owner->AbsoluteLink()}' />");
			// title (name) : 140 chars
			if ($SchemaTitle = $this->retrieveSchemaDotOrgTitle($config)) {
				$output->append("<meta itemprop='name' content='{$SchemaTitle}'>");
			}
			// description : 185 chars
			if ($SchemaDescription = $this->retrieveSchemaDotOrgDescription($config)) {
				$output->append("<meta itemprop='description' content='{$SchemaDescription}'>");
			}
			// image
			if ($SchemaImage = $this->retrieveSchemaDotOrgImage($config)) {
				$output->append("<meta itemprop='image' content='{$SchemaImage->AbsuloteURL}'>");
			}

		}

		/**
		 * Google Plus
		 */

		if ($config->GooglePlusStatus) {

			// author
			if ($GoogleAuthor = $this->retrieveGooglePlusAuthor($config)) {
				$output->append("<link rel='author' href='https://plus.google.com/{$GoogleAuthor}/' />");
			}

			// publisher
			if ($GooglePublisher = $this->retrieveGooglePlusPublisher($config)) {
				$output->append("<link rel='publisher' href='https://plus.google.com/{$GooglePublisher}/' />");
			}

		}

		/**
		 * Facebook
		 */

		if ($config->FacebookAppStatus) {

			// App ID
			if ($FacebookAppID = $this->retrieveFacebookAppID($config)) {
				$output->append("<meta property='fb:app_id' content='{$FacebookAppID}' />");
			}

			// Admins
			if ($FacebookAdmins = $this->retrieveFacebookAdmins($config)) {
				$output->append("<meta property='fb:admins' content='{$FacebookAdmins}' />");
			}

		}

		// return
		return implode("\n", iterator_to_array($output));

	}

}