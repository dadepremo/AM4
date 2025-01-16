<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Fetch plane management data for the logged-in user
$query = "
    SELECT * FROM my_planes WHERE user_id = :user_id
";
$stmt = $conn->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$managementData = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Plane Hours</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn-edit {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-edit:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Manage Plane Hours</h1>
    <nav>
        <a href="index.php">Back</a>
    </nav>
    <br>

    <table>
        <thead>
            <tr>
                <th>Plane Code</th>
                <th>Flights</th>
                <th>Hours per Flight</th>
                <th>Total Hours</th>
                <th>Hours Spent on Ferry</th>
                <th>Hours Max Before Check</th>
                <th>Hours to Check</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($managementData as $data): ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['plane_code']); ?></td>
                    <td><?php echo htmlspecialchars($data['num_of_flights']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($data['hours_per_flight'], 1)); ?></td>
                    <td><?php echo htmlspecialchars(number_format($data['total_hours'], 1)); ?></td>
                    <td><?php echo htmlspecialchars(number_format($data['hours_spent_on_ferry'], 1)); ?></td>
                    <td><?php echo htmlspecialchars(number_format($data['maint_check'], 1)); ?></td>
                    <td><?php echo htmlspecialchars(number_format($data['hours_to_check'], 1)); ?></td>
                    <td>
                        <a class="btn-edit" href="edit_plane_management.php?management_id=<?php echo $data['plane_code']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
