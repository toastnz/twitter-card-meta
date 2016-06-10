<?php

/**
 * Class MigrateSiteTreeMetaTaskTest
 *
 * @mixin PHPUnit_Framework_Assert
 */
class MigrateSiteTreeMetaTaskTest extends SapphireTest
{
    protected static $fixture_file = 'fixtures/MigrateSiteTreeMetaTaskTest.yml';

    protected $extraDataObjects = array('Page');

    public function setUp()
    {
        parent::setUp();

        // Apply the extension to the Page
        DataExtension::add_to_class('Page', 'TwitterCardMeta');
    }

    public function testRunWithOverwrite()
    {
        $oldHomepage = $this->objFromFixture('Page', 'oldHome');

        $this->assertEquals('Home', $oldHomepage->TwitterTitle);
    }
}
