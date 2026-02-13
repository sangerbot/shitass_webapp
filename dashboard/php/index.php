<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "notes_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? "";
    $username = $_POST["username"] ?? "";
    $newpassword = $_POST["password"] ?? ""; // current best Argon2 

    if ($newpassword !== "") {
    $hashpassword = password_hash($newpassword, PASSWORD_DEFAULT);
    }

    // Validate inputs
    if (empty($email) || empty($username) || empty($hashpassword)) {
        echo "All fields are required.";
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)"); // ?,?,? are undefined variables
    $stmt->bind_param("sss", $email, $username, $hashpassword); // defines the variables as strings

    // Execute
    if ($stmt->execute()) {
        echo "User registered successfully.";
        header("Location: ../login.html");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>