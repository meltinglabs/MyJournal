<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/create.class.php';
/**
 * @package myjournal
 */
class MyJournalCreateManagerController extends ResourceCreateManagerController {

	public function loadCustomCssJs() {
		$managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $myjournalAssetsUrl = $this->modx->getOption('myjournal.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/myjournal/');
		$connectorUrl = $myjournalAssetsUrl.'connector.php';
		$this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($myjournalAssetsUrl . 'mgr/libs/jquery.1.7.1.min.js');	
		
		//Load last because if a TV load another jQuery lib, it cancel/conflict markitup registering with jQuery
		$this->addLastJavascript($myjournalAssetsUrl . 'mgr/libs/jquery.markitup.js');	
		
		$this->addJavascript($myjournalAssetsUrl . 'mgr/libs/sets/default/set.js');	
		$this->addJavascript($myjournalAssetsUrl . 'mgr/container/create/panel.js');	
		$this->addJavascript($myjournalAssetsUrl . 'mgr/container/create/resource.js');	
		
		$this->addHtml('<script type="text/javascript"> 
		Ext.onReady(function() {			
			MyJournal.assets_url = "'.$myjournalAssetsUrl.'";
			MyJournal.connector_url = "'.$connectorUrl.'";
			MyJournal.resource_id = "'.$this->resource->get('id').'";
			MyJournal.record = '.$this->modx->toJSON($this->resourceArray).';
			MODx.ctx = "'.$this->resource->get('context_key').'";
			MODx.add("myjournal-main-panel"); 			
		});</script>');		
		
		/* load RTE */
        // $this->loadRichTextEditor();
		
		$this->addCss($myjournalAssetsUrl.'css/index.css');
		// $this->addCss($myjournalAssetsUrl.'mgr/libs/skins/markitup/style.css');
		// $this->addCss($myjournalAssetsUrl.'mgr/libs/sets/default/style.css');
	}

    public function getLanguageTopics() {
        return array('resource','myjournal:default');
    }
	
	 /**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        // return $this->modx->lexicon('articles.container_new');
        return 'New Journal';
    }
}