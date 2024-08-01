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

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $roll_numbers = $_POST['roll_no'];
    
    if (!empty($roll_numbers)) {
        $placeholders = implode(',', array_fill(0, count($roll_numbers), '?'));
        $stmt = $conn->prepare("DELETE FROM students WHERE roll_no IN ($placeholders)");
        $stmt->bind_param(str_repeat('s', count($roll_numbers)), ...$roll_numbers);

        if ($stmt->execute()) {
            echo "<div class='alert success'>Record(s) deleted successfully</div>";
        } else {
            echo "<div class='alert error'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert warning'>No records selected for deletion.</div>";
    }
}

// Fetch all student records
$sql = "SELECT name, roll_no, branch, dob, phone, gender, sem, email, address FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student Records</title>
    <link rel="stylesheet" href="update.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url("./background/images.jpeg") no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            color: white;
        }

        header {
            background: rgba(0, 0, 0, 0.1);
            color: white;
            padding: 1em 0;
            text-align: center;
            margin-bottom: 20px;
        }

        section {
            margin: 20px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        input[type="submit"] {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        input[type="submit"]:hover {
            background: #45a049;
        }

        footer {
            text-align: center;
            padding: 10px;
            background: black;
            margin-top: 20px;
            border-top: 1px solid #ddd;
        }

        .alert {
            margin: 20px auto;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            max-width: 1200px;
        }

        .alert.success {
            background: #d4edda;
            color: white;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
        }

        .alert.warning {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>

<body>
    <header>
        <h1>Delete Student Records</h1>
    </header>
    <section>
        <form id="delete-form" action="delete_student.php" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Name</th>
                        <th>Roll Number</th>
                        <th>Branch</th>
                        <th>DOB</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Semester</th>
                        <th>Email</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><input type='checkbox' name='roll_no[]' value='" . htmlspecialchars($row['roll_no']) . "'></td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['roll_no']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['branch']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sem']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <input type="submit" name="delete" value="Delete Selected">
        </form>
    </section>
    <footer>
        <div id="footer">
            <h5>Developed by Mahak Saxena</h5>
        </div>
    </footer>
</body>

</html>

<?php
$conn->close();
?>
