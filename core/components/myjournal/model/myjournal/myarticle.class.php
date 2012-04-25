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

class MyArticle extends modResource {
    /** @var modX $xpdo */
    public $xpdo;
    public $allowListingInClassKeyDropdown = false;
    public $showInContextMenu = false;
    
    /**
     * Override modResource::__construct to ensure a few specific fields are forced to be set.
     * @param xPDO $xpdo
     */
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','MyArticle');
        $this->set('show_in_tree',false);
        $this->set('richtext',true);
        $this->set('searchable',true);
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
        return $modx->getOption('myjournal.core_path',null,$modx->getOption('core_path').'components/myjournal/').'controllers/article/';
    }
    
    /**
     * Provide the name of this CRT.
     * {@inheritDoc}
     * @return string
     */
    public function getResourceTypeName() {
        return 'myarticle';
    }
}

class MyArticleCreateProcessor extends modResourceCreateProcessor {
    /** @var MyArticle $object */
    public $object;
    
    /**
     * Clears the container cache to ensure that the container listing is updated
     * @return void
     */
    public function clearContainerCache() {
        $this->modx->cacheManager->refresh(array(
            'db' => array(),
            'auto_publish' => array('contexts' => array($this->object->get('context_key'))),
            'context_settings' => array('contexts' => array($this->object->get('context_key'))),
            'resource' => array('contexts' => array($this->object->get('context_key'))),
        ));
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
class MyArticleUpdateProcessor extends modResourceUpdateProcessor {
    /** @var MyArticle $object */
    public $object;
    
    /**
     * Clears the container cache to ensure that the container listing is updated
     * @return void
     */
    public function clearContainerCache() {
        $this->modx->cacheManager->refresh(array(
            'db' => array(),
            'auto_publish' => array('contexts' => array($this->object->get('context_key'))),
            'context_settings' => array('contexts' => array($this->object->get('context_key'))),
            'resource' => array('contexts' => array($this->object->get('context_key'))),
        ));
    }
    
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