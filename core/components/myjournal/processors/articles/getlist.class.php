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
/**
 * @package myjournal
 * @subpackage processors
 */
class ArticlesGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'MyArticle';
    public $defaultSortField = 'createdon';
    public $defaultSortDirection = 'DESC';
    public $checkListPermission = false;
    // public $objectType = 'myjournal';
    public $languageTopics = array('resource','myjournal:default');
    
     /**
     * @param xPDOObject|Article $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $resourceArray = parent::prepareRow($object);
        $resourceArray['preview_url'] = $this->getPreviewUrl($resourceArray['id'], $resourceArray['context_key']);
        return $resourceArray;
    }
    
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->leftJoin('modUser','CreatedBy');
        $parent = $this->getProperty('parent',null);
        if (!empty($parent)) {
            $c->where(array(
                'parent' => $parent,
            ));
        }
        $c->where(array(
            'class_key' => 'MyArticle',
        ));
        return $c;
    }
    
    public function prepareQueryAfterCount(xPDOQuery $c) {
        $c->select($this->modx->getSelectColumns('MyArticle','MyArticle'));
        $c->select(array(
            'createdby_username' => 'CreatedBy.username',
        ));
        return $c;
    }
    
    /**
     * Get url for resource for preview window
     * @return string
     */
    public function getPreviewUrl($id, $ctx) {
        $this->previewUrl = $this->modx->makeUrl($id, $ctx, '', 'full');
        return $this->previewUrl;
    }
}
return 'ArticlesGetListProcessor';