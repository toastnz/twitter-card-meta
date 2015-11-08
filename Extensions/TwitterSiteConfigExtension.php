<?php

/**
 * Class TwitterSiteConfigExtension
 */
class TwitterSiteConfigExtension extends DataExtension {

    private static $db = array(
        'DefaultTwitterHandle' => 'Varchar(255)'
    );

    private static $has_one = array(
        'DefaultTwitterImage' => 'Image'
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields) {

        if (!$fields->fieldByName('Root.Settings')) {
            $fields->addFieldToTab('Root', TabSet::create('Settings'));
        }

        /** -----------------------------------------
         * Details
         * ----------------------------------------*/

        $fields->findOrMakeTab('Root.Settings.TwitterCards');
        $fields->addFieldsToTab('Root.Settings.TwitterCards', array(
            HeaderField::create('', 'Twitter Cards'),
            Textfield::create('DefaultTwitterHandle', 'Default Twitter Handle'),
            UploadField::create('DefaultTwitterImage', 'Default Twitter Card Image')
        ));

    }

}
