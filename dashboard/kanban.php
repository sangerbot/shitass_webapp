<?php
session_start();
require 'php/auth.php';

$conn = new mysqli("localhost", "root", "", "notes_app");
$userId = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ===============================
   HANDLE DRAG & DROP UPDATES
================================= */
if (isset($_POST['action']) && $_POST['action'] === 'move') {

    $noteId = intval($_POST['note_id']);
    $column = $_POST['folder_id'];

    // Only allow valid columns
    $allowed = ['uncategorized','todo','doing','done'];
    if (!in_array($column, $allowed)) exit;

    // Make sure a kanban row exists for this note
    $check = $conn->prepare("SELECT note_id FROM kanban WHERE note_id = ?");
    $check->bind_param("i", $noteId);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO kanban (note_id, uncategorized) VALUES (?, TRUE)");
        $insert->bind_param("i", $noteId);
        $insert->execute();
    }

    // Update status: only the moved column = TRUE
    $stmt = $conn->prepare("
        UPDATE kanban SET
            uncategorized = (? = 'uncategorized'),
            todo = (? = 'todo'),
            doing = (? = 'doing'),
            done = (? = 'done')
        WHERE note_id = ?
    ");
    $stmt->bind_param("ssssi", $column,$column,$column,$column,$noteId);
    $stmt->execute();

    echo "success";
    exit;
}

/* ===============================
   FETCH NOTES WITH KANBAN STATUS
================================= */

$sql = "
SELECT n.id, n.title,
       k.uncategorized, k.todo, k.doing, k.done
FROM notes n
LEFT JOIN kanban k ON n.id = k.note_id
WHERE n.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$columns = [
    'uncategorized' => [],
    'todo' => [],
    'doing' => [],
    'done' => []
];

while ($row = $result->fetch_assoc()) {
    if (!isset($row['uncategorized'])) $row['uncategorized'] = true; // default uncategorized
    if ($row['todo']) $columns['todo'][] = $row;
    elseif ($row['doing']) $columns['doing'][] = $row;
    elseif ($row['done']) $columns['done'][] = $row;
    else $columns['uncategorized'][] = $row;
}

function renderColumn($title, $id, $notes) {
        echo "<div class='column' ondrop='drop(event)' ondragover='allowDrop(event)' data-folder='$id'>";
        echo "<h2>$title</h2>";
        echo "<div class='notes-container' >";
        foreach ($notes as $note) {
            echo "<div class='notessss' draggable='true' ondragstart='drag(event)' id='note{$note['id']}'>";
            echo htmlspecialchars($note['title']);
            echo "</div>";
        }
        echo "</div></div>";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css files/window.css">
    <link rel="stylesheet" href="css files/notes.css">
    <link rel="stylesheet" href="css files/index.css">
    <meta charset="UTF-8">
    <title>Kanban Board</title>

    <style>
        @font-face {
            font-family: 'MSSansSerif';
            src: url('../fonts/MS_Sans_Serif_8pt.ttf') format('truetype');
        }

        body {
            font-family: 'MSSansSerif';
            color: white;
        }

        .board {
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .column {
            background-color: #c0c0c0;
            border: 2px solid;
            border-color: #dfdfdf #000000 #000000 #dfdfdf;
            box-shadow: 1px 1px 0 #ffffff inset, -1px -1px 0 #808080 inset;
            padding: 4px;
            min-width: 23%;
            min-height: 400px;
        }

        .column h2 {
            margin: 0 0 10px 0;
            font-size: 14px;
            background: #808080;
            color: #ffffff;
            padding: 2px 4px;
        }

        .notessss {
            background: #555;
            padding: 4px;
            margin: 2px;
            cursor: grab;
        }
        .notes-container {
            background-color: #ffffff;
            border: 2px solid;
            border-color:  #000000 #dfdfdf #dfdfdf #000000;
            box-shadow: 1px 1px 0 #ffffff inset, -1px -1px 0 #808080 inset;
            padding: 2px;
            min-width: 23%;
            min-height: 400px;
        }
    </style>
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
<h1 style="padding-left:20px;">Kanban Board</h1>

<div class="board">
    <?php
        renderColumn("Uncategorized", "uncategorized", $columns['uncategorized']);
        renderColumn("To Do", "todo", $columns['todo']);
        renderColumn("Doing", "doing", $columns['doing']);
        renderColumn("Done", "done", $columns['done']);
    ?>
</div>

</div>

<script>
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();

    const noteId = ev.dataTransfer.getData("text");
    const noteElement = document.getElementById(noteId);

    const column = ev.target.closest(".column");
    column.appendChild(noteElement);
    const contentArea = column.querySelector(".notes-container");
    contentArea.appendChild(noteElement);

    const folderId = column.getAttribute("data-folder");
    const cleanId = noteId.replace("note", "");

    fetch("/kanban.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=move&note_id=" + cleanId + "&folder_id=" + folderId
    });
    console.log("Moving note", cleanId, "to column", folderId);
}
</script>

</body>
</html>
