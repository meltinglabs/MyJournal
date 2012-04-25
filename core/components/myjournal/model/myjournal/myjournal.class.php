<?php
/**
 * MyJournal
 *
 * Copyright 2012 by Stephane Boulard <lossendae@gmail.com>
 *
 * MyJournal is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * MyJournal is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MyJournal; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package myjournal
 */
require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

/**
 * @package myjournal
 */
class MyJournal extends modResource {
    /** @var modX $xpdo */
    public $xpdo;
    public $allowListingInClassKeyDropdown = false;
    public $showInContextMenu = true;
    
    /**
     * Override modResource::__construct to ensure a few specific fields are forced to be set.
     * @param xPDO $xpdo
     */
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','MyJournal');
        $this->set('hide_children_in_tree',true);
    }
    
    /**
     * Get the controller path for our Articles type.
     * 
     * {@inheritDoc}
     * @static
     * @param xPDO $modx
     * @return string
     */
    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('myjournal.core_path',null,$modx->getOption('core_path').'components/myjournal/').'controllers/container/';
    }
    
    /**
     * Provide the name of this CRT.
     * {@inheritDoc}
     * @return string
     */
    public function getResourceTypeName() {
        return 'myjournal';
    }
    
    /**
     * Provide the custom context menu for Articles.
     * {@inheritDoc}
     * @return array
     */
    public function getContextMenuText() {
        return array(
            'text_create' => 'Journal',
            'text_create_here' => 'Create a Journal Here',
        );
    }
    
    
    /**
     * @param array $node
     * @return array
     */
    public function prepareTreeNode(array $node = array()) {
        // $this->xpdo->lexicon->load('articles:default');
        $menu = array();
        $menu[] = array(
            'text' => '<b>'.$this->get('pagetitle').'</b>',
            'handler' => 'Ext.emptyFn',
        );
        // $menu[] = '-';
        // $menu[] = array(
            // 'text' => $this->xpdo->lexicon('articles.articles_manage'),
            // 'handler' => 'this.editResource',
        // );
        // $menu[] = array(
            // 'text' => $this->xpdo->lexicon('articles.articles_write_new'),
            // 'handler' => 'function(itm,e) { itm.classKey = "Article"; this.createResourceHere(itm,e); }',
        // );
        // $menu[] = '-';
        // if ($this->get('published')) {
            // $menu[] = array(
                // 'text' => $this->xpdo->lexicon('articles.container_unpublish'),
                // 'handler' => 'this.unpublishDocument',
            // );
        // } else {
            // $menu[] = array(
                // 'text' => $this->xpdo->lexicon('articles.container_publish'),
                // 'handler' => 'this.publishDocument',
            // );
        // }
        // if ($this->get('deleted')) {
            // $menu[] = array(
                // 'text' => $this->xpdo->lexicon('articles.container_undelete'),
                // 'handler' => 'this.undeleteDocument',
            // );
        // } else {
            // $menu[] = array(
                // 'text' => $this->xpdo->lexicon('articles.container_delete'),
                // 'handler' => 'this.deleteDocument',
            // );
        // }
        // $menu[] = '-';
        // $menu[] = array(
            // 'text' => $this->xpdo->lexicon('articles.articles_view'),
            // 'handler' => 'this.preview',
        // );

        $node['menu'] = array('items' => $menu);
        return $node;
    }
    
     /**
     * Override modResource::process to set some custom placeholders for the Resource when rendering it in the front-end.
     * {@inheritDoc}
     * @return string
     */
    public function process() {
        $this->setPostListingCall();
        $this->_content = parent::process();
        return $this->_content;
    }
    
    public function setPostListingCall(){
        $where = array('class_key' => 'MyArticle');
        //@TODO : This will be the settings for myjournal later
        $settings = array();
        $articlesPlaceholder = $this->xpdo->getOption('articles_placeholder', $settings, 'articles');
        
        //@TODO allow user and/or template to override/add options with runSnippet
        $options = array(
            'elementClass' => 'modSnippet',
            'element' => 'getArchives',
            'makeArchive' => 0,
            'cache' => 0,
            'parents' => $this->get('id'),
            'where' => $this->xpdo->toJSON($where),
            'showHidden' => 1,
            'includeContent' => 1,
            'includeTVsList' => $this->xpdo->getOption('include_tvs_list', $settings, ''),
            'processTVs' => $this->xpdo->getOption('process_tvs', $settings, 0),
            'processTVsList' => $this->xpdo->getOption('process_tvs_list', $settings, ''),
            'tagKey' => $this->xpdo->getOption('tag_tv_name', $settings, 'tags'),
            'tagSearchType' => $this->xpdo->getOption('tag_tv_search_mode', $settings, 'contains'),
            'sortby' => $this->xpdo->getOption('sortby', $settings, 'publishedon'),
            'sortdir' => $this->xpdo->getOption('sortdir', $settings, 'DESC'),
            'limit' => $this->xpdo->getOption('post_per_page', $settings, 10),
            'pageLimit' => $this->xpdo->getOption('page_limit', $settings, 5),
            'pageVarKey' => $this->xpdo->getOption('page_var_key', $settings, 'page'),
            'pageNavVar' => $this->xpdo->getOption('page_nav_var', $settings, 'page.nav'),
            'totalVar' => $this->xpdo->getOption('page_total_var', $settings, 'total'),
            'offset' => $this->xpdo->getOption('page_offset', $settings, 0),
            'tpl' => $this->xpdo->getOption('articles_tpl', $settings, 'myjournal/article.tpl'),
        );
        
        if($this->xpdo->getOption('set_page_nav_placeholder', $settings, true)){
            $this->xpdo->setPlaceholder('paging','[[!+page.nav:notempty=`
                <div class="paging">
                <ul class="pageList">
                  [[!+page.nav]]
                </ul>
                </div>
            `]]');
        }
        
        $this->setTagCall('getPage', $articlesPlaceholder, false, $options);
    }
    
    public function setTagCall($name, $placeholder, $cached = false, $options = array()){
        $tags = ($cached) ? "[[": "[[!";
        $tags .= $name."?";        
        foreach($options as $key => $value){
            $tags .= "\n    &{$key}=`{$value}`";
        }    
        $tags .= "\n]]";
        /* Debugging tag call */
        if($this->xpdo->getOption('myjournal.debug_tag_call', null, false)){
            $debugTags = str_replace('[[','&#91;&#91;', $tags);
            $debugTags = str_replace(']]','&#93;&#93;', $debugTags);
            $this->xpdo->setPlaceholder('debug_tag_'.$placeholder, '<pre>'. $debugTags .'</pre>');
        }
        $this->xpdo->setPlaceholder($placeholder, $tags);
    }
    
    public function getContent(array $options = array()) {
        $content = parent::getContent($options);
        return $content;
    }
}
class MyJournalCreateProcessor extends modResourceCreateProcessor {
    /** @var MyJournal $object */
    public $object;
    
    /**
     * Override modResourceCreateProcessor::afterSave to provide custom functionality, saving the container settings to a
     * custom field in the manager
     * {@inheritDoc}
     * @return boolean
     */
    public function beforeSave() {
        $this->object->set('content','[[!MyJournal]]');
        $this->object->set('class_key','MyJournal');
        $this->object->set('richtext',false);
        /* We need to set a compatible remark theme as soon as possible */
        $this->object->set('template',1);
        $this->object->set('cacheable',true);
        $this->object->set('isfolder',true);
        $this->object->set('published',false);
        $this->object->set('deleted',false);
        return parent::beforeSave();
    }
    
    /**
     * Override modResourceCreateProcessor::afterSave to provide custom functionality
     * {@inheritDoc}
     * @return boolean
     */
    public function afterSave() {
        return parent::afterSave();
    }
    
    /**
     * Save the Resource Groups on the object
     * 
     * @return void
     */
    public function saveResourceGroups() {
        $attributted = array();
        $groups = $this->modx->getCollection('modResourceGroupResource', array( 'document' => $this->object->get('id') ));
        if($groups){
            foreach($groups as $group){
                $attributted[] = $group->get('id');
            }
        }
        $nbAttributted = count($attributted);
        $resourceGroups = $this->getProperty('resource_groups', array());
        $nbResourceGroups = count($resourceGroups);        
        if($nbResourceGroups > 0){
            /* assigning to group */
            foreach($resourceGroups as $id){
                if(!in_array($id, $attributted)){
                    $resourceGroupResource = $this->modx->newObject('modResourceGroupResource');
                    $resourceGroupResource->set('document_group',$id);
                    $resourceGroupResource->set('document',$this->object->get('id'));
                    if ($resourceGroupResource->save()) {
                        $this->modx->invokeEvent('OnResourceAddToResourceGroup',array(
                            'mode' => 'resource-update',
                            'resource' => &$this->object,
                            'resourceGroup' => &$resourceGroup,
                        ));
                    }
                }
            }
        }          
        if($nbAttributted > 0){
            /* if removing access to group */
            foreach($attributted as $id){
                if(!in_array($id, $resourceGroups)){
                    $resourceGroupResource = $this->modx->getObject('modResourceGroupResource',array(
                        'document_group' => $id,
                        'document' => $this->object->get('id'),
                    ));
                    if ($resourceGroupResource && $resourceGroupResource instanceof modResourceGroupResource) {
                        if ($resourceGroupResource->remove()) {
                            $this->modx->invokeEvent('OnResourceRemoveFromResourceGroup',array(
                                'mode' => 'resource-update',
                                'resource' => &$this->object,
                                'resourceGroup' => &$resourceGroup,
                            ));
                        }
                    }
                }
            }
        }
    }
}
class MyJournalUpdateProcessor extends modResourceUpdateProcessor {
    /** @var MyJournal $object */
    public $object;
    
    /**
     * If specified, set the Resource Groups attached to the Resource
     * @return mixed
     */
    public function setResourceGroups() {
        $attributted = array();
        $groups = $this->modx->getCollection('modResourceGroupResource', array( 'document' => $this->object->get('id') ));
        if($groups){
            foreach($groups as $group){
                $attributted[] = $group->get('id');
            }
        }
        $nbAttributted = count($attributted);
        $resourceGroups = $this->getProperty('resource_groups', array());
        $nbResourceGroups = count($resourceGroups);          
        if($nbResourceGroups > 0){
            /* assigning to group */
            foreach($resourceGroups as $id){
                if(!in_array($id, $attributted)){
                    $resourceGroupResource = $this->modx->newObject('modResourceGroupResource');
                    $resourceGroupResource->set('document_group',$id);
                    $resourceGroupResource->set('document',$this->object->get('id'));
                    if ($resourceGroupResource->save()) {
                        $this->modx->invokeEvent('OnResourceAddToResourceGroup',array(
                            'mode' => 'resource-update',
                            'resource' => &$this->object,
                            'resourceGroup' => &$resourceGroup,
                        ));
                    }
                }
            }
        }          
        if($nbAttributted > 0){
            /* if removing access to group */
            foreach($attributted as $id){
                if(!in_array($id, $resourceGroups)){
                    $resourceGroupResource = $this->modx->getObject('modResourceGroupResource',array(
                        'document_group' => $id,
                        'document' => $this->object->get('id'),
                    ));
                    if ($resourceGroupResource && $resourceGroupResource instanceof modResourceGroupResource) {
                        if ($resourceGroupResource->remove()) {
                            $this->modx->invokeEvent('OnResourceRemoveFromResourceGroup',array(
                                'mode' => 'resource-update',
                                'resource' => &$this->object,
                                'resourceGroup' => &$resourceGroup,
                            ));
                        }
                    }
                }
            }
        }    
        return $resourceGroups;
    }
}