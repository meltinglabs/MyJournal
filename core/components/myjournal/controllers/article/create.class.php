<?php
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/create.class.php';
/**
 * @package myjournal
 */
class MyArticleCreateManagerController extends ResourceCreateManagerController {
    
    public function loadCustomCssJs() {
        $managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $myjournalAssetsUrl = $this->modx->getOption('myjournal.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/myjournal/');
        $connectorUrl = $myjournalAssetsUrl.'connector.php';
        
        $this->addJavascript($myjournalAssetsUrl . 'mgr/libs/jquery.1.7.1.min.js');
        // Load last because if a TV load another jQuery lib, it cancel/conflict markitup registering with jQuery
        $this->addLastJavascript($myjournalAssetsUrl . 'mgr/libs/jquery.markitup.js');
        $this->addJavascript($myjournalAssetsUrl . 'mgr/libs/sets/default/set.js'); 
 
        $this->addJavascript($myjournalAssetsUrl . 'mgr/core/formpanel.js');    
        $this->addJavascript($myjournalAssetsUrl . 'mgr/article/create/panel.js');    
        $this->addJavascript($myjournalAssetsUrl . 'mgr/article/create/resource.js');    
        
        $this->addHtml('<script type="text/javascript"> 
        Ext.onReady(function() {            
            MyJournal.assets_url = "'.$myjournalAssetsUrl.'";
            MyJournal.connector_url = "'.$connectorUrl.'";
            MyJournal.resource_id = "'.$this->resource->get('id').'";
            MyJournal.record = '.$this->modx->toJSON($this->resourceArray).';
            MODx.ctx = "'.$this->resource->get('context_key').'";
            MODx.add("myjournal-main-panel");             
        });</script>');        
                
        $this->addCss($myjournalAssetsUrl.'css/index.css');
        $this->addCss($myjournalAssetsUrl.'mgr/libs/skins/markitup/style.css');
        $this->addCss($myjournalAssetsUrl.'mgr/libs/sets/default/style.css');
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
        return 'New Article';
    }
    
    /**
     * Load the TVs for the Resource
     *
     * @param array $reloadData resource data passed if reloading
     * @return string The TV editing form
     */
    public function loadTVs($reloadData = array()) {
        /* We override all the implementation here because we don't set any TV during creation of a new journal container */
        return '';
    }
    
    public function getResourceGroups() {
        $parentGroups = array();
        if ($this->resource->get('id') == 0) {
            $parent = $this->modx->getObject('modResource',$this->resource->get('parent'));
            /** @var modResource $parent */
            if ($parent) {
                $parentResourceGroups = $parent->getMany('ResourceGroupResources');
                /** @var modResourceGroupResource $parentResourceGroup */
                foreach ($parentResourceGroups as $parentResourceGroup) {
                    $parentGroups[] = $parentResourceGroup->get('document_group');
                }
                $parentGroups = array_unique($parentGroups);
            }
        }

        $this->resourceArray['resourceGroups'] = array();
        $resourceGroups = $this->resource->getGroupsList(array('name' => 'ASC'),0,0);
        /** @var modResourceGroup $resourceGroup */
        foreach ($resourceGroups['collection'] as $resourceGroup) {
            $access = (boolean) $resourceGroup->get('access');
            if (!empty($parent) && $this->resource->get('id') == 0) {
                $resourceGroupArray['access'] = in_array($resourceGroup->get('id'),$parentGroups) ? true : false;
            }
            $resourceGroupArray = array(
                'inputValue' => $resourceGroup->get('id'),
                'boxLabel' => $resourceGroup->get('name'),
                'checked' => $access,
                'name' => 'resource_groups[]',
            );

            $this->resourceArray['resourceGroups'][] = $resourceGroupArray;
        }
        return $this->resourceArray['resourceGroups'];
    }
}