<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "minor";
    $port = 3306;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $branch = $_POST['branch'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $sem = $_POST['sem'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO students (roll_no, name, branch, dob, phone, gender, sem, email, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $roll_no, $name, $branch, $dob, $phone, $gender, $sem, $email, $address);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
