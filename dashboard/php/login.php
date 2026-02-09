<?php
//login
$servername = "localhost";
$db_username = "your_db_user"; // replace with real thing
$db_password = "your_db_pass"; // replace with real thing
$database = "notes_app";

$conn = new mysqli($servername, $db_username, $db_password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
