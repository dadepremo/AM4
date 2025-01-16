<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Fetching the list of aircrafts (plane models) from the database
$query = "SELECT plane_model_id, manufacturer, aircraft FROM plane_models";
$stmt = $conn->prepare($query);
$stmt->execute();
$aircrafts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #444;
            margin-top: 20px;
        }

        form {
            max-width: 500px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input, select, textarea, button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            height: 80px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            form {
                padding: 15px;
            }

            input, select, textarea, button {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <h1>Track Flight</h1>
    <form action="process_flight.php" method="POST">
        <label for="plane_code">Plane Code:</label>
        <input type="text" name="plane_code" id="plane_code" required>

        <label for="aircraft">Aircraft:</label>
        <select name="aircraft" id="aircraft" required>
            <option value="">Select Aircraft</option>
            <?php foreach ($aircrafts as $aircraft): ?>
                <option value="<?php echo $aircraft['aircraft']; ?>">
                    <?php echo $aircraft['manufacturer'] . ', ' . $aircraft['aircraft']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="profit">Profit:</label>
        <input type="number" step="0.01" name="profit" id="profit" required>

        <label for="destination">Destination (ICAO-ICAO):</label>
        <input type="text" name="destination" id="destination" required>

        <label for="route_quotas">Route Quotas:</label>
        <input type="number" name="route_quotas" id="route_quotas" required>

        <label for="seats">Seats:</label>
        <input type="text" name="seats" id="seats" required>

        <label for="fuel">Fuel:</label>
        <input type="number" step="0.01" name="fuel" id="fuel" required>

        <label for="note">Note:</label>
        <textarea name="note" id="note"></textarea>

        <button type="submit">Save Flight</button>
        <a href="index.php">Home</a>
    </form>
</body>
</html>
