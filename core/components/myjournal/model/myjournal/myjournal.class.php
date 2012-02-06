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
     * Provide the name of this CRT.
     * {@inheritDoc}
     * @return string
     */
	// public function getResourceTypeName() {
		// $this->xpdo->lexicon->load('myjournal:default');
        // return $this->xpdo->lexicon('myjournal.container');
	// }
	
	public function getContent(array $options = array()) {
		$content = parent::getContent($options);
		$content .= '[[!myArticles]]';
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
}
class MyJournalUpdateProcessor extends modResourceUpdateProcessor {
	/** @var MyJournal $object */
    public $object;
}