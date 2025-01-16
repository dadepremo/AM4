<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $plane_code = $_POST['plane_code'];
    $aircraft = $_POST['aircraft'];
    $profit = $_POST['profit'];
    $destination = strtoupper($_POST['destination']);
    $route_quotas = $_POST['route_quotas'];
    $seats = $_POST['seats'];
    $fuel = $_POST['fuel'];
    $note = $_POST['note'];
    $user_id = $_SESSION['user_id'];

    // Prepare and execute the SQL query to insert the flight data into the database
    $query = "
        INSERT INTO flights (plane_code, aircraft, profit, destination, route_quotas, seats, fuel, note, user_id)
        VALUES (:plane_code, :aircraft, :profit, :destination, :route_quotas, :seats, :fuel, :note, :user_id)
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':plane_code' => $plane_code,
        ':aircraft' => $aircraft,
        ':profit' => $profit,
        ':destination' => $destination,
        ':route_quotas' => $route_quotas,
        ':seats' => $seats,
        ':fuel' => $fuel,
        ':note' => $note,
        ':user_id' => $user_id
    ]);

    echo "Flight record successfully saved.";
} else {
    echo "Invalid request.";
}
?>

