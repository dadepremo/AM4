<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Check if plane_code is provided
if (!isset($_GET['management_id'])) {
    header("Location: manage_plane.php");
    exit;
}

$management_id = $_GET['management_id'];

// Fetch the record to edit
$query = "SELECT * FROM my_planes WHERE plane_code = :management_id AND user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->execute([
    ':management_id' => $management_id,
    ':user_id' => $_SESSION['user_id']
]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Record not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_of_flights = $_POST['num_of_flights'];
    $hours_per_flight = $_POST['hours_per_flight'];
    $hours_spent_on_ferry = $_POST['hours_spent_on_ferry'];
    $maint_check = $_POST['maint_check'];

    // Calculate total_hours and hours_to_check
    $total_hours = ($hours_per_flight * $num_of_flights) + $hours_spent_on_ferry;
    $hours_to_check = $maint_check - $total_hours;

    // Update the record in the database
    $queryUpdate = "
        UPDATE my_planes
        SET 
            num_of_flights = :num_of_flights,
            hours_per_flight = :hours_per_flight,
            total_hours = :total_hours,
            hours_spent_on_ferry = :hours_spent_on_ferry,
            maint_check = :maint_check,
            hours_to_check = :hours_to_check
        WHERE plane_code = :management_id AND user_id = :user_id
    ";
    $stmtUpdate = $conn->prepare($queryUpdate);
    $stmtUpdate->execute([
        ':num_of_flights' => $num_of_flights,
        ':hours_per_flight' => $hours_per_flight,
        ':total_hours' => $total_hours,
        ':hours_spent_on_ferry' => $hours_spent_on_ferry,
        ':maint_check' => $maint_check,
        ':hours_to_check' => $hours_to_check,
        ':management_id' => $management_id,
        ':user_id' => $_SESSION['user_id']
    ]);

    header("Location: manage_plane.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Plane Management</title>
</head>
<body>
    <h1>Edit Plane Management</h1>
    <nav>
        <a href="manage_plane.php">Back to Management</a>
    </nav>
    <br>

    <form action="" method="POST">
        <label for="num_of_flights">Number of Flights:</label>
        <input type="number" name="num_of_flights" id="num_of_flights" 
               value="<?php echo htmlspecialchars($data['num_of_flights'] ?? ''); ?>" required><br>

        <label for="hours_per_flight">Hours per Flight:</label>
        <input type="number" step="0.1" name="hours_per_flight" id="hours_per_flight" 
               value="<?php echo htmlspecialchars($data['hours_per_flight'] ?? ''); ?>" required><br>

        <label for="hours_spent_on_ferry">Hours Spent on Ferry:</label>
        <input type="number" step="0.1" name="hours_spent_on_ferry" id="hours_spent_on_ferry" 
               value="<?php echo htmlspecialchars($data['hours_spent_on_ferry'] ?? ''); ?>" required><br>

        <label for="maint_check">Max Hours Before Check:</label>
        <input type="number" step="0.1" name="maint_check" id="maint_check" 
               value="<?php echo htmlspecialchars($data['maint_check'] ?? ''); ?>" required><br>

        <label for="hours_to_check">Hours to Check:</label>
        <input type="text" name="hours_to_check" id="hours_to_check" 
               value="<?php echo htmlspecialchars($data['hours_to_check'] ?? ''); ?>" readonly><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
