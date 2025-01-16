<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Fetch plane models from the plane_models table
$plane_models_query = "SELECT plane_model_id, manufacturer, aircraft FROM plane_models";
$plane_models_stmt = $conn->prepare($plane_models_query);
$plane_models_stmt->execute();
$plane_models = $plane_models_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch airports for the hub dropdown
$airports_query = "SELECT location, icao_code, iata_code FROM airports WHERE user_id = :user_id";
$airports_stmt = $conn->prepare($airports_query);
$airports_stmt->execute([':user_id' => $_SESSION['user_id']]);
$airports = $airports_stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $plane_code = $_POST['plane_code'];
    $plane_model_id = $_POST['plane_model']; // The plane model ID from the dropdown
    $manufacturer = $_POST['manufacturer'];
    $aircraft = $_POST['aircraft']; // Get the aircraft name directly
    $crew_pilots = $_POST['crew_pilots'];
    $crew_crew = $_POST['crew_crew'];
    $crew_engineers = $_POST['crew_engineers'];
    $crew_tech = $_POST['crew_tech'];
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
    $short_name = $_POST['short_name'];
    $my_short_name = $_POST['my_short_name'];
    $hub = $_POST['hub'];
    $user_id = $_SESSION['user_id'];

    // Prepare and execute the SQL query to insert the plane data into the database
    $query = "
        INSERT INTO my_planes 
        (plane_code, aircraft, manufacturer, crew_pilots, crew_crew, crew_engineers, crew_tech, 
         cruise_speed, capacity, runway_required, delivery_time, a_check_cost, flight_range, consumption, 
         service_ceiling, co2_emission, maint_check, hours_to_check, num_of_flights, short_name, my_short_name, hub, user_id)
        VALUES
        (:plane_code, :aircraft, :manufacturer, :crew_pilots, :crew_crew, :crew_engineers, :crew_tech, 
         :cruise_speed, :capacity, :runway_required, :delivery_time, :a_check_cost, :flight_range, :consumption, 
         :service_ceiling, :co2_emission, :maint_check, :hours_to_check, :num_of_flights, :short_name, :my_short_name, :hub, :user_id)
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([ 
        ':plane_code' => $plane_code,
        ':aircraft' => $aircraft,  // Store the aircraft directly
        ':manufacturer' => $manufacturer,
        ':crew_pilots' => $crew_pilots,
        ':crew_crew' => $crew_crew,
        ':crew_engineers' => $crew_engineers,
        ':crew_tech' => $crew_tech,
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
        ':hours_to_check' => $maint_check,
        ':num_of_flights' => 0,
        ':short_name' => $short_name,
        ':my_short_name' => $my_short_name,
        ':hub' => $hub,  // Store the selected hub
        ':user_id' => $user_id
    ]);

    echo "Plane record successfully saved.";

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Plane</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            padding-top: 30px;
        }

        nav {
            text-align: right;
            margin-right: 20px;
            margin-top: 10px;
        }

        form {
            width: 60%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="number"], input[type="text"], input[type="time"], select {
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        select:disabled {
            background-color: #e6e6e6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 48%;
            display: inline-block;
            margin-right: 4%;
        }

        .form-group input:last-child {
            margin-right: 0;
        }

        .form-group select {
            width: 48%;
            display: inline-block;
            margin-right: 4%;
        }

        .form-group select:last-child {
            margin-right: 0;
        }

        @media (max-width: 768px) {
            form {
                width: 90%;
            }

            .form-group input,
            .form-group select {
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <h1>Add Plane</h1>
    <nav>
        <a href="index.php" style="text-decoration: none; color: #4CAF50; font-size: 16px;">Back</a>
    </nav>
    <form action="add_plane.php" method="POST">
        <div class="form-group">
            <label for="plane_code">Plane Code:</label>
            <input type="text" name="plane_code" id="plane_code" required>
        </div>

        <div class="form-group">
            <label for="plane_model">Plane Model:</label>
            <select name="plane_model" id="plane_model" required>
                <option value="" disabled selected>Select Plane Model</option>
                <?php
                // Populate the dropdown with planes from the plane_models table
                foreach ($plane_models as $plane_model) {
                    $display_name = $plane_model['manufacturer'] . ", " . $plane_model['aircraft'];
                    echo "<option value='{$plane_model['plane_model_id']}'>{$display_name}</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="aircraft">Aircraft Name:</label>
            <input type="text" name="aircraft" id="aircraft" readonly>
        </div>

        <div class="form-group">
            <label for="manufacturer">Manufacturer:</label>
            <input type="text" name="manufacturer" id="manufacturer" readonly>
        </div>

        <div class="form-group">
            <label for="crew_pilots">Pilots:</label>
            <input type="number" name="crew_pilots" id="crew_pilots" required>
        </div>

        <div class="form-group">
            <label for="crew_crew">Crew:</label>
            <input type="number" name="crew_crew" id="crew_crew" required>
        </div>

        <div class="form-group">
            <label for="crew_engineers">Engineers:</label>
            <input type="number" name="crew_engineers" id="crew_engineers" required>
        </div>

        <div class="form-group">
            <label for="crew_tech">Technicians:</label>
            <input type="number" name="crew_tech" id="crew_tech" required>
        </div>

        <div class="form-group">
            <label for="cruise_speed">Cruise Speed (kph):</label>
            <input type="number" name="cruise_speed" id="cruise_speed">
        </div>

        <div class="form-group">
            <label for="capacity">Capacity (pax):</label>
            <input type="number" name="capacity" id="capacity">
        </div>

        <div class="form-group">
            <label for="runway_required">Runway Required (ft):</label>
            <input type="number" name="runway_required" id="runway_required">
        </div>

        <div class="form-group">
            <label for="delivery_time">Delivery Time (HH:MM:SS):</label>
            <input type="time" name="delivery_time" id="delivery_time">
        </div>

        <div class="form-group">
            <label for="a_check_cost">A-Check Cost ($):</label>
            <input type="text" name="a_check_cost" id="a_check_cost">
        </div>

        <div class="form-group">
            <label for="flight_range">Flight Range (km):</label>
            <input type="number" name="flight_range" id="flight_range">
        </div>

        <div class="form-group">
            <label for="consumption">Fuel Consumption (lbs/km):</label>
            <input type="number" name="consumption" id="consumption" step="0.01">
        </div>

        <div class="form-group">
            <label for="service_ceiling">Service Ceiling (ft):</label>
            <input type="number" name="service_ceiling" id="service_ceiling">
        </div>

        <div class="form-group">
            <label for="co2_emission">CO2 Emission (kg/pax/km):</label>
            <input type="number" name="co2_emission" id="co2_emission" step="0.01">
        </div>

        <div class="form-group">
            <label for="maint_check">Maintenance Check (hrs):</label>
            <input type="number" name="maint_check" id="maint_check">
        </div>

        <div class="form-group">
            <label for="short_name">Short Name:</label>
            <input type="text" name="short_name" id="short_name">
        </div>

        <div class="form-group">
            <label for="my_short_name">Custom Short Name:</label>
            <input type="text" name="my_short_name" id="my_short_name">
        </div>

        <div class="form-group">
            <label for="hub">Hub:</label>
            <select name="hub" id="hub" required>
                <option value="" disabled selected>Select Hub</option>
                <?php
                // Populate the hub dropdown with airport names and ICAO/IATA codes
                foreach ($airports as $airport) {
                    $display_name = $airport['location'] . ", " . $airport['icao_code'] . "/" . $airport['iata_code'];
                    echo "<option value='{$airport['iata_code']}'>{$display_name}</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit">Add Plane</button>
    </form>

    <script>
        $(document).ready(function() {
            $('#plane_model').change(function() {
                var plane_model_id = $(this).val();

                if (!plane_model_id) {
                    // Clear all fields when no plane model is selected
                    $('#manufacturer').val('');
                    $('#aircraft').val('');
                    $('#crew_pilots').val('2');
                    $('#crew_crew').val('2');
                    $('#crew_engineers').val('1');
                    $('#crew_tech').val('2');
                    $('#cruise_speed').val('');
                    $('#capacity').val('');
                    $('#runway_required').val('');
                    $('#delivery_time').val('');
                    $('#a_check_cost').val('');
                    $('#flight_range').val('');
                    $('#consumption').val('');
                    $('#service_ceiling').val('');
                    $('#co2_emission').val('');
                    $('#maint_check').val('');
                    $('#short_name').val('');
                    $('#my_short_name').val('');
                } else {
                    $.ajax({
                        url: 'get_plane_specs.php', // Fetch the data
                        type: 'GET',
                        data: { plane_model_id: plane_model_id },
                        success: function(response) {
                            // Parse the JSON response
                            var data = JSON.parse(response);

                            // Fill the form fields with the retrieved data
                            $('#manufacturer').val(data.manufacturer);
                            $('#aircraft').val(data.aircraft);
                            $('#crew_pilots').val(data.crew_pilots);
                            $('#crew_crew').val(data.crew_crew);
                            $('#crew_engineers').val(data.crew_engineers);
                            $('#crew_tech').val(data.crew_tech);
                            $('#cruise_speed').val(data.cruise_speed);
                            $('#capacity').val(data.capacity);
                            $('#runway_required').val(data.runway_required);
                            $('#delivery_time').val(data.delivery_time);
                            $('#a_check_cost').val(data.a_check_cost);
                            $('#flight_range').val(data.flight_range);
                            $('#consumption').val(data.consumption);
                            $('#service_ceiling').val(data.service_ceiling);
                            $('#co2_emission').val(data.co2_emission);
                            $('#maint_check').val(data.maint_check);
                            $('#short_name').val(data.short_name);
                            $('#my_short_name').val(data.my_short_name);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
