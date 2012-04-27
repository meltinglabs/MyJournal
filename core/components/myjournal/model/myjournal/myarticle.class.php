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
     * @since 0.71
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
    
    /**
     * Get the controller path for our MyArticle type.
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
    
    /**
     * Override modResource::process to set some custom placeholders for the Resource when rendering it in the front-end.
     * {@inheritDoc}
     * @return string
     */
    public function process() {
        /* Might be useful - Using a 3rd party nippet */
        $this->xpdo->setPlaceholder('layout_prefix', 'single-');
        $this->xpdo->setPlaceholder('layout_suffix', '-single');
        $this->setCommentTags();
        $this->_content = parent::process();        
        return $this->_content;        
    }
    
    /**
     * Set comment tag call
     */
    public function setCommentTags(){
        $this->setTotalCommentTag();
        $settings = array();
        
        $id = $this->get('id');
        $commentsPh = $this->xpdo->getOption('myarticle.comments', $settings, 'comments');
        $options = array(
            'thread' => $this->xpdo->getOption('myarticle.thread', $settings, "myarticle-{$id}"),
            'useCss' => false,
            'altRowCss' => 'alt',
            'nameField' => 'name',
            'tplComment' => 'myjournal/comment.tpl',
            'tplComments' => 'myjournal/comments.tpl',
        );
        $this->setTagCall('Quip', $commentsPh, false, $options);
        
        $replyPh = $this->xpdo->getOption('myarticle.comment_reply', $settings, 'comment_reply');
        $options = array_merge(array(
            'tplAddComment ' => 'myjournal/comment_reply.tpl',
        ), $options);
        $this->setTagCall('QuipReply', $replyPh, false, $options);        
    }
    
    /**
     * Set total comment tag call
     */
    public function setTotalCommentTag(){        
        $id = $this->get('id');
        $settings = array();
        $totalCommentsPh = $this->xpdo->getOption('total_comments', $settings, 'total_comments');
        $options = array(
            'thread' => $this->xpdo->getOption('myarticle.thread', $settings, "myarticle-{$id}"),
        );
        $this->setTagCall('QuipCount', $totalCommentsPh, false, $options);
    }
    
    /**
     * Set modx tags for the specified placeholder
     *
     * @param string $name The snippet name
     * @param string $targetPlaceholder The placeholder to set
     * @param boolean $cached Whether the snippet call should be cached or not
     * @param array $options An array of options to override default properties
     */
    public function setTagCall($name, $placeholder, $cached = false, $options = array()){
        $tags = ($cached) ? "[[": "[[!";
        $tags .= $name."?";        
        foreach($options as $key => $value){
            $tags .= "\n    &{$key}=`{$value}`";
        }    
        $tags .= "\n]]";
        /* Debugging tag call */
        if($this->xpdo->getOption('myarticle.debug_tag_call', null, false)){
            $debugTags = str_replace('[[','&#91;&#91;', $tags);
            $debugTags = str_replace(']]','&#93;&#93;', $debugTags);
            $this->xpdo->setPlaceholder('debug_'.$placeholder, '<pre>'. $debugTags .'</pre>');
        }
        $this->xpdo->setPlaceholder($placeholder, $tags);        
    }    
    
    /**
     * Load the taxonomy package for xPDO.
     */
    public function loadTaxonomy(){
        $this->xpdo->addPackage('taxonomy',$this->xpdo->getOption('myjournal.model_path',null,$this->xpdo->getOption('core_path').'components/myjournal/model/'));
    }
    
    /**
     * Get a list of tags from the taxonomy table
     *
     * @param boolean $loadAllTags Load all available tags or only the ags for current MyArticle object
     * @param string $valueField The value field to set the array value to (table {modx_prefix}_terms)
     * @return string The requested tag list
     */
    public function getTags($loadAllTags = false, $valueField = 'alias'){
        $tagsList = array();
        $query = $this->xpdo->newQuery('Terms');
        $query->leftJoin('TermTaxonomy', 'TermTaxonomy');
        $where = array('TermTaxonomy.type' => 'tags');
        if(!$loadAllTags){
            $query->leftJoin('TermRelationships', 'Attached');
            $where['Attached.owner_id'] = $this->get('id');
        }
        $query->where($where);
        $rows = $this->xpdo->getCollection('Terms', $query);
        foreach($rows as $row){
            $tagsList[$row->get('value')] = $row->get($valueField);               
        }
        return $tagsList;
    }
    
    /**
     * Save a list of tags to the database
     *
     * @param string $tags The list of tags for current MyArticle object
     */
    public function saveTags($tags){           
        $new = array();
        /* add package */
        $this->loadTaxonomy();
        /* get all existing tags */
        $tagsList = $this->getTags(true, 'id');

        /* Submitted tag list */
        $taxonomy = $tags;
        $submittedTagsList = explode(',', $taxonomy['tags']);
        
        /* Trim all array values */
        array_walk($submittedTagsList, create_function('&$val', '$val = trim($val);'));
        
        if(!empty($submittedTagsList)){
            foreach($submittedTagsList as $value){     
                /* Add new tags - And creta the taxonomy along */
                if(!array_key_exists($value, $tagsList)){           
                    $tag = $this->xpdo->newObject('Terms');
                    $tag->set('value', $value);
                    $tag->save(); 
                    
                    /* Add taxonomy type */
                    $taxonomy = $this->xpdo->newObject('TermTaxonomy');
                    $taxonomy->set('type', 'tags');
                    $taxonomy->set('description', 'Tags');                
                    $tag->addOne($taxonomy);
                    
                    /* Add relationship */
                    $attached = $this->xpdo->newObject('TermRelationships');
                    $attached->set('owner_id', $this->get('id'));
                    $attached->set('term_id', $tag->get('id'));                
                    $tag->addMany($attached);
                    
                    $tag->save();
                    /* Prevent several attempt to save a tag */
                    $tagsList[$value] = $tag->get('id');
                } else {
                    /* The tag term already exist, should we add a new relationship ? */
                    $hasRelationship = $this->xpdo->getObject('TermRelationships', array(
                        'owner_id' => $this->get('id'),
                        'term_id' => $tagsList[$value],
                    ));
                    if(!$hasRelationship){
                        $attached = $this->xpdo->newObject('TermRelationships');
                        $attached->set('owner_id', $this->get('id'));
                        $attached->set('term_id', $tagsList[$value]);
                        $attached->save();
                    }
                }             
            }
        }
        
        /* Get only assigned tags */
        $tagsList = $this->getTags(false, 'id');
        
        /* Check one existing and attached tags as not been submitted */
        foreach($tagsList as $key => $value){
            /* Remove tag relationship  */
            if(!in_array($key, $submittedTagsList)){                
                $attached = $this->xpdo->getObject('TermRelationships', array(
                    'owner_id' => $this->get('id'),
                    'term_id' => $value,
                ));
                if($attached) $attached->remove();
            }  
        }
    }
}

/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the MyArticle type
 *
 * @package myjournal
 */
class MyArticleCreateProcessor extends modResourceCreateProcessor {
    /** @var MyArticle $object */
    public $object;
    
    /**
     * Clears the container cache to ensure that the container listing is updated
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
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the MyArticle type
 *
 * @package myjournal
 */
class MyArticleUpdateProcessor extends modResourceUpdateProcessor {
    /** @var MyArticle $object */
    public $object;
    
    public function beforeSet() {
        $this->setProperty('clearCache',true);
        return parent::beforeSet();
    }
    
    public function afterSave() {
        $afterSave = parent::afterSave();
        $this->clearContainerCache();
        $this->object->saveTags($this->getProperty('taxonomy'));
        return $afterSave;
    }
    
    /**
     * Clears the container cache to ensure that the container listing is updated
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