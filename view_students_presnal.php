<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>
    <link rel="stylesheet" href="styles_view_pre_data.css">
</head>

<body>
    <header>
        <h1>Student Records</h1>
    </header>
    <section>
        <?php
        // Database connection details
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

        // Retrieve data from the database
        $sql = "SELECT roll_no, name, branch, dob, phone, gender, sem, email, address FROM students";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Roll Number</th><th>Name</th><th>Branch</th><th>Date of Birth</th><th>Phone</th><th>Gender</th><th>Semester</th><th>Email</th><th>Address</th></tr>";
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["roll_no"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["branch"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["dob"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["gender"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["sem"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "0 results";
        }

        // Close connection
        $conn->close();
        ?>
    </section>
    <footer>
        <div id="footer">
            <h3>Developed by Mahak Saxena</h3>
        </div>
    </footer>
</body>

</html>
