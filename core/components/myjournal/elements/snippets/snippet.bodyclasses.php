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
 * Simple helper snippet to get a space separated list of informations related to the browsr currently used
 */
$bodyClasses = array();

/* Browser detection */
if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx') !== false ) {
        $bodyClasses[] = 'lynx';
    } elseif ( stripos($_SERVER['HTTP_USER_AGENT'], 'chrome') !== false ) {
        $bodyClasses[] = 'chrome';
    } elseif ( stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false ) {
        $bodyClasses[] = 'safari';
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko') !== false ) {
        $bodyClasses[] = 'gecko';
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false ) {
        $bodyClasses[] = 'msie';        
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false ) {
        $bodyClasses[] = 'msie';        
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false ) {
        $bodyClasses[] = 'opera';
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Nav') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/4.') !== false ) {
        $bodyClasses[] = 'ns4';
    }
}
if ( in_array('safari',$bodyClasses) && stripos($_SERVER['HTTP_USER_AGENT'], 'mobile') !== false ){
    $bodyClasses[] = 'iphone';
}
if ( in_array('msie',$bodyClasses) ){
    if( strpos($_SERVER['HTTP_USER_AGENT'], 'Win') !== false ) $bodyClasses[] = 'windows';
    if( strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false ) $bodyClasses[] = 'osx';
}
return implode(' ', $bodyClasses);