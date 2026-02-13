<?php
session_start();
require 'php/auth.php';

$conn = new mysqli("localhost", "root", "", "notes_app");

$userId = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("UPDATE subscriptions SET is_subscribed = TRUE WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();

 $_SESSION["is_subscribed"] = true;
header("Location: ../notes.php");
exit;
?>