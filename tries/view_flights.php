<?php
include 'database.php';

$query = "SELECT * FROM flights";
$stmt = $conn->prepare($query);
$stmt->execute();
$flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Flights</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Available Flights</h1>
    <table border="1">
        <tr>
            <th>Flight Number</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Price</th>
        </tr>
        <?php foreach ($flights as $flight): ?>
        <tr>
            <td><?php echo htmlspecialchars($flight['flight_number']); ?></td>
            <td><?php echo htmlspecialchars($flight['departure']); ?></td>
            <td><?php echo htmlspecialchars($flight['arrival']); ?></td>
            <td><?php echo htmlspecialchars($flight['price']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
