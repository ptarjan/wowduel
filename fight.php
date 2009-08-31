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
$url = "http://www.wowarmory.com/character-sheet.xml?" .
    http_build_query(array("n" => $chars[0][0], "r" => $chars[0][1]));

$ch = curl_init($url);
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => True,
    CURLOPT_USERAGENT => "WowDuel ( http://paulisageek.com/wowduel/ ) Firefox/3.0",
));
$data = curl_exec($ch);

$xml = simplexml_load_string($data);
$level = $xml->characterInfo->character['level'];
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
print $ilevel;
?>
</body>
<html>
