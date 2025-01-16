<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $location = ucfirst($_POST['location']);
    $country = ucfirst($_POST['country']);
    $iata_code = strtoupper($_POST['iata_code']);
    $icao_code = strtoupper($_POST['icao_code']);

    try {
        // Prepare the SQL query to insert the data into the airports table
        $query = "INSERT INTO airports (location, country, iata_code, icao_code) 
                  VALUES (:location, :country, :iata_code, :icao_code)";
        $stmt = $conn->prepare($query);

        // Execute the query with the provided data
        $stmt->execute([
            ':location' => $location,
            ':country' => $country,
            ':iata_code' => $iata_code,
            ':icao_code' => $icao_code,
        ]);

        echo "Airport added successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<style>
    /* Body Styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f8f8;
        margin: 0;
        padding: 0;
        color: #333;
    }

    /* Header Styling */
    h1 {
        text-align: center;
        font-size: 36px;
        margin-top: 30px;
        color: #4CAF50; /* Green color for title */
    }

    /* Navigation Styling */
    a {
        font-size: 16px;
        text-decoration: none;
        color: #007bff;
        padding: 8px 15px;
        border-radius: 4px;
        margin-top: 20px;
        display: inline-block;
    }

    a:hover {
        background-color: #f2f2f2;
        color: #0056b3;
    }

    /* Form Styling */
    form {
        width: 60%;
        margin: 30px auto;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    label {
        font-size: 16px;
        display: block;
        margin: 10px 0 5px;
    }

    input[type="text"] {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    button {
        background-color: #4CAF50;
        color: white;
        padding: 12px 20px;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
    }

    button:hover {
        background-color: #45a049;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        form {
            width: 90%;
        }

        h1 {
            font-size: 28px;
        }

        a {
            font-size: 14px;
            padding: 6px 12px;
        }
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Airport</title>
</head>
<body>

    <h1>Add New Airport</h1>

    <a href="index.php">Back to Home</a>


    <!-- Form to add airport data -->
    <form method="POST" action="add_airport.php">
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required><br>

        <label for="country">Country:</label>
        <input type="text" id="country" name="country" required><br>

        <label for="iata_code">IATA Code:</label>
        <input type="text" id="iata_code" name="iata_code" required><br>

        <label for="icao_code">ICAO Code:</label>
        <input type="text" id="icao_code" name="icao_code" required><br>

        <button type="submit">Add Airport</button>
    </form>

</body>
</html>
