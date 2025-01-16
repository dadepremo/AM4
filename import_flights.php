<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Function to process the CSV or text file and insert into the database
function importFlights($filePath) {
    global $conn;

    // Open the file for reading
    if (($file = fopen($filePath, "r")) !== false) {
        // Skip the header row (if necessary)
        fgetcsv($file, 1000, "\t"); // Assumes tab-separated values. Change to ',' for CSV.

        // Loop through the file line by line
        while (($line = fgetcsv($file, 1000, "\t")) !== false) { 
            list($plane_code, $aircraft_type, $profit, $destination, $route, $route_quotas, $seats, $fuel, $note) = $line;

            $profit = str_replace(['$', ',00', '.'], '', $profit);

            $query = "SELECT * FROM my_planes WHERE plane_code = :plane_code AND user_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->execute([':plane_code' => $plane_code, ':user_id' => $_SESSION['user_id']]);
            $aircraft = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($aircraft) {
                $insertQuery = "
                    INSERT INTO flights (plane_code, aircraft, profit, destination, route, route_quotas, seats, fuel, note, user_id)
                    VALUES (:plane_code, :aircraft, :profit, :destination, :route, :route_quotas, :seats, :fuel, :note, :user_id)
                ";

                $stmt = $conn->prepare($insertQuery);
                $stmt->execute([
                    ':plane_code' => $plane_code,
                    ':aircraft' => $aircraft['aircraft'],
                    ':profit' => $profit,
                    ':destination' => $destination,
                    ':route' => $route,
                    ':route_quotas' => $route_quotas,
                    ':seats' => $seats,
                    ':fuel' => $fuel,
                    ':note' => $note,
                    ':user_id' => $_SESSION['user_id']
                ]);

                // Update total hours and maintenance check
                $query1 = "SELECT * FROM my_planes WHERE plane_code = :plane_code AND user_id = :user_id";
                $stmt1 = $conn->prepare($query1);
                $stmt1->execute([':plane_code' => $plane_code, ':user_id' => $_SESSION['user_id']]);
                $aircraft1 = $stmt1->fetch(PDO::FETCH_ASSOC);

                if ($aircraft1) {
                    $flights_num1 = $aircraft1['num_of_flights'] + 1;
                    $hours_per_flight1 = $aircraft1['hours_per_flight'];
                    $hours_spent_on_ferry1 = $aircraft1['hours_spent_on_ferry'];
                    $total_hours1 = $flights_num1 * $hours_per_flight1 + $hours_spent_on_ferry1;
                    $maint_check1 = $aircraft1['maint_check'];
                    $hours_to_check1 = $maint_check1 - $total_hours1;

                    $updateQueryy = "
                        UPDATE my_planes SET 
                        total_hours = :total_hours, 
                        hours_to_check = :hours_to_check,
                        num_of_flights = :num 
                        WHERE plane_code = :plane_code AND user_id = :user_id
                    ";

                    $stmtt2 = $conn->prepare($updateQueryy);
                    $stmtt2->execute([
                        ':plane_code' => $plane_code,
                        ':total_hours' => $total_hours1,
                        ':hours_to_check' => $hours_to_check1,
                        ':num' => $flights_num1,
                        ':user_id' => $_SESSION['user_id']
                    ]);
                } else {
                    echo "Plane not found in the database.";
                }
            } else {
                echo "Aircraft model not found for plane code: $plane_code.<br>";
            }
        }

        fclose($file);
        echo "Flights imported successfully!";
    } else {
        echo "Error opening file!";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['flights_file'])) {
    $filePath = $_FILES['flights_file']['tmp_name'];
    importFlights($filePath);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Flights</title>
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
            margin-top: 20px;
            color: #444;
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

        input[type="file"], button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
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

            input, button {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <h1>Import Flight Data</h1>
    <form action="import_flights.php" method="POST" enctype="multipart/form-data">
        <label for="flights_file">Choose the file to import:</label>
        <input type="file" name="flights_file" id="flights_file" accept=".csv, .txt" required>

        <button type="submit">Import Flights</button>
        <a href="index.php">Home</a>
    </form>
</body>
</html>
