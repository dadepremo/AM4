<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php';

// Fetch airports data from the database for the dropdown menu
$airportQuery = "SELECT location, icao_code, iata_code FROM airports ORDER BY location ASC";
$airportStmt = $conn->prepare($airportQuery);
$airportStmt->execute();
$airports = $airportStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
        color: #333;
    }

    h1 {
        font-size: 36px;
        text-align: center;
        margin-top: 20px;
        color: #4CAF50; /* Green color for the header */
    }

    /* Form Styles */
    form {
        max-width: 600px;
        margin: 40px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    label {
        font-size: 16px;
        display: block;
        margin-bottom: 8px;
        color: #555;
    }

    input[type="file"], input[type="text"], select, button {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        margin: 8px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    input[type="file"]:focus, input[type="text"]:focus, select:focus {
        border-color: #4CAF50; /* Green border on focus */
    }

    button {
        background-color: #4CAF50;
        color: white;
        font-size: 18px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
        padding: 12px;
    }

    button:hover {
        background-color: #45a049; /* Darker green on hover */
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        h1 {
            font-size: 28px;
        }

        form {
            width: 90%;
        }

        button {
            font-size: 16px;
            padding: 10px;
        }
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Routes CSV</title>
</head>
<body>
    <h1>Upload Routes CSV</h1>

    <!-- Form for uploading CSV file -->
    <form action="process_csv.php" method="POST" enctype="multipart/form-data">
        <label for="csv_file">Seleziona il file CSV:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required><br>

        <label for="origin_airport_iata">Origin Airport IATA Code:</label>
        <select name="origin_airport_iata" id="origin_airport_iata" required>
            <option value="">Select an airport</option>
            <?php foreach ($airports as $airport): ?>
                <option value="<?= $airport['iata_code'] ?>">
                    <?= $airport['location'] . ', ' . $airport['icao_code'] . ' / ' . $airport['iata_code'] ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="plane_model">Plane Model:</label>
        <input type="text" name="plane_model" id="plane_model" required><br>

        <button type="submit">Upload and Process</button>
    </form>
</body>
</html>
