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
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<?php

function cache($url,$ttl,$prefix='', &$headers) {
    $tmpdir = '/var/tmp/wowduel/';
    if (!is_dir($tmpdir)) mkdir($tmpdir);
    $headers = array();
    $tmp = $tmpdir.$prefix.md5($url);
    if(file_exists($tmp)) $st = stat($tmp);
    else $st = false;
    if(!$st || $st && ($st['mtime']<($_SERVER['REQUEST_TIME']-$ttl))) {
        if($st) touch($tmp);

        $opts = array(
          'http'=>array(
            'timeout' => 10,
            'max_redirects' => 10,
            'user_agent' => "wowduel/1.0 (http://paulisageek.com/wowduel/) Firefox/3.0",
          )
        );

        $context = stream_context_create($opts);

        $stream = fopen($url,'r', false, $context);
        if(!$stream) {
          if($st) return $tmp;
          return false;
        }
        $tmpf = tempnam($tmpdir,'tmp');
        $tmpstream = fopen($tmpf, 'w');
        $bytes = stream_copy_to_stream($stream, $tmpstream, 1000001);
        if ($bytes == 1000001)
            throw new Exception("Documents larger than 1000000 bytes are unsupported");

        $meta = (stream_get_meta_data($stream));
        $headers = $meta['wrapper_data'];
        fclose($stream);

        fclose($tmpstream);
        rename($tmpf, $tmp);
    } else $bytes = $st['size'];
    return file_get_contents($tmp);
}

function fetch($url) {
    return cache($url, 60 * 5, '', $headers);
}

function get_char_url($n, $r) {
    return "http://www.wowarmory.com/character-sheet.xml?" .
        http_build_query(array("n" => $n, "r" => $r));
}

function get_char_info($n, $r) {
    $url = get_char_url($n, $r);
    $data = fetch($url);

    $xml = simplexml_load_string($data);
    $ilevel = 0;
    $items = $xml->characterInfo->characterTab->items;
    if (! $items) return false;

    foreach ($items->children() as $item) {
        $id = (string) $item['id'];
        $url = "http://www.wowarmory.com/item-info.xml?" .
            http_build_query(array("i" => $id));
        $data = fetch($url);

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
if (!$left) {
    die("<h1>Can't find {$safe_names[0]}</h1>");
}
$right = get_char_info($chars[1][0], $chars[1][1]);
if (!$right) {
    die("<h1>Can't find {$safe_names[1]}</h1>");
}

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
<div id="left" class="character">
    <h1><a href="<?php print get_char_url($chars[0][0], $chars[0][1]) ?>"><?php print "{$safe_names[0]}" ?></a></h1>
    <p>Level: <?php print $left[1] ?></p>
    <ul>
<?php 
foreach ($left[0]->characterInfo->characterTab->items->children() as $item) {
?>
    <li><a href="http://www.wowhead.com/?item=<?php print $item['id'] ?>"><img src="http://www.wowarmory.com/wow-icons/_images/51x51/<?php print htmlspecialchars($item['icon']) ?>.jpg" /></a></li>
<?php
}
?>
    </ul>
</div>

<div id="right" class="character">
    <h1><a href="<?php print get_char_url($chars[1][0], $chars[1][1]) ?>"><?php print "{$safe_names[1]}" ?></a></h1>
    <p>Level: <?php print $right[1] ?></p>
    <ul>
<?php 
foreach ($right[0]->characterInfo->characterTab->items->children() as $item) {
?>
    <li><a href="http://www.wowhead.com/?item=<?php print $item['id'] ?>"><img src="http://www.wowarmory.com/wow-icons/_images/51x51/<?php print htmlspecialchars($item['icon']) ?>.jpg" /></a></li>
<?php
}
?>
    </ul>
</div>
<script src="http://static.wowhead.com/widgets/power.js"></script>
</body>
<html>
