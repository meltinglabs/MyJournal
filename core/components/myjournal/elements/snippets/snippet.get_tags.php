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
 * Retreive tags list for a MyArticle CRT
 */
$tags = array();
$id = $modx->getOption('id', $scriptProperties, null);
if(empty($id)){
    $classKey = $modx->resource->get('class_key');  
    if($classKey !== 'MyArticle'){
        return 'Only resource witn class_key <strong>"MyArticle"</strong> have acces to taxonomy right now. Get your act together!';    
    }
    if($classKey == 'MyArticle'){
        $modx->resource->loadTaxonomy();
        $tagsList = $modx->resource->getTags();
    }
} 

if(!empty($id)){
    $resource = $modx->getObject('MyArticle', $id);
    if($resource){
        $resource->loadTaxonomy();
        $tagsList = $resource->getTags();
    }
}
if(!empty($tagsList)){
    foreach($tagsList as $key => $value){
        /* @TODO maybe chunkify but not for now */
        $tags[] = "<a rel=\"tag\" href=\"#{$value}/\">{$key}</a>";
    }
}
return implode(', ', $tags);