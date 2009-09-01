YUI().use("node", "lang", "yql", function(Y) {

function getQuerystring(key, default_) {
    key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
    var qs = regex.exec(window.location.href);
    if(qs == null)
        return default_;
    else
        return decodeURIComponent(qs[1]);
}
function htmlspecialchars(string) {
    return string.replace("<", "&lt;").replace(">", "&gt;");
}

var chars = [
    [getQuerystring('left_name'), getQuerystring('left_server')],
    [getQuerystring('right_name'), getQuerystring('right_server')],
];
var safe_names = [
    htmlspecialchars(char[0][0] + ", " + chars[0][1]),
    htmlspecialchars(char[1][0] + ", " + chars[1][1]),
];

function get_char_url(n, r) {
    return "http://www.wowarmory.com/character-sheet.xml?" +
        "n=" + encodeURIComponent(n) + "&r=" + encodeURIComponent(r);
}

function get_char_info(n, r) {
    url = get_char_url(n, r);

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
 
};
