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
?>
</body>
<html>
