<?php
/**
* @package myjournal
* @subpackage build
*/
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'bodyclasses',
    'description' => 'Add some css classes to use in your template if needed',
    'snippet' => getSnippetContent($sources['snippets'], 'snippet.bodyclasses'),
),'',true,true);
// $properties = include $sources['build'].'properties/properties.cliche.php';
// $snippets[0]->setProperties($properties);
// unset($properties, $content);

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'content_autop',
    'description' => 'Formatted resource content - taken from wpautop function',
    'snippet' => getSnippetContent($sources['snippets'], 'snippet.content_autop'),
),'',true,true);
// $properties = include $sources['build'].'properties/properties.cliche.php';
// $snippets[0]->setProperties($properties);
// unset($properties, $content);

$snippets[2]= $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'name' => 'total_comments',
    'description' => 'An helper snippets to get total comments from Quip automatically',
    'snippet' => getSnippetContent($sources['snippets'], 'snippet.total_comments'),
),'',true,true);
// $properties = include $sources['build'].'properties/properties.cliche.php';
// $snippets[0]->setProperties($properties);
// unset($properties, $content);

$snippets[3]= $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => 3,
    'name' => 'get_tags',
    'description' => 'Get tags for an article',
    'snippet' => getSnippetContent($sources['snippets'], 'snippet.get_tags'),
),'',true,true);
// $properties = include $sources['build'].'properties/properties.cliche.php';
// $snippets[0]->setProperties($properties);
// unset($properties, $content);

return $snippets;