<?php

/**
 * Class ReconMetadata_SiteConfigExtension_Configuration
 *
 * @TODO add MetaCustom
 *
 */

class ReconMetadata_SiteConfigExtension extends DataExtension {

	//// Overload Class Variables
	private static $db = array(

		//// Configuration
		// Charset
		"CharsetStatus" => "Boolean",
		// Title
		"TitleStatus" => "Boolean",
		// Favicon
		"FaviconStatus" => "Boolean",
		// Inheritance
		"Inheritance" => "Enum(array('None', 'Single', 'Multiple', 'SiteConfig'), 'None')",
		// HTML
		"MetaDescriptionStatus" => "Boolean",
		"MetaKeywordsStatus" => "Boolean",
		// "MetaCustomStatus" => "Boolean",
		// Social Media
		"OpenGraphStatus" => "Boolean",
		"TwitterCardsStatus" => "Boolean",
		"SchemaDotOrgStatus" => "Boolean",
		// Transmissions
		"GooglePlusStatus" => "Boolean",
		"FacebookAppStatus" => "Boolean",

		//// Metadata
		// Charset
		"Charset" => "Text",
		// Title
		"Title" => "Text",
		"Tagline" => "Text",
		"TitleSeparator" => "Varchar(1)",
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
		// Favicon
		"Favicon" => "Image",
		// Image
		"OpenGraphImage" => "Image",
		"TwitterCardsImage" => "Image",
		"SchemaDotOrgImage" => "Image",
	);

	//// Overload Predefined Variables
	private static $defaults = array(

		//// Configuration
		// Inheritance
		"Inheritance" => "None", // shouldn't need ??
		// Charset
		"CharsetStatus" => 1,
		// Title
		"TitleStatus" => 1,
		// Favicon
		"FaviconStatus" => 1,
		// HTML
		"MetaDescriptionStatus" => 1,
		"MetaKeywordsStatus" => 0,
		// Social Sharing
		"OpenGraphStatus" => 0,
		"TwitterCardsStatus" => 0,
		"SchemaDotOrgStatus" => 0,
		// Transmissions
		"GooglePlusStatus" => 0,
		"FacebookAppStatus" => 0,

		//// Metadata
		"Charset" => "utf-8",
		"TitleSeparator" => "|",
		//
		"OpenGraphType" => "article", // need ??
		"TCType" => "summary", // need ??
		"SchemaType" => "Article", // need ??

	);

	//// Overload Class Functions

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

	// CMS Fields
	public function updateCMSFields(FieldList $fields) {

		//// Tabset
		$fields->addFieldToTab("Root", new TabSet("ReconMetadata"));

		//// Defaults

		// variables
		$self = $this->owner;
		$defaults = new ArrayObject();
		$disabled = "[ disabled ]";
		$count = 1;
		$subcount = null;

		/*
		 * HTML Configuration
		 */
		if ($self->CharsetStatus || $self->TitleStatus || $self->FaviconStatus) {

			// header
			$defaults->append(HeaderField::create("DefaultConfigurationHeader", $count . ". HTML Configuration", 2));

			/*
			 * Charset
			 * @TODO expand charsets
			 */
			if ($self->CharsetStatus) {

				// header
				$defaults->append(HeaderField::create("CharsetHeader", $count . ".1. Character Set *", 3));
				// charset
				$defaults->append(TextField::create("Charset", "Character Set")->setDescription("* defaults to <strong>utf-8</strong>"));

			}

			/*
			 * Title
			 */
			if ($self->TitleStatus) {

				// header
				$defaults->append(HeaderField::create("TitleHeader", $count . ".1. Title", 3));
				$defaults->append(LiteralField::create("TitleContents", "<p>Takes the form: <strong>Page Title | Site Title | Site Tagline</strong></p>"));
				// title
				$defaults->append(TextField::create("Title", "Site Title")->setDescription("* recommended<br />- <strong>Your Site Name</strong> will be ignored"));
				$defaults->append(TextField::create("Tagline", "Site Tagline")->setDescription("* not recommended, unless you know what you're doing<br />- <strong>your tagline here</strong> will be ignored"));
				$defaults->append(TextField::create("TitleSeparator", "Title Separator *<br />[ 1 char ]")
					->setDescription("* defaults to <strong>| (pipe)</strong><br />* copy and paste HTML entities from <a href='http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references#Character_entity_references_in_HTML' target='_blank'>Wikipedia HTML Character Reference</a><br />- restricted to 1 character")
					->setMaxLength(1)
				);

			}

			/**
			 * Favicon
			 * @TODO modern & fallback icon options
			 * @TODO apple touch icons
			 * @TODO arbitrary number of favicons
			 */
			if ($self->FaviconStatus) {

				//// Favicon
				$defaults->append(HeaderField::create("FaviconHeader", $count++ . ".1. Favicon", 3));
				// image
				$title = "Icon File";
				$favicon = UploadField::create("Favicon", $title)->setDescription("- allowed file types: ICO, PNG, GIF, JPG, APNG<br />- allowed dimensions: 16&sup2; or 32&sup2; pixels");
				$favicon->setFolderName("Metadata/Favicons");
				$favicon->setAllowedExtensions(array("ico", "png", "gif", "jpg", "jpeg", "apng"));
				$defaults->append($favicon);


			}

		}

		/*
		 * Metadata
		 */
		if ($self->Inheritance != "None") {

			//
			$subcount = 1;

			//// HTML

			// check whether any metadata elements are active
			if ($self->MetaDescriptionStatus ||
				$self->MetaKeywordsStatus ||
				$self->OpenGraphStatus ||
				$self->TwitterCardsStatus ||
				$self->SchemaDotOrgStatus ||
				$self->GooglePlusStatus ||
				$self->FacebookAppStatus
			) {
				$defaults->append(HeaderField::create("DefaultsMetadataHeader", $count . ". HTML Metadata", 2));
			}

			// check whether any HTML metadata elements are active
			if ($self->MetaDescriptionStatus || $self->MetaKeywordsStatus) {
				$defaults->append(HeaderField::create("HTMLHeader", $count . "." . $subcount++ . ". HTML", 3));
			}

			// description
			if ($self->MetaDescriptionStatus) {
				$defaults->append(TextareaField::create("MetaDescription", "Meta Description<br />[ 155 chars ]")); // ->setMaxLength(155) - doesn't work on TextareaField
			} else {
				// disabled message
				// $defaults->append(ReadonlyField::create("MetaDescriptionMsg", $title, $disabled));
			}

			// keywords
			if ($self->MetaKeywordsStatus) {
				$defaults->append(TextareaField::create("MetaKeywords", "Meta Keywords<br />[ obsolete ]"));
			}

			//// Open Graph
			if ($self->OpenGraphStatus) {

				// header
				$defaults->append(HeaderField::create("OpenGraphHeader", $count . "." . $subcount++ . ". Open Graph", 3));

				// type
				$defaults->append(DropDownField::create("OpenGraphType", "Type",  singleton("SiteConfig")->dbObject("OpenGraphType")->enumValues())->performDisabledTransformation());

				// title
				$defaults->append(TextField::create("OpenGraphTitle", "Title<br />[ 95 chars ]")->setMaxLength(95));

				// description
				$defaults->append(TextareaField::create("OpenGraphDescription", "Description<br />[ 297 chars ]")); // ->setMaxLength(297) - doesn't work on TextareaField

				// image
				$OGImage = UploadField::create("OpenGraphImage", "Image");
				$OGImage->setFolderName("Metadata/OpenGraph");
				$OGImage->setAllowedExtensions(array("jpg", "jpeg", "png", "gif", "bmp"));
				$OGImage->setDescription("- allowed file types: JPG, PNG, GIF, BMP<br />- recommended dimensions: 120 x 120 pixels<hr /><strong>Facebook says:</strong> Use images that are at least 1200 x 630 pixels to display large, beautiful stories on Facebook with high resolution devices. At the minimum, your images should be 600 x 315 pixels to display in the larger format. If your image is smaller than 600 x 315 px, it will still display in a link page post, but the size will be much smaller. Note that images are displayed in 1.91:1 aspect ratio across desktop and mobile News Feed.");
				$defaults->append($OGImage);

			}

			//// Twitter Cards
			if ($self->TwitterCardsStatus) {

				//
				$defaults->append(HeaderField::create("TwitterCardsHeader", $count . "." . $subcount++ . ". Twitter Cards", 3));

				// type
				$defaults->append(DropDownField::create("TwitterCardsType", "Type",  singleton("SiteConfig")->dbObject("TwitterCardsType")->enumValues())->performDisabledTransformation());

				// title
				$defaults->append(TextField::create("TwitterCardsTitle", "Title<br />[ 70 chars ]")->setMaxLength(70));

				// description
				$defaults->append(TextareaField::create("TwitterCardsDescription", "Description<br />[ 200 chars ]")); // ->setMaxLength(200) - doesn't work on TextareaField

				// image
				$TwitterImage = UploadField::create("TwitterCardsImage", "Image");
				$TwitterImage->setFolderName("Metadata/TwitterCards");
				$TwitterImage->setAllowedExtensions(array("jpg", "jpeg", "png", "gif"));
				$TwitterImage->setDescription("- allowed file types: JPG, PNG, GIF<br />- recommended dimensions: 120 x 120 pixels<hr /><strong>Twitter says:</strong> URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. The image must be a minimum size of 120px by 120px and must be less than 1MB in file size. For an expanded tweet and its detail page, the image will be cropped to a 4:3 aspect ratio and resized to be displayed at 120px by 90px. The image will also be cropped and resized to 120px by 120px for use in embedded tweets.");
				$defaults->append($TwitterImage);

			}

			//// Schema.org
			if ($self->SchemaDotOrgStatus) {

				//
				$defaults->append(HeaderField::create("SchemaDotOrgHeader", $count++ . "." . $subcount . ". Schema.org", 3));

				// type
				$defaults->append(DropDownField::create("SchemaDotOrgType", "Type",  singleton("SiteConfig")->dbObject("SchemaDotOrgType")->enumValues())->performDisabledTransformation());

				// title
				$defaults->append(TextField::create("SchemaDotOrgTitle", "Title<br />[ 140 chars ]")->setMaxLength(140));

				// description
				$defaults->append(TextareaField::create("SchemaDotOrgDescription", "Description<br />[ 185 chars ]")); //->setMaxLength(185) - doesn't work on TextareaField

				// image
				$SchemaImage = UploadField::create("SchemaDotOrgImage", "Image");
				$SchemaImage->setFolderName("Metadata/SchemaDotOrg");
				$SchemaImage->setAllowedExtensions(array("jpg", "jpeg", "png", "gif"));
				$SchemaImage->setDescription("- allowed file types: JPG, PNG, GIF<br />- recommended dimensions: 120 x 120 pixels<hr /><strong>Google says:</strong> Images that are too small or not square enough are not included in the +Snippet, even if the images are explicitly referenced by schema.org microdata or Open Graph markup. Specifically, the height must be at least 120px, and if the width is less than 100px, then the aspect ratio must be no greater than 3.0.");
				$defaults->append($SchemaImage);

			}

			//// Transmissions

			//
			$subcount = 1;

			//
			if ($self->GooglePlusStatus || $self->FacebookAppStatus) {

				//
				$defaults->append(HeaderField::create("HeaderTransmissions", $count . ". Transmissions", 2));

				// Google Plus
				if ($self->GooglePlusStatus) {

					//
					$defaults->append(HeaderField::create("GooglePlusHeader", $count . "." . $subcount++ . ". Google Plus", 3));

					// Google Plus Author
					$defaults->append(TextField::create("GooglePlusAuthor", "Google Plus Author ID"));

					// Publisher Google ID
					$defaults->append(TextField::create("GooglePlusPublisher", "Google Plus Publisher ID"));

				}

				// Facebook App
				if ($self->FacebookAppStatus) {

					//
					$defaults->append(HeaderField::create("FacebookAppHeader", $count . "." . $subcount . ". Facebook Application", 3));

					// Facebook App ID
					$defaults->append(TextField::create("FacebookAppID", "App ID"));

					// Facebook Admins
					$defaults->append(TextField::create("FacebookAdmins", "Admins"));

				}

			}

		}

		// add
		// allow for header = +1 // disabled
		if ($defaults->count() > 0) {
			$fields->addFieldsToTab("Root.ReconMetadata.Defaults", iterator_to_array($defaults));
		}

		//// Configuration

		// variables
		$status = array(
			0 => "Off",
			1 => "On"
		);
		$recommendOn = array(
			0 => "Off",
			1 => "On *"
		);
		$recommendOff = array(
			0 => "Off *",
			1 => "On"
		);
		$count = 1;

		// fields
		$fields->addFieldsToTab("Root.ReconMetadata.Configuration", array(

			//
			HeaderField::create("ConfigurationHeader", $count++ . ". HTML Configuration", 2),
			// Charset
			DropdownField::create("CharsetStatus", "Character Set", $recommendOn),
			// Title
			DropdownField::create("TitleStatus", "Site Title", $recommendOn),
			// Favicon
			DropdownField::create("FaviconStatus", "Site Favicon", $recommendOn),

			//
			HeaderField::create("MetadataHeader", $count . ". HTML Metadata", 2),
			// Inheritance
			DropDownField::create("Inheritance", "Inheritance",  singleton("SiteConfig")->dbObject("Inheritance")->enumValues())->setDescription("* <strong>None</strong> recommended"),
			// Basic Metadata
			HeaderField::create("HTMLHeader", $count . ".1. HTML", 3),
			DropdownField::create("MetaDescriptionStatus", "Description", $recommendOn),
			DropdownField::create("MetaKeywordsStatus", "Keywords", $recommendOff),
			// Social Sharing
			HeaderField::create("SocialSharingHeader", $count . ".2. Social Sharing", 3),
			DropdownField::create("OpenGraphStatus", "Open Graph ( Facebook )", $status),
			DropdownField::create("TwitterCardsStatus", "Twitter Cards", $status),
			DropdownField::create("SchemaDotOrgStatus", "Schema.org ( Google )", $status),

			// Transmissions
			HeaderField::create("TransmissionsHeader", $count . ".3. Transmissions", 3),
			//
			DropdownField::create("GooglePlusStatus", "Google Plus Authorship", $status),
			DropdownField::create("FacebookAppStatus", "Facebook Application", $status),
		));

		//// Help
		$fields->addFieldsToTab("Root.ReconMetadata.Help", array(

			// Introduction
			// HeaderField::create("HelpHeader", "ReconMetadata Help"),
			LiteralField::create("HelpContents", "
				<h2>1. Usage</h2>
				<ul>
					<li>Use <strong>&dollar;ReconMetadata</strong> in templates to output metadata.</li>
					<li>This should immediately follow the opening head tag, especially so if you are using <strong>Character Set</strong>.</li>
					<li>Don't forget to forget to remove all references in your templates to whichever tags you use.</li>
					<li>And remember to <a href='/?flush=ALL' target='_blank'>bash the cache</a> and debug the output to be sure!</li>
				</ul>
				<h2>2. Inheritance</h2>
				<ul>
					<li>Inheritance is hierarchical and tracks up the site tree.</li>
					<li>If no value exists for a given field, it will search for a value based on the following strategies:</li>
					<li><strong>None</strong> - does not inherit anything</li>
					<li><strong>Single</strong> - inherits value from parent page only - root pages inherit from SiteConfig (Defaults)</li>
					<li><strong>Multiple</strong> - inherits the first value encountered recursively up the site tree, ending with SiteConfig (Defaults)</li>
					<li><strong>SiteConfig</strong> - inherits value from SiteConfig (Defaults) only</li>
				</ul>
				<br />
				<p>
					Well, as unforgiving as it is, you should be using <strong>None</strong>. Unless of course you don't want to.
					<br />
					<br />
					There are numerous cases where the active types could be useful, e.g.
					<br />
					<em>- setting a global Facebook App ID and Google Plus Authorship</em>
					<br />
					<em>- setting generic metadata for pages without metadata</em>
					<br />
					<br />
					The trick is to utilise as few values as possible, or to use the weaker forms of inheritance - <strong>Single</strong> and <strong>SiteConfig</strong>.
					<br />
					There can be potential SEO penalties for spamming bots with repetitive metadata, which completely defeats the point of this.
					<br />
					<em>You have been warned!</em>
				</p>
				<h2>3. Configuration</h2>
				<ul>
					<li><strong>On</strong> means the meta tag will be output</li>
					<li><strong>Off</strong> means it won't</li>
					<li><strong>*</strong>'s are recommended options</li>
				</ul>
				<h2>4. Defaults</h2>
				<ul>
					<li><strong>*</strong>'s will output a value even if empty</li>
					<li>And there is i</li>
				</ul>
			"),

		));

	}

	/**
	 * Class Functions
	 */

	/*
	 * Chain function for ReconMetadata_SiteTreeExtension
	 */
	public function locateParent() {
		return false;
	}

}