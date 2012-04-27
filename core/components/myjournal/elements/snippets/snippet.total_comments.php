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
 * Total comments snippet helper that wrap QuipCount and provide default implementation for thread
 */
$options = array();
$id = $modx->getOption('id', $scriptProperties, null);
$commentSnippet = $modx->getOption('snippet', $scriptProperties, 'QuipCount');
if(!empty($id)) {
    /** @var modSnippet $snippet */
    $snippet = $modx->getObject('modSnippet', array('name' => $commentSnippet));
    if ($snippet) {
        $snippet->setCacheable(false);
        $output = $snippet->process(array(
            'thread' => "myarticle-{$id}",
        ));
        return $output;
    }
}
return '';