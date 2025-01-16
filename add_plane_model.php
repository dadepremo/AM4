<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect data from the form
    $cruise_speed = $_POST['cruise_speed'];
    $capacity = $_POST['capacity'];
    $runway_required = $_POST['runway_required'];
    $delivery_time = $_POST['delivery_time'];
    $a_check_cost = $_POST['a_check_cost'];
    $flight_range = $_POST['flight_range'];
    $consumption = $_POST['consumption'];
    $service_ceiling = $_POST['service_ceiling'];
    $co2_emission = $_POST['co2_emission'];
    $maint_check = $_POST['maint_check'];
    $crew_pilots = $_POST['crew_pilots'];
    $crew_crew = $_POST['crew_crew'];
    $crew_engineers = $_POST['crew_engineers'];
    $crew_tech = $_POST['crew_tech'];

    // New fields
    $manufacturer = $_POST['manufacturer'];
    $aircraft = $_POST['aircraft'];
    $short_name = $_POST['short_name'];
    $my_short_name = $_POST['my_short_name'];

    try {
        // Insert plane model data into the database
        $query = "
            INSERT INTO plane_models (
                cruise_speed, capacity, runway_required, delivery_time, a_check_cost, 
                flight_range, consumption, service_ceiling, co2_emission, maint_check, 
                crew_pilots, crew_crew, crew_engineers, crew_tech,
                manufacturer, aircraft, short_name, my_short_name
            ) VALUES (
                :cruise_speed, :capacity, :runway_required, :delivery_time, :a_check_cost, 
                :flight_range, :consumption, :service_ceiling, :co2_emission, :maint_check, 
                :crew_pilots, :crew_crew, :crew_engineers, :crew_tech,
                :manufacturer, :aircraft, :short_name, :my_short_name
            )
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':cruise_speed' => $cruise_speed,
            ':capacity' => $capacity,
            ':runway_required' => $runway_required,
            ':delivery_time' => $delivery_time,
            ':a_check_cost' => $a_check_cost,
            ':flight_range' => $flight_range,
            ':consumption' => $consumption,
            ':service_ceiling' => $service_ceiling,
            ':co2_emission' => $co2_emission,
            ':maint_check' => $maint_check,
            ':crew_pilots' => $crew_pilots,
            ':crew_crew' => $crew_crew,
            ':crew_engineers' => $crew_engineers,
            ':crew_tech' => $crew_tech,
            ':manufacturer' => $manufacturer,
            ':aircraft' => $aircraft,
            ':short_name' => $short_name,
            ':my_short_name' => $my_short_name,
        ]);

        echo "Plane model added successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<style>
    /* General Body Styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        padding: 0;
        color: #333;
    }

    /* Header Styling */
    h1 {
        text-align: center;
        font-size: 36px;
        margin-top: 40px;
        color: #4CAF50; /* Green color */
    }

    /* Form container styling */
    form {
        width: 80%;
        max-width: 900px;
        margin: 30px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Form Labels */
    label {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 8px;
        display: block;
        color: #555;
    }

    /* Input Fields */
    input[type="text"], 
    input[type="number"], 
    input[type="time"] {
        width: 100%;
        padding: 12px;
        font-size: 14px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
    }

    /* Submit Button Styling */
    button {
        background-color: #4CAF50;
        color: white;
        padding: 12px 20px;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #45a049;
    }

    /* Back Link Styling */
    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        font-size: 16px;
        color: #007bff;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        form {
            width: 90%;
        }

        h1 {
            font-size: 28px;
        }

        button {
            padding: 10px;
        }

        label, input {
            font-size: 14px;
        }
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Plane Model</title>
</head>
<body>
    <h1>Add Plane Model</h1>
    <form action="add_plane_model.php" method="POST">
        <label for="cruise_speed">Cruise Speed (KPH):</label>
        <input type="number" name="cruise_speed" id="cruise_speed" required><br>

        <label for="capacity">Capacity (Pax):</label>
        <input type="number" name="capacity" id="capacity" required><br>

        <label for="runway_required">Runway Required (Feet):</label>
        <input type="number" name="runway_required" id="runway_required" required><br>

        <label for="delivery_time">Delivery Time (HH:MM:SS):</label>
        <input type="time" name="delivery_time" id="delivery_time" required><br>

        <label for="a_check_cost">A-Check Cost (USD):</label>
        <input type="number" name="a_check_cost" id="a_check_cost" step="0.01" required><br>

        <label for="flight_range">Flight Range (KM):</label>
        <input type="number" name="flight_range" id="flight_range" required><br>

        <label for="consumption">Consumption (LBS/KM):</label>
        <input type="number" name="consumption" id="consumption" step="0.01" required><br>

        <label for="service_ceiling">Service Ceiling (Feet):</label>
        <input type="number" name="service_ceiling" id="service_ceiling" required><br>

        <label for="co2_emission">CO2 Emission (kg/pax/km):</label>
        <input type="number" name="co2_emission" id="co2_emission" step="0.01" required><br>

        <label for="maint_check">Maintenance Check (Hours):</label>
        <input type="number" name="maint_check" id="maint_check" required><br>

        <label for="crew_pilots">Pilots Required:</label>
        <input type="number" name="crew_pilots" id="crew_pilots" required><br>

        <label for="crew_crew">Crew Members Required:</label>
        <input type="number" name="crew_crew" id="crew_crew" required><br>

        <label for="crew_engineers">Engineers Required:</label>
        <input type="number" name="crew_engineers" id="crew_engineers" required><br>

        <label for="crew_tech">Technicians Required:</label>
        <input type="number" name="crew_tech" id="crew_tech" required><br>

        <!-- New Fields -->
        <label for="manufacturer">Manufacturer:</label>
        <input type="text" name="manufacturer" id="manufacturer" required><br>

        <label for="aircraft">Aircraft:</label>
        <input type="text" name="aircraft" id="aircraft" required><br>

        <label for="short_name">Short Name:</label>
        <input type="text" name="short_name" id="short_name" required><br>

        <label for="my_short_name">My Short Name:</label>
        <input type="text" name="my_short_name" id="my_short_name" required><br>

        <button type="submit">Add Plane Model</button>
    </form>

    <a href="index.php" class="back-link">Back to Home</a>
</body>
</html>
