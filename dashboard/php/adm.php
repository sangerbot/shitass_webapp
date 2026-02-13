<?php
session_start();

function requireRole($role) {
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
        header("Location: ../notes.php");
        exit;
    }
}

?>