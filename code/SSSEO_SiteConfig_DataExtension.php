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

class SSSEO_SiteConfig_DataExtension extends DataExtension
{


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
        // Favicon
        'TouchIconStatus' => 'Enum(array("off", "on"), "on")', // default: on
        // Authorship
        'AuthorshipStatus' => 'Enum(array("off", "on"), "off")', // default: off
        // Facebook Insights
        'FacebookInsightsStatus' => 'Enum(array("off", "on"), "off")', // default: off
        // Open Graph
        'OpenGraphStatus' => 'Enum(array("off", "on"), "off")', // defaults: off
        // Twitter Cards
        'TwitterCardsStatus' => 'Enum(array("off", "on"), "off")', // defaults: off
        // Schema.org
        'SchemaDotOrgStatus' => 'Enum(array("off", "on"), "off")', // defaults: off
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

        //// Authorship
        'GoogleProfileID' => 'Varchar(128)',
        'FacebookProfileID' => 'Varchar(128)',

        //// Facebook Insights
        // Application ID
        'FacebookAppID' => 'Varchar(128)',

    );
    private static $has_one = array(
        // Favicon
        'FaviconPNG' => 'Image',
        // Touch Icon
        'TouchIconImage' => 'Image',
    );

    private static $has_many = array(
        //// Facebook Insights
        // Facebook Administrators
        'FacebookAdmins' => 'Member',
    );


    /* Overload Methods
    ------------------------------------------------------------------------------*/

    // CMS Fields
    public function updateCMSFields(FieldList $fields)
    {

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
            // Touch Icon
            DropdownField::create('TouchIconStatus', 'Touch Icon', $owner->dbObject('TouchIconStatus')->enumValues())
                ->setDescription('enable touch icons for desktop shortcuts and browser dashboards'),
            // Authorship
            DropdownField::create('AuthorshipStatus', 'Authorship', $owner->dbObject('AuthorshipStatus')->enumValues())
                ->setDescription('enable authorship of pages'),
            // Facebook Insights
            DropdownField::create('FacebookInsightsStatus', 'Facebook Insights', $owner->dbObject('FacebookInsightsStatus')->enumValues())
                ->setDescription('enable Facebook Insights (Facebook Application)'),
            // Open Graph
            DropdownField::create('OpenGraphStatus', 'Open Graph', $owner->dbObject('OpenGraphStatus')->enumValues())
                ->setDescription('enable Open Graph'),
            // Twitter Cards
            DropdownField::create('TwitterCardsStatus', 'Twitter Cards', $owner->dbObject('TwitterCardsStatus')->enumValues())
                ->setDescription('enable Twitter Cards'),
            // Schema.org
            DropdownField::create('SchemaDotOrgStatus', 'Schema.org', $owner->dbObject('SchemaDotOrgStatus')->enumValues())
                ->setDescription('enable Schema.org'),
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

        //// Touch Icon

        $tab = 'Root.SSSEO.TouchIcon';

        $fields->addFieldsToTab($tab, array(
            ReadonlyField::create('AppleTouchIconPrecomposed', 'apple-touch-icon-precomposed', 'on'),
            UploadField::create('TouchIconImage', 'Touch Icon Image')
                ->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'))
                ->setFolderName('SSSEO/TouchIcon/')
                ->setDescription('file format: JPG, PNG, GIF<br />pixel dimensions: 400 x 400 (recommended, minimum 192)<br />pixel ratio: 1:1')
        ));

        //// Facebook Insights

        if ($this->FacebookInsightsEnabled()) {
            $tab = 'Root.SSSEO.FacebookInsights';

            // add
            $fields->addFieldsToTab($tab, array(
                TextField::create('FacebookAppID', 'Facebook Application ID'),
                GridField::create('FacebookAdmins', 'Facebook Administrators', $this->owner->FacebookAdmins())
                    ->setConfig(GridFieldConfig_RelationEditor::create())
            ));
        }

        //// Authorship

        if ($this->AuthorshipEnabled()) {
            $tab = 'Root.SSSEO.Authorship';

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
    public function titleSeparatorDefault()
    {
        return self::$TitleSeparatorDefault;
    }

    //
    public function taglineSeparatorDefault()
    {
        return self::$TaglineSeparatorDefault;
    }

    //
    public function faviconBGDefault()
    {
        return self::$FaviconBGDefault;
    }


    /* Accessor Methods
    ------------------------------------------------------------------------------*/

    //
    public function CharsetEnabled()
    {
        return ($this->owner->CharsetStatus == 'off') ? false : true;
    }

    //
    public function CanonicalEnabled()
    {
        return ($this->owner->CanonicalStatus == 'off') ? false : true;
    }

    //
    public function TitleEnabled()
    {
        return ($this->owner->TitleStatus == 'off') ? false : true;
    }

    //
    public function FaviconEnabled()
    {
        return ($this->owner->FaviconStatus == 'off') ? false : true;
    }

    //
    public function TouchIconEnabled()
    {
        return ($this->owner->TouchIconStatus == 'off') ? false : true;
    }

    //
    public function AuthorshipEnabled()
    {
        return ($this->owner->AuthorshipStatus == 'off') ? false : true;
    }

    //
    public function FacebookInsightsEnabled()
    {
        return ($this->owner->FacebookInsightsStatus == 'off') ? false : true;
    }

    //
    public function OpenGraphEnabled()
    {
        return ($this->owner->OpenGraphStatus == 'off') ? false : true;
    }

    //
    public function TwitterCardsEnabled()
    {
        return ($this->owner->TwitterCardsStatus == 'off') ? false : true;
    }

    //
    public function SchemaDotOrgEnabled()
    {
        return ($this->owner->SchemaDotOrgStatus == 'off') ? false : true;
    }

    //
    public function ExtraMetaEnabled()
    {
        return ($this->owner->ExtraMetaStatus == 'off') ? false : true;
    }
}
