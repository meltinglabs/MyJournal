<?php
/**
 * MyJournal
 *
 * Copyright 2011-12 by Shaun McCormick <shaun+myjournal@modx.com>
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
 * The base service class for MyJournal.
 *
 * @package myjournal
 */
class MyJournalService {
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;
    /**
     * An array of configuration properties
     * @var array $config
     */
    public $config;
    /**
     * An array of cached chunks used for faster processing
     * 
     * @var array $chunks
     */
    public $chunks;

    /**
     * Create an instance of MyJournalService.
     *
     * @param modX $modx A reference to the modX object
     * @param array $config A configuration array
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('myjournal.core_path',$config,$this->modx->getOption('core_path').'components/myjournal/');
        $assetsUrl = $this->modx->getOption('myjournal.assets_url',$config,$this->modx->getOption('assets_url').'components/myjournal/');
        $connectorUrl = $assetsUrl.'connector.php';

        $this->config = array_merge(array(
            'assets_url' => $assetsUrl,
            'connector_url' => $connectorUrl,

            'core_path' => $corePath,
            'model_path' => $corePath.'model/',
            'element_path' => $corePath.'elements/',
            'snippet_path' => $corePath.'elements/snippets/',
            'tvs_path' => $corePath.'elements/tvs/',
            'chunks_path' => $corePath.'elements/chunks/',
            'chunk_suffix' => '.chunk.tpl',
            'processors_path' => $corePath.'processors/',
        ),$config);
        // $this->modx->lexicon->load('myjournal:default');
    }

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name,array $properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            $chunk = $this->modx->getObject('modChunk',array('name' => $name),true);
            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name,$this->config['chunk_suffix']);
                if ($chunk == false) return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }

    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl by default.
     * @param string $suffix The suffix to add to the chunk filename.
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name,$suffix = '.tpl') {
        $chunk = false;
        $f = $this->config['chunks_path'].strtolower($name).$suffix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            /** @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

}