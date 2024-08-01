<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Roll Number</title>
    <link rel="stylesheet" href="update.css">
</head>

<body>
    <header>
        <h1>Enter Roll Number to Update Details</h1>
    </header>
    <section>
        <form id="get-roll-no-form" action="update_student.php" method="get">
            <label for="roll_no" class="required">Roll Number:</label>
            <input type="text" placeholder="e.g., 22049c04032" name="roll_no" id="roll_no" required>
            <input type="submit" name="submit" value="Fetch Details">
        </form>
    </section>
    <footer>
        <div id="footer">
            <h5>Developed by Mahak Saxena</h5>
        </div>
    </footer>
</body>

</html>
