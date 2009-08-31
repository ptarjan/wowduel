<!DOCTYPE html>
<?php
$chars = array(
    array($_REQUEST['left_name'], $_REQUEST['left_server']),
    array($_REQUEST['right_name'], $_REQUEST['right_server']),
);
$safe_chars = array(
    array(htmlspecialchars($chars[0][0]), htmlspecialchars($chars[0][1])),
    array(htmlspecialchars($chars[1][0]), htmlspecialchars($chars[1][1])),
); 
?>
<html>
<head>
    <title>Duel - <?php print "{$safe_chars[0][0]}, {$safe_chars[0][1]} vs {$safe_chars[1][0]}, {$safe_chars[1][1]}" ?></title>
</head>
<body>
<?php

function get_char_url($n, $r) {
    return "http://www.wowarmory.com/character-sheet.xml?" .
        http_build_query(array("n" => $n, "r" => $r));
}

function get_char_info($n, $r) {
    $url = get_char_url($n, $r);

    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => True,
        CURLOPT_USERAGENT => "WowDuel ( http://paulisageek.com/wowduel/ ) Firefox/3.0",
    ));
    $data = curl_exec($ch);

    $xml = simplexml_load_string($data);
    $ilevel = 0;
    foreach ($xml->characterInfo->characterTab->items->children() as $item) {
        $id = (string) $item['id'];
        $url = "http://www.wowarmory.com/item-info.xml?" .
            http_build_query(array("i" => $id));
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => True,
            CURLOPT_USERAGENT => "WowDuel ( http://paulisageek.com/wowduel/ ) Firefox/3.0",
        ));
        $data = curl_exec($ch);

        $item_info = simplexml_load_string($data);
        $ilevel += $item_info->itemInfo->item["level"];
    }
    return array($xml, $ilevel);
}

$left = get_char_info($chars[0][0], $chars[0][1]);
$right = get_char_info($chars[1][0], $chars[1][1]);

?>
<div id="left">
    <h1><a href="<?php print get_char_url($chars[0][0], $chars[0][1]) ?>"><?php print "{$safe_chars[0][0]}, {$safe_chars[0][1]}" ?></a></h1>
    <p>Level: <?php print $left[1] ?></p>
    <ul>
<?php 
foreach ($left[0]->characterInfo->characterTab->items->children() as $item) {
?>
    <li><img src="http://www.wowarmory.com/wow-icons/_images/51x51/<?php print htmlspecialchars($item['icon']) ?>.jpg" /></li>
<?php
}
?>
    </ul>
</div>

<div id="right">
    <h1><a href="<?php print get_char_url($chars[1][0], $chars[1][1]) ?>"><?php print "{$safe_chars[1][0]}, {$safe_chars[1][1]}" ?></a></h1>
    <p>Level: <?php print $left[1] ?></p>
    <ul>
<?php 
foreach ($right[0]->characterInfo->characterTab->items->children() as $item) {
?>
    <li><img src="http://www.wowarmory.com/wow-icons/_images/51x51/<?php print htmlspecialchars($item['icon']) ?>.jpg" /></li>
<?php
}
?>
    </ul>
</div>
</body>
<html>
