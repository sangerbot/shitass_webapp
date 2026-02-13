<?php
require 'adm.php';  // checks if user is logged in
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="vcss files/window.css">
        <link rel="stylesheet" href="css files/notes.css">
        <link rel="stylesheet" href="css files/index.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kanban Board</title>
    </head>
    <body>
        <div id="menu" style="padding: 20px;"></div>
        <script>
            fetch("taskbar.php")
            .then(response => response.text())
            .then(data => {
                document.getElementById("menu").innerHTML = data;
            });
        </script>
    </body>
</html>