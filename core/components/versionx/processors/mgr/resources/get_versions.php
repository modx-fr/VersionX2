<?php

$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$sort = $modx->getOption('sort',$scriptProperties,'version_id');
$dir = $modx->getOption('dir',$scriptProperties,'desc');

$search = $modx->getOption('query',$scriptProperties,'');
$resource = intval($modx->getOption('resource',$scriptProperties,0));
$current = intval($modx->getOption('current',$scriptProperties,0));

$c = $modx->newQuery('vxResource');
$c->select(array('version_id','saved','mode'));

if (strlen($search) > 1) {
    $c->where(array('id:LIKE' => "%$search%",));
}
if ($resource > 0)
    $c->where(array('content_id' => $resource));
if ($current > 0)
    $c->where(array('version_id:!=' => $current));

$total = $modx->getCount('vxResource',$c);

$c->sortby($sort,$dir);
$c->limit($limit,$start);

$results = array();
$query = $modx->getCollection('vxResource',$c);
/* @var vxResource $r */
foreach ($query as $r) {
    $ta = $r->toArray('',false,true);
    $results[] = array(
        'id' => $ta['version_id'],
        'display' => '#'.$ta['version_id'] . ': ' . $modx->lexicon('versionx.mode.'.$ta['mode']) . ' at ' . $ta['saved']
    );
}

if (count($results) == 0)
    return $modx->error->failure($modx->lexicon('versionx.error.noresults'));

$returnArray = array(
    'success' => true,
    'total' => $total,
    'results' => $results
);
return $modx->toJSON($returnArray);