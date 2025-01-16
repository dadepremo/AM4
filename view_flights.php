<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Fetch flights from the database
$query = "
    SELECT flight_id, plane_code, aircraft, profit, destination, route, route_quotas, seats, fuel, note, created_at, updated_at
    FROM flights
    WHERE user_id = :user_id
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flights</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Flights</h1>
    <nav>
        <a href="add_flight.php">Add New Flight</a>
        <a href="index.php">Home</a>
    </nav>
    <br>

    <table>
        <thead>
            <tr>
                <th>Flight ID</th>
                <th>Plane Code</th>
                <th>Aircraft</th>
                <th>Profit</th>
                <th>Destination</th>
                <th>Route</th>
                <th>Route Quotas</th>
                <th>Seats</th>
                <th>Fuel</th>
                <th>Note</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($flights)): ?>
                <tr>
                    <td colspan="12">No flights found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($flights as $flight): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($flight['flight_id']); ?></td>
                        <td><?php echo htmlspecialchars($flight['plane_code']); ?></td>
                        <td><?php echo htmlspecialchars($flight['aircraft']); ?></td>
                        <td><?php echo htmlspecialchars('$' . number_format($flight['profit'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($flight['destination']); ?></td>
                        <td><?php echo htmlspecialchars($flight['route']); ?></td>
                        <td><?php echo htmlspecialchars($flight['route_quotas']); ?></td>
                        <td><?php echo htmlspecialchars($flight['seats']); ?></td>
                        <td><?php echo htmlspecialchars($flight['fuel']); ?></td>
                        <td><?php echo htmlspecialchars($flight['note']); ?></td>
                        <td><?php echo htmlspecialchars($flight['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($flight['updated_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
