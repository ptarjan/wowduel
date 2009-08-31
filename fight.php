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
$safe_names = array(
    "{$safe_chars[0][0]}, {$safe_chars[0][1]}",
    "{$safe_chars[1][0]}, {$safe_chars[1][1]}",
);
?>
<html>
<head>
    <title>Duel - <?php print "{$safe_names[0]} vs {$safe_names[1]}" ?></title>
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

function compare($left, $right) {
    if ($left[1] < $right[1])
        return -1;
    if ($left[1] > $right[1])
        return 1;
    return 0;
}

$left = get_char_info($chars[0][0], $chars[0][1]);
$right = get_char_info($chars[1][0], $chars[1][1]);

switch (compare($left, $right)) {
    case -1 : $status = "{$safe_names[1]} Wins!"; break;
    case 1 : $status = "{$safe_names[0]} Wins!"; break;
    case 0 : 
    default : $status = "Tie!";
}

?>
<div id="status">
    <?php print $status ?>
</div>
<div id="left">
    <h1><a href="<?php print get_char_url($chars[0][0], $chars[0][1]) ?>"><?php print "{$safe_names[0]}" ?></a></h1>
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
    <h1><a href="<?php print get_char_url($chars[1][0], $chars[1][1]) ?>"><?php print "{$safe_names[1]}" ?></a></h1>
    <p>Level: <?php print $right[1] ?></p>
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