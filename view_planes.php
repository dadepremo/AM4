<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Fetch the planes from the database
$query = "SELECT * FROM my_planes where user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->execute([
    ':user_id' => $_SESSION['user_id']
]);
$planes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Planes</title>
    <style>
        table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
}

a {
    color: #0066cc;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <h1>Planes in Fleet</h1>
    <nav>
        <a href="index.php">Back</a>
    </nav>
    <table border="1">
        <thead>
            <tr>
                <th>Plane Code</th>
                <th>Aircraft</th>
                <th>Manufacturer</th>
                <th>Cruise Speed</th>
                <th>Capacity</th>
                <th>Runway Required</th>
                <th>Delivery Time</th>
                <th>A-Check Cost</th>
                <th>Flight Range</th>
                <th>Consumption</th>
                <th>Service Ceiling</th>
                <th>CO2 Emission</th>
                <th>Maint Check</th>
                <th>Hours to Check</th>
                <th>Flights</th>
                <th>Short Name</th>
                <th>Hub</th>
                <th>Flight Route</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through the fetched planes and display them in rows
            foreach ($planes as $plane) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($plane['plane_code']) . "</td>";
                echo "<td>" . htmlspecialchars($plane['aircraft']) . "</td>";
                echo "<td>" . htmlspecialchars($plane['manufacturer']) . "</td>";
                echo "<td>" . htmlspecialchars($plane['cruise_speed']) . " kph</td>";
                echo "<td>" . htmlspecialchars($plane['capacity']) . " pax</td>";
                echo "<td>" . htmlspecialchars($plane['runway_required']) . " ft</td>";
                echo "<td>" . htmlspecialchars($plane['delivery_time']) . "</td>";
                echo "<td>$" . number_format($plane['a_check_cost'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($plane['flight_range']) . " km</td>";
                echo "<td>" . htmlspecialchars($plane['consumption']) . " lbs/km</td>";
                echo "<td>" . htmlspecialchars($plane['service_ceiling']) . " ft</td>";
                echo "<td>" . htmlspecialchars($plane['co2_emission']) . " kg/pax/km</td>";
                echo "<td>" . htmlspecialchars($plane['maint_check']) . " hrs</td>";
                echo "<td>" . htmlspecialchars($plane['hours_to_check']) . " hrs</td>";
                echo "<td>" . htmlspecialchars($plane['num_of_flights']) . "</td>";
                echo "<td>" . htmlspecialchars($plane['short_name']) . "</td>";
                echo "<td>" . htmlspecialchars($plane['hub']) . "</td>";
                echo "<td>" . htmlspecialchars($plane['flight_route']) . "</td>";
                echo "<td><a href='edit_plane.php?id=" . $plane['plane_id'] . "'>Edit</a> | <a href='delete_plane.php?id=" . $plane['plane_id'] . "'>Delete</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
