<?php

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

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
    private static $db = [
        'DefaultTwitterHandle' => 'Varchar(255)'
    ];

    private static $has_one = [
        'DefaultTwitterImage' => Image::class
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var UploadField $twitterImage
        ===========================================*/

        if (!$fields->fieldByName('Root.Metadata')) {
            $fields->addFieldToTab('Root', TabSet::create('Metadata'));
        }

        /** -----------------------------------------
         * Details
         * ----------------------------------------*/

        $twitterImage = UploadField::create('DefaultTwitterImage', 'Default Twitter Card Image');
        $twitterImage->setDescription('Ideal size 560px * 750px');

        $fields->findOrMakeTab('Root.Metadata.Twitter');
        $fields->addFieldsToTab('Root.Metadata.Twitter', [
            HeaderField::create('', 'Twitter Cards'),
            TextField::create('DefaultTwitterHandle', 'Default Twitter Handle'),
            $twitterImage
        ]);

    }

}
