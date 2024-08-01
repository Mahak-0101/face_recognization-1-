<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="styles_student_attendance.css">
</head>
<body>
    <div class="container">
        <h1>Attendance Records</h1>
        <?php
        // Database configuration
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "minor";
        $port = 3307;

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL query to retrieve data
        $sql = "SELECT id, name, roll_number, branch, time FROM attendance";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Roll Number</th><th>Branch</th><th>Time</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["roll_number"] . "</td>";
                echo "<td>" . $row["branch"] . "</td>";
                echo "<td>" . $row["time"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No results found</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
