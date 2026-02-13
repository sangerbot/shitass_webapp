<?php
session_start();
require 'php/auth.php';

$conn = new mysqli("localhost", "root", "", "notes_app");

$userId = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CREATE
if (isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO notes (user_id, folder_id, title, content) VALUES (?, NULL, ?, ?)");
    $stmt->bind_param("iss", $userId, $title, $content);
    $stmt->execute();
    header("Location: notes.php");
    exit;
}

// UPDATE
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $noteId = $_POST['note_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $content, $noteId, $userId);
    $stmt->execute();
    header("Location: notes.php");
    exit;
}

// DELETE
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $noteId = $_POST['note_id'];

    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $noteId, $userId);
    $stmt->execute();
    header("Location: notes.php");
    exit;
}

// READ
$stmt = $conn->prepare("SELECT id, title, content FROM notes WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css files/window.css">
    <link rel="stylesheet" href="css files/notes.css">
    <link rel="stylesheet" href="css files/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
</head>
    <body onload="startTime()">
        <div id="menu" style="padding: 20px;"></div>
        <script>
            fetch("taskbar.php")
            .then(response => response.text())
            .then(data => {
                document.getElementById("menu").innerHTML = data;
            });
        </script>
        <div class="login-box notesbox" style="margin-bottom: 20px;">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div>
                    <h2>New Note - <input name="title" type="text" placeholder="Enter note title..." style=" font-family: 'MSSansSerif'; margin: 0; color: white; width: 300px; border: none; background: transparent; font-size: inherit;"></h2>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 320px;">
                        <div>
                            <div class="inputs">
                                <textarea name="content" type="text" placeholder="Enter note content..." style="width: 100%; min-height: 57px; max-height: 400px; resize: vertical; font-family: inherit;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="button-group">
                        <button class="button-group" type="submit" >OK</button>
                        <button class="button-group" type="button" onclick="window.location.reload();">Cancel</button>
                    </div>
                </div>
            </form>
        </div>

        <?php while ($note = $result->fetch_assoc()): ?>
            <div class="login-box notesbox">
                <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="note_id" value="<?= $note['id'] ?>">
                    <div>
                        <h2>
                            <input name="title" type="text" value="<?= htmlspecialchars($note['title']) ?>" style=" font-family: 'MSSansSerif'; margin: 0; color: white; width: 300px; border: none; background: transparent; font-size: inherit;">
                        </h2>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 320px;">
                            <div>
                                <div class="inputs">
                                    <textarea name="content" style="width: 100%; min-height: 57px; max-height: 400px; resize: vertical; font-family: inherit;"><?= htmlspecialchars($note['content']) ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="button-group">
                            <button type="submit">OK</button>
                </form>
                            <form method="POST">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="note_id" value="<?= $note['id'] ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
            </div>
        <?php endwhile; ?>
    </body>
</html>