<!DOCTYPE html>
<html>
<head>
    <title>Wow Duel - Who will win?</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
    <h1>Wow Duel - Who will win?</h1>
    <form action="fight.php">
        <fieldset>
            <legend>Pick 2 characters to fight</legend>
            <table>
            <tr><td>
                Name
            </td><td>
                <input name="left_name" value="Vosk" />
            </td><td>
                <input name="right_name" value="Ariesz" />
            </td></tr>
            <tr><td>
                Server
            </td><td>
                <input name="left_server" value="Mal'Ganis" />
            </td><td>
                <input name="right_server" value="Mal'Ganis" />
            </td></tr>
            <tr><td></td><td>
                <input type="submit" value="Fight!" />
            </td></tr>
            </table>
        </fieldset>
    </form>
</body>
</html>
