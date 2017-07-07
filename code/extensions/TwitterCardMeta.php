<?php

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Control\Director;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataExtension;

/**
 * Class TwitterCardMeta
 *
 * @property SiteTree $owner
 */
class TwitterCardMeta extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'TwitterSite'        => 'Varchar(160)',
        'TwitterCreator'     => 'Varchar(160)',
        'TwitterTitle'       => 'Varchar(160)',
        'TwitterCardType'    => 'Varchar(160)',
        'TwitterDescription' => 'Varchar(160)'
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'TwitterImage' => Image::class
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        /** =========================================
         * @var TextareaField $address
        ===========================================*/

        if (!$this->owner->TwitterCardType) {
            $this->owner->TwitterCardType = 'summary_large_image';
        }

        $fields->addFieldToTab('Root.Main', ToggleCompositeField::create('Twitter Graph', 'Twitter Card',
            [
                LiteralField::create('', '<h2>&nbsp;&nbsp;&nbsp;Twitter Card <img style="position:relative;top:4px;left 4px;" src="' . Director::absoluteBaseURL() . 'twitter-card-meta/images/twitter.png"></h2>'),
                TextField::create('TwitterCreator', 'Creator Handle')
                    ->setAttribute('placeholder', 'e.g @username')
                    ->setRightTitle('Twitter account name for the author/creator (Will default to site handle)'),
                OptionsetField::create('TwitterCardType', 'Twitter Card Type', [
                    'summary_large_image' => 'summary with large image',
                    'summary'             => 'summary',
                ], 'summary_large_image')
                    ->setRightTitle('Choose which type of twitter card you would like this page to share as.'),
                TextField::create('TwitterTitle', 'Twitter Card Title')
                    ->setAttribute('placeholder', 'e.g Description Of Page Content')
                    ->setRightTitle('Twitter title to to display on the Twitter card'),
                TextareaField::create('TwitterDescription', '')
                    ->setRightTitle('Twitter card description goes here, automatically defaults to the content summary'),
                UploadField::create('TwitterImage', 'Twitter Card Image')
                    ->setRightTitle('Will default too the first image in the WYSIWYG editor or banner image if left blank'),
            ]
        ));
    }

    /**
     * Template helper
     *
     * @return string|null
     */
    public function getCreatorHandle()
    {
        return $this->owner->TwitterCreator ?: SiteConfig::current_site_config()->DefaultTwitterHandle;
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

    /**
     * @param string $tags
     */
    public function MetaTags(&$tags)
    {
        /** =========================================
         * @var TwitterSiteConfigExtension $siteConfig
        ===========================================*/

        $siteConfig = SiteConfig::current_site_config();

        // Type
        if ($this->owner->TwitterCardType) {
            $tags .= sprintf('<meta name="twitter:card" content="%s">', $this->owner->TwitterCardType) . "\n";
        } else {
            $tags .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        }

        // Site
        $siteHandle = $this->owner->TwitterSite ?: $siteConfig->DefaultTwitterHandle ?: '';
        $tags       .= sprintf('<meta name="twitter:site" content="%s">', $siteHandle) . "\n";

        // Creator
        if ($this->owner->TwitterSite) {
            $tags .= sprintf('<meta name="twitter:creator" content="%s">', $this->owner->TwitterSite) . "\n";
        } else {
            $tags .= sprintf('<meta name="twitter:creator" content="%s">', $siteHandle) . "\n";
        }

        // Title
        if ($this->owner->TwitterTitle) {
            $tags .= sprintf('<meta name="twitter:title" content="%s">', $this->owner->TwitterTitle) . "\n";
        } else {
            $tags .= sprintf('<meta name="twitter:title" content="%s">', $this->owner->Title) . "\n";
        }

        // Description
        $tags .= sprintf('<meta name="twitter:description" content="%s">', $this->owner->TwitterDescription) . "\n";

        // Image
        $image = $this->owner->getTwitterImageURL();

        $this->owner->extend('updateTwitterImage', $image);

        $tags .= sprintf('<meta name="twitter:image" content="%s">', $image) . "\n";
    }

}
