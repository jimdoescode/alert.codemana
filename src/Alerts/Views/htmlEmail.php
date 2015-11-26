<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
    <head>
        <title>CodeMana Alert</title>
        <style type="text/css">
            p, ul {margin-bottom: 15px;}
            a {color: #778abc;}
        </style>
    </head>
    <body>
<?php foreach ($patchFiles as $file): ?>
        <table border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td colspan="3">
                        <?=$file->name;?>
                    </td>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($file->lines as $line): ?>
                <tr style="background-color: <?=$line->isRemoved ? '#f66' : ($line->isAdded ? '#6f6' : '#fff');?>;">
                    <td><?=$line->isAdded ? '' : $line->number;?></td>
                    <td><?=$line->isAdded ? $line->number : '';?></td>
                    <td><pre><?=$line->parsed;?></pre></td>
                </tr>
    <?php endforeach; ?>
            </tbody>
        </table>
<?php endforeach; ?>
    </body>
</html>
