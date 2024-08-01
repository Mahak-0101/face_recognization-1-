<?php
// mark_attendance.php

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "minor"; // Adjust the database name if necessary
$port = 3307; // Adjust the port if necessary

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Set the content type to application/json
header('Content-Type: application/json');

// Get the JSON data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

if (is_array($data)) {
    $stmt = $conn->prepare("INSERT INTO attendance (name, roll_number, branch, time) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error]);
        $conn->close();
        exit();
    }

    foreach ($data as $entry) {
        $name = $entry['name'];
        $roll_number = $entry['rollNumber'];
        $branch = $entry['branch'];
        $time = $entry['time'];

        $stmt->bind_param("ssss", $name, $roll_number, $branch, $time);
        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Execute statement failed: ' . $stmt->error]);
            $stmt->close();
            $conn->close();
            exit();
        }
    }

    $stmt->close();
    echo json_encode(['status' => 'success', 'message' => 'Attendance recorded successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data format']);
}

$conn->close();
?>
