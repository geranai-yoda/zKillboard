<?php

require_once '../init.php';

if ($redis->get("tq:itemsPopulated") != true)
{
        Util::out("Waiting for items to be populated...");
        exit();
}

$key = date('YmdH');
if ($redis->get($key) == true) exit();

$json = CrestTools::getJSON("$crestServer/inventory/categories/7/");

foreach ($json['groups'] as $group) {
    $href = $group['href'];
    $types = CrestTools::getJSON($href);
    if ($types != null && $types['types'] != null) {
        foreach ($types['types'] as $type) {
            $typeID = getTypeID($type['href']);
            $mdb->set('information', ['type' => 'typeID', 'id' => $typeID], ['fittable' => true]);
        }
    }
    sleep(1);
}

$redis->setex($key, 86400, true); 

function getTypeID($href)
{
    $ex = explode('/', $href);

    return (int) $ex[4];
}
