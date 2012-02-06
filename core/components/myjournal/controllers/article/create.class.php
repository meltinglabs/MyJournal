<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/create.class.php';
/**
 * @package myjournal
 */
class MyArticleCreateManagerController extends ResourceCreateManagerController {
    public function getLanguageTopics() {
        return array('resource');
    }
	 /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        // return $this->modx->lexicon('articles.container_new');
        return 'New Articles';
    }
}