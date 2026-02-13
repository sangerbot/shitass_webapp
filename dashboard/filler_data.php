
<?php

// seed.php

$conn = new mysqli("localhost", "root", "", "notes_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -----------------------------
// 1. Insert users (with hashed passwords)
// -----------------------------
$users = [
    [1, 'alice', 'alice@example.com', 'hash1', 1],
    [2, 'bob', 'bob@example.com', 'hash2', 0],
    [3, 'carol', 'carol@example.com', 'hash3', 0],
    [4, 'dave', 'dave@example.com', 'hash4', 0],
    [5, 'eve', 'eve@example.com', 'hash5', 0],
    [6, 'frank', 'frank@example.com', 'hash6', 0],
    [7, 'grace', 'grace@example.com', 'hash7', 0],
    [8, 'heidi', 'heidi@example.com', 'hash8', 0],
    [9, 'ivan', 'ivan@example.com', 'hash9', 1],
    [10, 'judy', 'judy@example.com', 'hash10', 1]
];

$userStmt = $conn->prepare("INSERT INTO users (id, username, email, password, admin) VALUES (?, ?, ?, ?, ?)");

foreach ($users as $u) {
    $hashedPassword = password_hash($u[2], PASSWORD_DEFAULT);
    $userStmt->bind_param("isssi", $u[0], $u[1], $u[2], $hashedPassword, $u[4]);
    $userStmt->execute();
}

$userStmt->close();

// -----------------------------
// 2. Insert folders
// -----------------------------
$folders = [
    [1, 'Personal'],
    [2, 'Work'],
    [3, 'Projects'],
    [4, 'Notes'],
    [5, 'Travel'],
    [6, 'Health'],
    [7, 'Finance'],
    [8, 'School'],
    [9, 'Ideas'],
    [10, 'Misc']
];

$folderStmt = $conn->prepare("INSERT INTO folders (user_id, name) VALUES (?, ?)");

foreach ($folders as $f) {
    $folderStmt->bind_param("is", $f[0], $f[1]);
    $folderStmt->execute();
}

$folderStmt->close();

// -----------------------------
// 3. Insert notes
// -----------------------------
$notes = [
    [1, NULL, 'Grocery List', 'Buy milk, eggs, bread'],
    [2, NULL, 'Workout Plan', 'Monday: Chest, Tuesday: Back'],
    [3, NULL, 'Project Ideas', 'Build a chatbot for school'],
    [4, NULL, 'Meeting Notes', 'Discuss quarterly results'],
    [5, NULL, 'Travel Plans', 'Visit Japan in spring'],
    [6, NULL, 'Book List', 'Read 10 books this year'],
    [7, NULL, 'Recipe', 'Pasta with homemade sauce'],
    [8, NULL, 'Daily Journal', 'Today I learned SQL'],
    [9, NULL, 'Budget', 'Track monthly expenses'],
    [10, NULL, 'Learning Goals', 'Practice coding 2 hours/day']
];

$noteStmt = $conn->prepare("INSERT INTO notes (user_id, folder_id, title, content) VALUES (?, ?, ?, ?)");

foreach ($notes as $n) {
    $noteStmt->bind_param("iiss", $n[0], $n[1], $n[2], $n[3]);
    $noteStmt->execute();
}

$noteStmt->close();

// -----------------------------
// 4. Insert kanban rows
// -----------------------------
$kanban = [
    [1, 1, 0, 0, 0],
    [2, 0, 1, 0, 0],
    [3, 1, 0, 0, 0],
    [4, 0, 0, 1, 0],
    [5, 0, 0, 0, 1],
    [6, 1, 0, 0, 0],
    [7, 0, 0, 1, 0],
    [8, 0, 0, 1, 0],
    [9, 1, 0, 0, 0],
    [10, 0, 0, 1, 0]
];


$kanbanStmt = $conn->prepare("INSERT INTO kanban (note_id, uncategorized, todo, doing, done) VALUES (?, ?, ?, ?, ?)");

foreach ($kanban as $k) {
    $kanbanStmt->bind_param("iiiii", $k[0], $k[1], $k[2], $k[3], $k[4]);
    $kanbanStmt->execute();
}

$kanbanStmt->close();

// -----------------------------
// Insert subscriptions
// -----------------------------
$subscriptions = [
    [1, 1, 1, '2024-01-01', '2025-01-01'],
    [2, 2, 0, NULL, NULL],
    [3, 3, 1, '2024-02-15', '2025-02-15'],
    [4, 4, 0, NULL, NULL],
    [5, 5, 1, '2024-03-01', '2024-12-31'],
    [6, 6, 1, '2024-01-15', '2025-01-15'],
    [7, 7, 0, NULL, NULL],
    [8, 8, 1, '2024-04-01', '2025-04-01'],
    [9, 9, 1, '2023-12-01', '2024-12-01'],
    [10, 10, 1, '2024-01-10', '2025-01-10']
];

$subStmt = $conn->prepare("INSERT INTO subscriptions (user_id, is_subscribed, started_at, expires_at) VALUES (?, ?, ?, ?)");

foreach ($subscriptions as $s) {
    $subStmt->bind_param("iiss", $s[1], $s[2], $s[3], $s[4]);
    $subStmt->execute();
}

$subStmt->close();

echo "Data seeded successfully!";
$conn->close();