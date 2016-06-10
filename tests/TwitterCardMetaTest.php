<?php

/**
 * Class TwitterCardMetaTest
 *
 * @mixin PHPUnit_Framework_Assert
 */
class TwitterCardMetaTest extends SapphireTest
{
    protected static $fixture_file = 'fixtures/TwitterCardMetaTest.yml';

    public function setUp()
    {
        /** =========================================
         * @var SiteConfig $siteConfig
         * ========================================*/

        parent::setUp();

        $siteConfig = SiteConfig::current_site_config();
        $siteConfig->setField('DefaultTwitterHandle', 'silverstripe');
        $siteConfig->write();
    }

    /**
     * @covers TwitterCardMeta::getCreatorHandle()
     */
    public function testGetCreatorHandle()
    {
        /** =========================================
         * @var TwitterCardMeta $aboutPage
         * ========================================*/

        $aboutPage = $this->objFromFixture('SiteTree', 'about');
        $handle = $aboutPage->getCreatorHandle();
        $this->assertEquals('toastnz', $handle);

        $aboutPage = $this->objFromFixture('SiteTree', 'home');
        $handle = $aboutPage->getCreatorHandle();
        $this->assertEquals('silverstripe', $handle);
    }
}
