<?php
session_start();
require 'auth.php';

$conn = new mysqli("localhost", "root", "", "notes_app");

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO notes (user_id, folder_id, title, content) VALUES (?, NULL, ?, ?)");
$stmt->bind_param("iss", $user_id, $title, $content);
$stmt->execute();

header("Location: ../notes.php");
exit;
?>