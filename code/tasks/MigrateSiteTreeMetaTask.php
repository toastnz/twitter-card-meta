<?php

use SilverStripe\Control\Director;
use SilverStripe\ORM\DB;
use SilverStripe\Core\Convert;
use SilverStripe\Dev\BuildTask;

/**
 * Class MigrateSiteTreeMetaTask
 */
class MigrateSiteTreeMetaTask extends BuildTask {

    protected $title = 'Migrate Twitter metadata from Page to SiteTree';

    protected $description = 'For versions < 0.1.0: Migrates existing data from the Page table to the SiteTree table. Pass the argument/GET var "overwrite=1" to replace existing data.';

    protected $eol;

    protected $hr;

    protected $fields_to_update = array(
        'TwitterSite',
        'TwitterCreator',
        'TwitterTitle',
        'TwitterCardType',
        'TwitterDescription',
        'TwitterImageID'
    );

    public function __construct()
    {
        if (!Director::is_cli()) {
            $this->eol = '<br>';
            $this->hr = '<hr>';
        } else {
            $this->eol = PHP_EOL;
            $this->hr = '--' . PHP_EOL;
        }

        parent::__construct();
    }

    /**
     * @param SS_HTTPRequest $request
     */
    public function run($request)
    {
        /** =========================================
         * @var Page $page
        ===========================================*/

        if (class_exists('Page')) {
            if (Page::has_extension('TwitterCardMeta')) {
                // Should we overwrite?
                $overwrite = $request->getVar('overwrite') ? true : false;

                echo sprintf('Overwrite is %s', $overwrite ? 'enabled' : 'disabled') . $this->eol . $this->eol;

                $pages = Page::get();

                foreach ($pages as $page) {

                    $id = $page->ID;

                    echo $this->hr;
                    echo 'Updating page: ' . $page->Title . $this->eol;

                    foreach ($this->fields_to_update as $fieldName) {
                        $oldData = DB::query("SELECT {$fieldName} FROM Page WHERE ID = {$id}")->column($fieldName);

                        $newData = DB::query("SELECT {$fieldName} FROM SiteTree WHERE ID = {$id}")->column($fieldName);

                        if (!empty($oldData)) {
                            // If new data has been saved and we don't want to overwrite, exit the loop
                            if (!empty($newData) && $overwrite === false) {
                                continue;
                            }
                            $data = Convert::raw2sql($oldData[0]);
                            DB::query("UPDATE SiteTree SET {$fieldName} = '{$data}' WHERE ID = {$id}");
                            DB::query("UPDATE SiteTree_Live SET {$fieldName} = '{$data}' WHERE ID = {$id}");
                        } else {
                            echo 'Field "' . $fieldName . '" empty.' . $this->eol;
                        }
                    }
                }
            }
        }
    }
}
