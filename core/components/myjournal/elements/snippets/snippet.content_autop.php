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
 * A nice function helper that allow content to be nicely formatted
 */
$id = $modx->getOption('id', $scriptProperties, null);
$text = $modx->getOption('text', $scriptProperties, null);
$autop = '';
if(empty($id) && empty($text)){
    return $modx->resource->get('autop');
} 
/* Slow */
if(!empty($id)) {
    $obj = $modx->getObject('modResource', $id);
    if($obj){
        return $obj->get('autop');
    }
}
/* Faster */
if(!empty($text)) {
   return $modx->resource->autop($text);
}
return $autop;