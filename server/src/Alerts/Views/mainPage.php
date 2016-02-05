<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <title>CodeMana Alerts</title>

        <link rel="stylesheet" href="//yui.yahooapis.com/pure/0.6.0/pure-min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div id="mount-point"></div>
        <script>
            GLOBALS = {
                'logged_in': <?=$loggedIn ? 'true' : 'false';?>,
                'githubClientId': '<?=$githubClientId;?>'
            }
        </script>
        <script src="js/script.js"></script>
    </body>
</html>