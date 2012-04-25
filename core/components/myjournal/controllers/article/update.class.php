<?php
/**
 * @var modX $modx
 */
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';
/**
 * @package myjournal
 */
class MyArticleUpdateManagerController extends ResourceUpdateManagerController {
    public $tvElements = array();

    public function loadCustomCssJs() {
        $managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $myjournalAssetsUrl = $this->modx->getOption('myjournal.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/myjournal/');
        $connectorUrl = $myjournalAssetsUrl.'connector.php';
        
        // $this->addJavascript($managerUrl.'assets/modext/util/datetime.js');
        // $this->addJavascript($myjournalAssetsUrl . 'mgr/core/browser-panel.js');    
        // $this->addJavascript($myjournalAssetsUrl . 'mgr/core/browser-window.js'); 
        // $this->addJavascript($myjournalAssetsUrl . 'mgr/core/tv.js');    
        // $this->addJavascript($myjournalAssetsUrl . 'mgr/article/update/panel.js');    
        // $this->addJavascript($myjournalAssetsUrl . 'mgr/article/update/resource.js');  
        
        $this->addJavascript($myjournalAssetsUrl . 'mgr/libs/jquery.1.7.1.min.js');
        //Load last because if a TV load another jQuery lib, it cancel/conflict markitup registering with jQuery
        $this->addLastJavascript($myjournalAssetsUrl . 'mgr/libs/jquery.markitup.js');
        $this->addJavascript($myjournalAssetsUrl . 'mgr/libs/sets/default/set.js'); 
        
        $this->addJavascript($myjournalAssetsUrl . 'mgr/core/formpanel.js');    
        $this->addJavascript($myjournalAssetsUrl . 'mgr/article/update/panel.js');  
        $this->addJavascript($myjournalAssetsUrl . 'mgr/article/update/resource.js');  
        
        $this->addHtml('<script type="text/javascript"> 
        Ext.onReady(function() {            
            MyJournal.assets_url = "'.$myjournalAssetsUrl.'";
            MyJournal.connector_url = "'.$connectorUrl.'";
            MyJournal.resource_id = '.$this->resource->get('id').';
            MyJournal.preview_url = "'.$this->previewUrl.'";
            MyJournal.record = '.$this->modx->toJSON($this->resourceArray).';
            MyJournal.tvs = '.$this->modx->toJSON($this->tvElements).';
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
        return 'Edit article';
    }
    
    public function process(array $scriptProperties = array()) {
        $placeholders = array();
        $reloadData = $this->getReloadData();

        $loaded = $this->getResource();
        if ($loaded !== true) {
            return $this->failure($loaded);
        }
        if(is_array($reloadData) && !empty($reloadData)) {
            $this->resource->fromArray($reloadData);
        }

        /* get context */
        $this->setContext();
        if (!$this->context) { return $this->failure($this->modx->lexicon('access_denied')); }

        /* check for locked status */
        $this->checkForLocks();

        /* set template overrides */
        if (isset($scriptProperties['template'])) $this->resource->set('template',$scriptProperties['template']);

        $this->setParent();

        /* invoke OnDocFormRender event */
        $this->fireOnRenderEvent();

        /* check permissions */
        $this->setPermissions();

        /* register FC rules */
        $this->resourceArray = $this->resource->toArray();
        $overridden = $this->checkFormCustomizationRules($this->resource);
        $this->resourceArray = array_merge($this->resourceArray,$overridden);

        $this->resourceArray['published'] = intval($this->resourceArray['published']) == 1 ? true : false;
        $this->resourceArray['hidemenu'] = intval($this->resourceArray['hidemenu']) == 1 ? true : false;
        $this->resourceArray['isfolder'] = intval($this->resourceArray['isfolder']) == 1 ? true : false;
        $this->resourceArray['richtext'] = intval($this->resourceArray['richtext']) == 1 ? true : false;
        $this->resourceArray['searchable'] = intval($this->resourceArray['searchable']) == 1 ? true : false;
        $this->resourceArray['cacheable'] = intval($this->resourceArray['cacheable']) == 1 ? true : false;
        $this->resourceArray['deleted'] = intval($this->resourceArray['deleted']) == 1 ? true : false;
        $this->resourceArray['uri_override'] = intval($this->resourceArray['uri_override']) == 1 ? true : false;
        if (!empty($this->resourceArray['parent'])) {
            if ($this->parent->get('id') == $this->resourceArray['parent']) {
                $this->resourceArray['parent_pagetitle'] = $this->parent->get('pagetitle');
            } else {
                $overriddenParent = $this->modx->getObject('modResource',$this->resourceArray['parent']);
                if ($overriddenParent) {
                    $this->resourceArray['parent_pagetitle'] = $overriddenParent->get('pagetitle');
                }
            }
        }

        /* get TVs */
        $this->resource->set('template',$this->resourceArray['template']);

        if (!empty($reloadData)) {
            $this->resourceArray['resourceGroups'] = array();
            $this->resourceArray['resource_groups'] = $this->modx->fromJSON($this->resourceArray['resource_groups']);
            foreach ($this->resourceArray['resource_groups'] as $resourceGroup) {
                $this->resourceArray['resourceGroups'][] = array(
                    $resourceGroup['id'],
                    $resourceGroup['name'],
                    $resourceGroup['access'],
                );
            }
            unset($this->resourceArray['resource_groups']);
        } else {
            $this->getResourceGroups();
        }

        $this->prepareResource();
        $this->getPreviewUrl();

        /* single-use token for reloading resource */
        $this->setResourceToken();

        $this->setPlaceholder('resource',$this->resource);
        return $placeholders;
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