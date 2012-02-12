<?php
/**
 * @var modX $modx
 */
require_once $modx->getOption('manager_path',null,MODX_MANAGER_PATH).'controllers/default/resource/update.class.php';
/**
 * @package myjournal
 */
class MyArticleUpdateManagerController extends ResourceUpdateManagerController {
	public $tvElements = array();

	public function loadCustomCssJs() {
		$managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $myjournalAssetsUrl = $this->modx->getOption('myjournal.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/myjournal/');
		$connectorUrl = $myjournalAssetsUrl.'connector.php';
		// $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($myjournalAssetsUrl . 'mgr/libs/jquery.1.7.1.min.js');	
		
		//Load last because if a TV load another jQuery lib, it cancel/conflict markitup registering with jQuery
		$this->addLastJavascript($myjournalAssetsUrl . 'mgr/libs/jquery.markitup.js');	
		
		$this->addJavascript($myjournalAssetsUrl . 'mgr/libs/sets/default/set.js');	
		$this->addJavascript($myjournalAssetsUrl . 'mgr/core/tv.js');	
		$this->addJavascript($myjournalAssetsUrl . 'mgr/article/update/panel.js');	
		$this->addJavascript($myjournalAssetsUrl . 'mgr/article/update/resource.js');		
		
		$this->addHtml('<script type="text/javascript"> 
		Ext.onReady(function() {			
			MyJournal.assets_url = "'.$myjournalAssetsUrl.'";
			MyJournal.connector_url = "'.$connectorUrl.'";
			MyJournal.resource_id = '.$this->resource->get('id').';
			MyJournal.record = '.$this->modx->toJSON($this->resourceArray).';
			MyJournal.tvs = '.$this->modx->toJSON($this->tvElements).';
			MODx.ctx = "'.$this->resource->get('context_key').'";
			MODx.add("myjournal-main-panel"); 			
		});</script>');		
		
		/* load RTE */
        $this->loadRichTextEditor();
		
		$this->addCss($myjournalAssetsUrl.'css/index.css');
		$this->addCss($myjournalAssetsUrl.'mgr/libs/skins/markitup/style.css');
		$this->addCss($myjournalAssetsUrl.'mgr/libs/sets/default/style.css');
	}
	
	public function getLanguageTopics() {
        return array('resource','myjournal:default');
    }
	
	/**
     * Return the pagetitle
     *
     * @return string
     */
    public function getPageTitle() {
        // return $this->modx->lexicon('articles.container_new');
        return 'Edit article';
    }
	
	public function process(array $scriptProperties = array()) {
        $placeholders = array();
        $reloadData = $this->getReloadData();

        $loaded = $this->getResource();
        if ($loaded !== true) {
            return $this->failure($loaded);
        }
        if(is_array($reloadData) && !empty($reloadData)) {
            $this->resource->fromArray($reloadData);
        }

        /* get context */
        $this->setContext();
        if (!$this->context) { return $this->failure($this->modx->lexicon('access_denied')); }

        /* check for locked status */
        $this->checkForLocks();

        /* set template overrides */
        if (isset($scriptProperties['template'])) $this->resource->set('template',$scriptProperties['template']);

        $this->setParent();

        /* invoke OnDocFormRender event */
        $this->fireOnRenderEvent();

        /* check permissions */
        $this->setPermissions();

        /* register FC rules */
        $this->resourceArray = $this->resource->toArray();
        $overridden = $this->checkFormCustomizationRules($this->resource);
        $this->resourceArray = array_merge($this->resourceArray,$overridden);

        $this->resourceArray['published'] = intval($this->resourceArray['published']) == 1 ? true : false;
        $this->resourceArray['hidemenu'] = intval($this->resourceArray['hidemenu']) == 1 ? true : false;
        $this->resourceArray['isfolder'] = intval($this->resourceArray['isfolder']) == 1 ? true : false;
        $this->resourceArray['richtext'] = intval($this->resourceArray['richtext']) == 1 ? true : false;
        $this->resourceArray['searchable'] = intval($this->resourceArray['searchable']) == 1 ? true : false;
        $this->resourceArray['cacheable'] = intval($this->resourceArray['cacheable']) == 1 ? true : false;
        $this->resourceArray['deleted'] = intval($this->resourceArray['deleted']) == 1 ? true : false;
        $this->resourceArray['uri_override'] = intval($this->resourceArray['uri_override']) == 1 ? true : false;
        if (!empty($this->resourceArray['parent'])) {
            if ($this->parent->get('id') == $this->resourceArray['parent']) {
                $this->resourceArray['parent_pagetitle'] = $this->parent->get('pagetitle');
            } else {
                $overriddenParent = $this->modx->getObject('modResource',$this->resourceArray['parent']);
                if ($overriddenParent) {
                    $this->resourceArray['parent_pagetitle'] = $overriddenParent->get('pagetitle');
                }
            }
        }

        /* get TVs */
        $this->resource->set('template',$this->resourceArray['template']);

        if (!empty($reloadData)) {
            $this->resourceArray['resourceGroups'] = array();
            $this->resourceArray['resource_groups'] = $this->modx->fromJSON($this->resourceArray['resource_groups']);
            foreach ($this->resourceArray['resource_groups'] as $resourceGroup) {
                $this->resourceArray['resourceGroups'][] = array(
                    $resourceGroup['id'],
                    $resourceGroup['name'],
                    $resourceGroup['access'],
                );
            }
            unset($this->resourceArray['resource_groups']);
        } else {
            $this->getResourceGroups();
        }

        $this->prepareResource();
        $this->tvElements = $this->loadTVs($reloadData);
		// echo '<pre>'.print_r($this->tvElements, true).'</pre>';

        $this->getPreviewUrl();

        /* single-use token for reloading resource */
        $this->setResourceToken();

        $this->setPlaceholder('resource',$this->resource);
        return $placeholders;
    }
	
	/**
     * Load the TVs for the Resource
     *
     * @param array $reloadData resource data passed if reloading
     * @return string The TV editing form
     */
    public function loadTVs($reloadData = array()) {
        $this->setPlaceholder('wctx',$this->resource->get('context_key'));
        $_GET['wctx'] = $this->resource->get('context_key');

        $this->fireOnTVFormRender();

        /* get categories */
        $c = $this->modx->newQuery('modCategory');
        $c->sortby('category','ASC');
        $categories = $this->modx->getCollection('modCategory',$c);
        $emptyCategory = $this->modx->newObject('modCategory');
        $emptyCategory->set('category',ucfirst($this->modx->lexicon('uncategorized')));
        $emptyCategory->id = 0;
        $categories[0] = $emptyCategory;
        $tvMap = array();
        $hidden = array();
        $templateId = $this->resource->get('template');
        if ($templateId && ($template = $this->modx->getObject('modTemplate', $templateId))) {
            $tvs = array();
            if ($template) {
                $c = $this->modx->newQuery('modTemplateVar');
                $c->query['distinct'] = 'DISTINCT';
                $c->select($this->modx->getSelectColumns('modTemplateVar', 'modTemplateVar'));
                $c->select($this->modx->getSelectColumns('modCategory', 'Category', 'cat_', array('category')));
                if(empty($reloadData)) {
                    $c->select($this->modx->getSelectColumns('modTemplateVarResource', 'TemplateVarResource', '', array('value')));
                }
                $c->select($this->modx->getSelectColumns('modTemplateVarTemplate', 'TemplateVarTemplate', '', array('rank')));
                $c->leftJoin('modCategory','Category');
                $c->innerJoin('modTemplateVarTemplate','TemplateVarTemplate',array(
                    'TemplateVarTemplate.tmplvarid = modTemplateVar.id',
                    'TemplateVarTemplate.templateid' => $templateId,
                ));
                $c->leftJoin('modTemplateVarResource','TemplateVarResource',array(
                    'TemplateVarResource.tmplvarid = modTemplateVar.id',
                    'TemplateVarResource.contentid' => $this->resource->get('id'),
                ));
                $c->sortby('cat_category,TemplateVarTemplate.rank,modTemplateVar.rank','ASC');
                $tvs = $this->modx->getCollection('modTemplateVar',$c);

                $reloading = !empty($reloadData) && count($reloadData) > 0;
                $this->modx->smarty->assign('tvcount',count($tvs));
                /** @var modTemplateVar $tv */
                foreach ($tvs as $tv) {
                    $v = '';
                    $tv->set('inherited', false);
                    $cat = (int)$tv->get('category');
                    $tvid = $tv->get('id');
                    if($reloading && array_key_exists('tv'.$tvid, $reloadData)) {
                        $v = $reloadData['tv'.$tvid];
                        $tv->set('value', $v);
                    } else {
                        $default = $tv->processBindings($tv->get('default_text'),$this->resource->get('id'));
                        if (strpos($tv->get('default_text'),'@INHERIT') > -1 && (strcmp($default,$tv->get('value')) == 0 || $tv->get('value') == null)) {
                            $tv->set('inherited',true);
                        }
                        if ($tv->get('value') == null) {
                            $v = $tv->get('default_text');
                            if ($tv->get('type') == 'checkbox' && $tv->get('value') == '') {
                                $v = '';
                            }
                            $tv->set('value',$v);
                        }
                    }

                    if ($tv->get('type') == 'richtext') {
                        $this->rteFields = array_merge($this->rteFields,array(
                            'tv' . $tv->get('id'),
                        ));
                    }
                    $inputForm = $tv->renderInput($this->resource->get('id'), array('value'=> $v));
                    if (empty($inputForm)) continue;

                    $tv->set('formElement',$inputForm);
                    if ($tv->get('type') != 'hidden') {
                        if (!isset($categories[$cat]->tvs) || !is_array($categories[$cat]->tvs)) {
                            $categories[$cat]->tvs = array();
                            $categories[$cat]->tvCount = 0;
                        }

                        /* add to tv/category map */
                        $tvMap[$tv->get('id')] = $tv->category;

                        /* add TV to category array */
                        $categories[$cat]->tvs[] = $tv;
                        if ($tv->get('type') != 'hidden') {
                            $categories[$cat]->tvCount++;
                        }
                    } else {
                        $hidden[] = $tv;
                    }
                }
            }
        }

        $finalCategories = array();
        /** @var modCategory $category */
        foreach ($categories as $n => $category) {
            if (is_object($category) && $category instanceof modCategory) {
                $category->hidden = empty($category->tvCount) ? true : false;
                $ct = isset($category->tvs) ? count($category->tvs) : 0;
                if ($ct > 0) {
                    $finalCategories[$category->get('id')] = $category;
                    $this->tvCounts[$n] = $ct;
                }
            }
        }
        
        $onResourceTVFormRender = $this->modx->invokeEvent('OnResourceTVFormRender',array(
            'categories' => &$finalCategories,
            'template' => $templateId,
            'resource' => $this->resource->get('id'),
            'tvCounts' => &$this->tvCounts,
            'hidden' => &$hidden,
        ));
        if (is_array($onResourceTVFormRender)) {
            $onResourceTVFormRender = implode('',$onResourceTVFormRender);
        }
        $this->setPlaceholder('OnResourceTVFormRender',$onResourceTVFormRender);

        $this->setPlaceholder('categories',$finalCategories);
        $this->setPlaceholder('tvCounts',$this->modx->toJSON($this->tvCounts));
        $this->setPlaceholder('tvMap',$this->modx->toJSON($tvMap));
        $this->setPlaceholder('hidden',$hidden);

        if (!empty($this->scriptProperties['showCheckbox'])) {
            $this->setPlaceholder('showCheckbox',1);
        }
		
		$reorg = array();
		foreach($finalCategories as $category){
			$current = $category->category;
			foreach($category->tvs as $key){
				$tv = $key->toArray();
				$tv = $this->prepareTvOutput($tv);				
				unset($tv['formElement']);
				$reorg[$current][] = $tv;
			}
		}
		// echo '<pre>'.print_r($reorg, true).'</pre>';
        return $reorg;
    }
	
	public function prepareTvOutput($tv){
		switch($tv['type']){
			case 'email':
				$tv['vtype'] = 'email';
				break;
			case 'textarea':
				$tv['height'] = 200;
				break;
			case 'richtext':
				$tv['cls'] = 'richtext';
				$tv['height'] = 200;
				break;
			case 'checkbox':
				$display = $tv['display'];
				if($display == 'delim'){
					$delimiter = $tv['output_properties']['delimiter'];
					$choices = explode($delimiter, $tv['elements']);
					foreach($choices as $choice){
						$a = explode('==', $choice);
						$tv['items'][$a[0]] = $a[1];
					}
					$tv['value'] = explode($delimiter, $tv['value']);
				}				
				break;
			case 'option':
				$display = $tv['display'];
				if($display == 'delim'){
					$delimiter = $tv['output_properties']['delimiter'];
					$choices = explode($delimiter, $tv['elements']);
					foreach($choices as $choice){
						$a = explode('==', $choice);
						$tv['items'][$a[0]] = $a[1];
					}
				}				
				break;
			default:
				break;
		}
		return $tv;
	}
}