<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $flight_number = $_POST['flight_number'];
    $departure = $_POST['departure'];
    $arrival = $_POST['arrival'];
    $price = $_POST['price'];

    $query = "INSERT INTO flights (flight_number, departure, arrival, price) VALUES (:flight_number, :departure, :arrival, :price)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':flight_number', $flight_number);
    $stmt->bindParam(':departure', $departure);
    $stmt->bindParam(':arrival', $arrival);
    $stmt->bindParam(':price', $price);
    $stmt->execute();

    echo "Flight added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Flight</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Add Flight</h1>
    <form method="POST">
        <label>Flight Number:</label>
        <input type="text" name="flight_number" required><br>
        <label>Departure:</label>
        <input type="text" name="departure" required><br>
        <label>Arrival:</label>
        <input type="text" name="arrival" required><br>
        <label>Price:</label>
        <input type="number" name="price" required><br>
        <button type="submit">Add Flight</button>
    </form>
</body>
</html>
