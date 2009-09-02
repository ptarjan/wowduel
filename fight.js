YUI().use("node", "yql", function(Y) {

function htmlspecialchars(string) {
    return string.replace("<", "&lt;").replace(">", "&gt;");
}

Y.on("submit", fight, "form");

function fight(e) {
    // Don't do the form submit
    e.halt();

    var data = new Y.yql('use "http://paulisageek.com/wowduel/wowduel.xml" ; select * from wowduel where left_name="' + Y.get('[name="left_name"]').get("value") + '" AND left_server="' + Y.get('[name="left_server"]').get("value") + '" AND right_name="' + Y.get('[name="right_name"]').get("value") + '" AND right_server="' + Y.get('[name="right_server"]').get("value") + '"');
    data.on('query', function(r) {
        var result = r.results.result;
        console.log(result);
        var fight = result.left.ilevel - result.right.ilevel;
        if (fight < 0)
            Y.get("#status").setContent(result.left.name + " Wins!");
        else if (fight > 0)
            Y.get("#status").setContent(result.right.name + " Wins!");
        else
            Y.get("#status").setContent("Tie!");

        Y.get("#left h1 a").setAttribute("href", result.left.url);
        Y.get("#left h1 a").setContent(result.left.name);
        Y.get("#left p").append(result.left.ilevel);
        var ul = Y.get("#left ul");
        for each (var item in result.left.items) {
            console.log(item);
            ul.append(
                '<li><a href="http://www.wowhead.com/?item=' + item['id'] + '"><img src="http://www.wowarmory.com/wow-icons/_images/51x51/' + item['icon'] + '.jpg" /></a></li>'
            );
        }
        Y.get("#result").setStyle("display", "block");
    });
}
});
