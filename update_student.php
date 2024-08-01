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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['roll_no'])) {
    $roll_no = $_GET['roll_no'];

    // Fetch existing details
    $sql = "SELECT name, roll_no, branch, dob, phone, gender, sem, email, address FROM students WHERE roll_no=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $roll_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        echo "No student found with the provided roll number.";
        exit;
    }

    // Close the statement
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['roll_no'])) {
    // Update details
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
    $stmt = $conn->prepare("UPDATE students SET name=?, branch=?, dob=?, phone=?, gender=?, sem=?, email=?, address=? WHERE roll_no=?");
    $stmt->bind_param("sssssssss", $name, $branch, $dob, $phone, $gender, $sem, $email, $address, $roll_no);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
    exit;
} else {
    echo "Invalid request.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student Details</title>
    <link rel="stylesheet" href="add_student.css">
</head>

<body>
    <header>
        <h1>Update Student Details</h1>
    </header>
    <section>
        <form id="update-form" action="update_student.php" method="post">
            <input type="hidden" name="roll_no" value="<?php echo htmlspecialchars($student['roll_no']); ?>">

            <label for="name" class="required">Name:</label>
            <input type="text" placeholder="e.g., Ram" name="name" id="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>

            <label for="branch" class="required">Branch:</label>
            <select name="branch" id="branch" required>
                <option value="CSE" <?php if ($student['branch'] == 'CSE') echo 'selected'; ?>>Computer Science and Engineering</option>
                <option value="FT" <?php if ($student['branch'] == 'FT') echo 'selected'; ?>>Fashion Technology</option>
                <option value="AID" <?php if ($student['branch'] == 'AID') echo 'selected'; ?>>Architecture and Interior Department</option>
                <option value="HMCT" <?php if ($student['branch'] == 'HMCT') echo 'selected'; ?>>Hotel Management and Catering Technology Department</option>
            </select>

            <label for="dob" class="required">Date of Birth:</label>
            <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($student['dob']); ?>" required>

            <label for="phone" class="required">Mobile Number:</label>
            <input type="tel" id="phone" name="phone" placeholder="123-45-678" value="<?php echo htmlspecialchars($student['phone']); ?>" required>

            <label class="required">Gender:</label>
            <input type="radio" name="gender" value="male" <?php if ($student['gender'] == 'male') echo 'checked'; ?>> Male
            <input type="radio" name="gender" value="female" <?php if ($student['gender'] == 'female') echo 'checked'; ?>> Female

            <label for="sem" class="required">Semester:</label>
            <select name="sem" id="sem" required>
                <option value="1st" <?php if ($student['sem'] == '1st') echo 'selected'; ?>>1st Semester</option>
                <option value="2nd" <?php if ($student['sem'] == '2nd') echo 'selected'; ?>>2nd Semester</option>
                <option value="3rd" <?php if ($student['sem'] == '3rd') echo 'selected'; ?>>3rd Semester</option>
                <option value="4th" <?php if ($student['sem'] == '4th') echo 'selected'; ?>>4th Semester</option>
                <option value="5th" <?php if ($student['sem'] == '5th') echo 'selected'; ?>>5th Semester</option>
                <option value="6th" <?php if ($student['sem'] == '6th') echo 'selected'; ?>>6th Semester</option>
            </select>

            <label for="email" class="required">Email id:</label>
            <input type="email" placeholder="Enter Email" name="email" id="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

            <label for="address" class="required"> Address:</label>
            <textarea cols="60" rows="5" placeholder="Enter your address" name="address" id="address" required><?php echo htmlspecialchars($student['address']); ?></textarea>

            <input type="submit" name="submit" value="Update">
        </form>
    </section>
    <footer>
        <div id="footer">
            <h5>Developed by Mahak Saxena</h5>
        </div>
    </footer>
</body>

</html>
