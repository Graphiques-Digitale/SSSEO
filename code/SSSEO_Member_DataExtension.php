<?php

/**
 * SSSEO_Core_SiteTree_DataExtension
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

class SSSEO_Member_DataExtension extends DataExtension
{

    /* Overload Variable
     ------------------------------------------------------------------------------*/

    private static $db = array(
        // Author Google+ ID
        'GoogleProfileID' => 'Varchar(128)',
        // Author Facebook ID
        'FacebookProfileID' => 'Varchar(128)',
    );
    private static $has_one = array(
        'FacebookAdmin' => 'SiteConfig',
    );
    private static $many_many = array(
        // pages authored
        'Authored' => 'SiteTree',
    );


    /* Overload Methods
    ------------------------------------------------------------------------------*/

    // CMS Fields
    public function updateCMSFields(FieldList $fields)
    {

        // SSSEO Tabset
        $fields->addFieldToTab('Root', new TabSet('SSSEO'));

        // Configuration
        $tab = 'Root.SSSEO.Configuration';

        // Author
        $fields->addFieldsToTab($tab, array(
            TextField::create('GoogleProfileID', 'Google+ Profile ID'),
            TextField::create('FacebookProfileID', 'Facebook Profile ID')
        ));

        /**
         * @TODO ???
         */
        // Facebook Administrators
// 		$tab = 'Root.SSSEO.Configuration';
// 		$fields->addFieldsToTab($tab, array(
// 			GridField::create('FacebookAdmin', 'Facebook Administrator', $this->owner->FacebookAdmin())
// 				->setConfig(GridFieldConfig_RelationEditor::create())
// 		));

        // Pages Authored
        // remove
        $fields->removeByName(array('Authored'));
        // add
        $tab = 'Root.SSSEO.Authored';
        $fields->addFieldsToTab($tab, array(
            GridField::create('Authored', 'Pages Authored', $this->owner->Authored())
                ->setConfig(GridFieldConfig_RelationEditor::create())
        ));
    }
}
