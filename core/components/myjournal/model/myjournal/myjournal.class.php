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
     * Get the controller path for our MyJournal type.
     * 
     * {@inheritDoc}
     * @static
     * @param xPDO $modx
     * @return string
     */
    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('myjournal.core_path',null,$modx->getOption('core_path').'components/myjournal/').'controllers/container/';
    }    
    
    public function get($k, $format = null, $formatTemplate= null){
        switch($k){
            case 'autop':
                $content = $this->autop( $this->get('content') );
                $value = $content;
                break;
            default:
                $value = parent::get($k,$format,$formatTemplate);
                break;
        }
        return $value;
    }
    
    /**
     * Replaces double line-breaks with paragraph elements.
     *
     * A group of regex replaces used to identify text formatted with newlines and
     * replace double line-breaks with HTML paragraph tags. The remaining
     * line-breaks after conversion become <<br />> tags, unless $br is set to '0'
     * or 'false'.
     *
     * @param string $pee The text which has to be formatted.
     * @param bool $br Optional. If set, this will convert all remaining line-breaks after paragraphing. Default true.
     * @return string Text which has been converted into correct paragraph tags.
     */
    public function autop($pee, $br = true) {
        $pre_tags = array();

        if ( trim($pee) === '' )
            return '';

        $pee = $pee . "\n"; // just to make things a little easier, pad the end

        if ( strpos($pee, '<pre') !== false ) {
            $pee_parts = explode( '</pre>', $pee );
            $last_pee = array_pop($pee_parts);
            $pee = '';
            $i = 0;

            foreach ( $pee_parts as $pee_part ) {
                $start = strpos($pee_part, '<pre');

                // Malformed html?
                if ( $start === false ) {
                    $pee .= $pee_part;
                    continue;
                }

                $name = "<pre></pre>";
                $pre_tags[$name] = substr( $pee_part, $start ) . '</pre>';

                $pee .= substr( $pee_part, 0, $start ) . $name;
                $i++;
            }

            $pee .= $last_pee;
        }

        $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
        $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
        if ( strpos($pee, '<object') !== false ) {
            $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
            $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
        }
        $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        // make paragraphs, including one at the end
        $pees = preg_split('/\n\s*\n/', $pee, -1, true);
        // $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pee = '';
        foreach ( $pees as $tinkle )
            $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        $pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
        $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
        if ( $br ) {
            // $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $pee);
            $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
            $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
        }
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
        $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
        $pee = preg_replace( "|\n</p>$|", '</p>', $pee );

        if ( !empty($pre_tags) )
            $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);

        return $pee;
    }

    public function set($k, $v= null, $vType= '') {
        $oldAlias = false;
        if ($k == 'alias') {
            $oldAlias = $this->get('alias');
        }
        $set = parent::set($k,$v,$vType);
        if ($this->isDirty('alias') && !empty($oldAlias)) {
            $this->oldAlias = $oldAlias;
        }
        return $set;
    }
    
    public function save($cacheFlag = null) {
        $isNew = $this->isNew();
        $saved = parent::save($cacheFlag);
        if ($saved && !$isNew && !empty($this->oldAlias)) {
            $newAlias = $this->get('alias');
            $saved = $this->updateChildrenURIs($newAlias,$this->oldAlias);
        }
        return $saved;
    }
    
    /**
    * Update all Articles URIs to reflect the new blog alias
    *
    * @param string $newAlias
    * @param string $oldAlias
    * @return bool
    */
    public function updateChildrenURIs($newAlias,$oldAlias) {
        $useMultiByte = $this->getOption('use_multibyte',null,false) && function_exists('mb_strlen');
        $encoding = $this->getOption('modx_charset',null,'UTF-8');
        $oldAliasLength = ($useMultiByte ? mb_strlen($oldAlias,$encoding) : strlen($oldAlias)) + 1;
        $uriField = $this->xpdo->escape('uri');

        $sql = 'UPDATE '.$this->xpdo->getTableName('MyArticle').'
        SET '.$uriField.' = CONCAT("'.$newAlias.'",SUBSTRING('.$uriField.','.$oldAliasLength.'))
        WHERE
        '.$this->xpdo->escape('parent').' = '.$this->get('id').'
        AND SUBSTRING('.$uriField.',1,'.$oldAliasLength.') = "'.$oldAlias.'/"';
        $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG,$sql);
        $this->xpdo->exec($sql);
        return true;
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
     * Provide the custom context menu for MyJournal.
     * {@inheritDoc}
     * @return array
     */
    public function getContextMenuText() {
        return array(
            'text_create' => 'Journal',
            'text_create_here' => 'Create a new Journal Here',
        );
    }    
    
    /**
     * @param array $node
     * @return array
     */
    public function prepareTreeNode(array $node = array()) {
        // $this->xpdo->lexicon->load('myjournal:default');
        $menu = array();
        $menu[] = array(
            'text' => 'Edit Journal',
            'handler' => 'this.editResource',
        );
        $menu[] = array(
            'text' => 'View Journal (Front end)',
            'handler' => 'this.preview',
        );
        $menu[] = '-';
        if ($this->get('published')) {
            $menu[] = array(
                'text' => 'Publish Journal',
                'handler' => 'this.unpublishDocument',
            );
        } else {
            $menu[] = array(
                'text' => 'Unpublish Journal',
                'handler' => 'this.publishDocument',
            );
        }
        if ($this->get('deleted')) {
            $menu[] = array(
                'text' => 'Undelete Journal',
                'handler' => 'this.undeleteDocument',
            );
        } else {
            $menu[] = array(
                'text' => 'Delete Journal',
                'handler' => 'this.deleteDocument',
            );
        }
        $menu[] = '-';
        $menu[] = array(
            'text' => 'Create a new Article (MyArticle)',
            'handler' => 'function(itm,e) { itm.classKey = "MyArticle"; this.createResourceHere(itm,e); }',
        );
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
    
    /**
     * Get the getPage and getArchives call to display listings of posts on the container.
     *
     * @param string $placeholderPrefix
     * @return string
     */
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
            'tpl' => $this->xpdo->getOption('articles_tpl', $settings, 'myjournal/articles.tpl'),
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
    
    /**
     * Set modx tags for the specified placeholder
     *
     * @param string $name The snippet name
     * @param string $targetPlaceholder The placeholder to set
     * @param boolean $cached Whether the snippet call should be cached or not
     * @param array $options An array of options to override default properties
     */
    public function setTagCall($name, $targetPlaceholder, $cached = false, $options = array()){
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
            $this->xpdo->setPlaceholder('debug_tag_'.$targetPlaceholder, '<pre>'. $debugTags .'</pre>');
        }
        $this->xpdo->setPlaceholder($targetPlaceholder, $tags);
        /* Might be useful - Using a 3rd party nippet */
        $this->xpdo->setPlaceholder('layout_prefix', 'articles-list-');
        $this->xpdo->setPlaceholder('layout_suffix', '-articles-list');
    }
    
    /**
     * Get an array of settings for the resource.
     * @return array
     */
    public function getSettings() {
        $settings = $this->getProperties('articles');
        if (!empty($settings)) {
            $settings = is_array($settings) ? $settings : $this->xpdo->fromJSON($settings);
        }
        return !empty($settings) ? $settings : array();
    }
    
    /**
     * @param array $options
     * @return string
     */
    public function getContent(array $options = array()) {
        $content = parent::getContent($options);
        return $content;
    }
}

/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the MyJournal type
 *
 * @package myjournal
 */
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
        $this->object->set('content','[[+articles]]');
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
     * Override the default implementation to use results from checkboxes instead of a grid store
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

/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the MyJournal type
 *
 * @package myjournal
 */
class MyJournalUpdateProcessor extends modResourceUpdateProcessor {
    /** @var MyJournal $object */
    public $object;
    
    /**
     * If specified, set the Resource Groups attached to the Resource
     * Override the default implementation to use results from checkboxes instead of a grid store
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