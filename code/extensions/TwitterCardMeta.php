<?php

/**
 * Class TwitterCardMeta
 *
 * @property string TwitterSite
 * @property string TwitterCreator
 * @property string TwitterTitle
 * @property string TwitterCardType
 * @property string TwitterDescription
 * @property int TwitterImageID
 *
 * @method Image TwitterImage
 */
class TwitterCardMeta extends DataExtension
{
    /**
     * @var array
     */
    private static $db = array(
        'TwitterSite' => 'Varchar(160)',
        'TwitterCreator' => 'Varchar(160)',
        'TwitterTitle' => 'Varchar(160)',
        'TwitterCardType' => 'Varchar(160)',
        'TwitterDescription' => 'Varchar(160)'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'TwitterImage' => 'Image'
    );

    /**
     * @param FieldList $fields
     * @return Object
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var TextareaField $address
        ===========================================*/

        if (!$this->owner->TwitterCardType) {
            $this->owner->TwitterCardType = 'summary_large_image';
        }

        $fields->addFieldsToTab('Root.Main', ToggleCompositeField::create('Twitter Graph', 'Twitter Card',
            array(
                LiteralField::create('', '<h2>&nbsp;&nbsp;&nbsp;Twitter Card <img style="position:relative;top:4px;left 4px;" src="' . Director::absoluteBaseURL() . 'twitter-card-meta/images/twitter.png"></h2>'),
                TextField::create('TwitterCreator', 'Creator Handle')
                    ->setAttribute('placeholder', 'e.g @username')
                    ->setRightTitle('Twitter account name for the author/creator (Will default to site handle)'),
                OptionsetField::create('TwitterCardType', 'Twitter Card Type', array(
                    'summary_large_image' => 'summary with large image',
                    'summary' => 'summary',
                ), 'summary_large_image')
                    ->setRightTitle('Choose which type of twitter card you would like this page to share as.'),
                TextField::create('TwitterTitle', 'Twitter Card Title')
                    ->setAttribute('placeholder', 'e.g Description Of Page Content')
                    ->setRightTitle('Twitter title to to display on the Twitter card'),
                TextAreaField::create('TwitterDescription', '')
                    ->setRightTitle('Twitter card description goes here, automatically defaults to the content summary'),
                UploadField::create('TwitterImage', 'Twitter Card Image')
                    ->setRightTitle('Will default too the first image in the WYSIWYG editor or banner image if left blank'),
            )
        ));
    }

    /**
     * Set up defaults for fields
     */
    public function onBeforeWrite()
    {
        /** =========================================
         * @var TwitterSiteConfigExtension $siteConfig
        ===========================================*/

        parent::onBeforeWrite();

        $siteConfig = SiteConfig::current_site_config();

        if ($this->owner->isChanged('Content') && !$this->owner->TwitterDescription) {
            $this->owner->setField('TwitterDescription', $this->owner->dbObject('Content')->Summary(50));
        }
        if ($this->owner->isChanged('Title') && !$this->owner->TwitterTitle) {
            $this->owner->setField('TwitterTitle', $this->owner->Title);
        }
        if (!$this->owner->TwitterSite) {
            $this->owner->setField('TwitterSite', $siteConfig->DefaultTwitterHandle);
        }
        if (!$this->owner->TwitterCreator) {
            $this->owner->setField('TwitterCreator', $siteConfig->DefaultTwitterHandle);
        }
        if (!$this->owner->TwitterImageID) {
            $this->owner->setField('TwitterImageID', $siteConfig->DefaultTwitterImageID);
        }

    }

    /**
     * Template helper
     *
     * @return string|null
     */
    public function getCreatorHandle()
    {
        return $this->owner->TwitterCreator ? : SiteConfig::current_site_config()->DefaultTwitterHandle;
    }

    /**
     * Controller logic for returning Twitter card image
     *
     * @return String
     */
    public function getTwitterImageURL()
    {
        if ($this->owner->TwitterImage() && $this->owner->TwitterImage()->exists()) {
            return $this->owner->TwitterImage()->CroppedImage(560, 750)->AbsoluteURL;
        } elseif ($firstImage = $this->getFirstImage()) {
            return Controller::join_links(Director::absoluteBaseURL(), $firstImage);
        } elseif (SiteConfig::current_site_config()->DefaultTwitterImage() && SiteConfig::current_site_config()->DefaultTwitterImage()->exists()) {
            return SiteConfig::current_site_config()->DefaultTwitterImage()->CroppedImage(560, 750)->AbsoluteURL;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getFirstImage()
    {
        $pattern = ' /<img[^>]+ src[\\s = \'"]';
        $pattern .= '+([^"\'>\\s]+)/is';
        if (preg_match($pattern, $this->owner->Content, $match) !== false && !empty($match)) {
            $imageLink = preg_replace('/_resampled\/resizedimage[0-9]*-/', '', $match[1]);
            return (string)$imageLink;
        } else {
            return '';
        }
    }


}
