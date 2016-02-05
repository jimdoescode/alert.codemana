<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <title>CodeMana Alerts</title>

        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div id="mount-point"></div>
        <script src="js/script.js"></script>
        <script>
            ReactDOM.render(
                React.createElement(App, {
                    'isLoggedIn': <?=$loggedIn ? 'true' : 'false';?>,
                    'githubClientId': '<?=$githubClientId;?>'
                }),
                document.getElementById('mount-point')
            );
        </script>

    </body>
</html>