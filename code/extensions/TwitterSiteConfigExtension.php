<?php

/**
 * Class TwitterSiteConfigExtension
 *
 * @property string DefaultTwitterHandle
 * @property int    DefaultTwitterImageID
 *
 * @method Image DefaultTwitterImage
 */
class TwitterSiteConfigExtension extends DataExtension
{
    private static $db = array(
        'DefaultTwitterHandle' => 'Varchar(255)'
    );

    private static $has_one = array(
        'DefaultTwitterImage' => 'Image'
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var UploadField $twitterImage
        ===========================================*/

        if (!$fields->fieldByName('Root.Settings')) {
            $fields->addFieldToTab('Root', TabSet::create('Settings'));
        }

        /** -----------------------------------------
         * Details
         * ----------------------------------------*/

        $twitterImage = UploadField::create('DefaultTwitterImage', 'Default Twitter Card Image');
        $twitterImage->setDescription('Ideal size 560px * 750px');

        $fields->findOrMakeTab('Root.Settings.TwitterCards');
        $fields->addFieldsToTab('Root.Settings.TwitterCards', array(
            HeaderField::create('', 'Twitter Cards'),
            Textfield::create('DefaultTwitterHandle', 'Default Twitter Handle'),
            $twitterImage
        ));

    }

}
