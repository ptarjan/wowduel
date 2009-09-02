YUI().use("node", "yql", function(Y) {

function htmlspecialchars(string) {
    return string.replace("<", "&lt;").replace(">", "&gt;");
}

Y.on("submit", fight, "form");

function fight(e) {
    // Don't do the form submit
    e.halt();

    var data = new Y.yql('use "http://paulisageek.com/wowduel/wowduel.xml" ; select * from wowduel where left_name="Vosk" AND left_server="Mal\'Ganis" AND right_name="Ariesz" AND right_server="Mal\'Ganis"');
    data.on('query', function(r) {
        console.log(r.results);
    });
}
});
