<?php
session_start();
require 'php/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css files/window.css">
    </head>
    <body>
        <div class="taskbar">
              
            <div onclick="window.location.href='php/logout.php'" style="cursor: pointer;" class="start-btn btn">
                <img src="https://win98icons.alexmeub.com/icons/png/windows-4.png"> Logout   
            </div>
            <div class="btn" onclick="window.location.href='notes.php'">Notes</div>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                <div class="btn" onclick="window.location.href='kanban.php'">kanban</div>
            <?php endif; ?>
            <div class="btn right" onclick="window.location.href='sub.php'">Subscribe!</div>
            
            <div class="tray" id="txt">04:04 AM</div>
        </div>
     </body>   
</html>