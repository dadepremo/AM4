<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Check if the plane_id is provided in the URL
if (isset($_GET['id'])) {
    $plane_id = $_GET['id'];

    // Fetch the plane details from the database
    $query = "SELECT * FROM my_planes WHERE plane_id = :plane_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':plane_id', $plane_id, PDO::PARAM_INT);
    $stmt->execute();
    $plane = $stmt->fetch(PDO::FETCH_ASSOC);

    // If plane is not found, redirect to the planes list page
    if (!$plane) {
        header("Location: view_planes.php");
        exit;
    }
} else {
    header("Location: view_planes.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated plane details from the form
    $plane_code = $_POST['plane_code'];
    $aircraft = $_POST['aircraft'];
    $manufacturer = $_POST['manufacturer'];
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
    $flight_route = $_POST['flight_route'];

    // Update the plane details in the database
    $update_query = "
        UPDATE my_planes
        SET plane_code = :plane_code,
            aircraft = :aircraft,
            manufacturer = :manufacturer,
            crew_pilots = :crew_pilots,
            crew_crew = :crew_crew,
            crew_engineers = :crew_engineers,
            crew_tech = :crew_tech,
            cruise_speed = :cruise_speed,
            capacity = :capacity,
            runway_required = :runway_required,
            delivery_time = :delivery_time,
            a_check_cost = :a_check_cost,
            flight_range = :flight_range,
            consumption = :consumption,
            service_ceiling = :service_ceiling,
            co2_emission = :co2_emission,
            maint_check = :maint_check,
            short_name = :short_name,
            my_short_name = :my_short_name,
            hub = :hub,
            flight_route = :flight_route
        WHERE plane_id = :plane_id
    ";

    $stmt = $conn->prepare($update_query);
    $stmt->execute([
        ':plane_code' => $plane_code,
        ':aircraft' => $aircraft,
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
        ':short_name' => $short_name,
        ':my_short_name' => $my_short_name,
        ':hub' => $hub,
        ':flight_route' => $flight_route,
        ':plane_id' => $plane_id
    ]);

    echo "Plane details updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Plane</title>
</head>
<body>
    <h1>Edit Plane Details</h1>
    <nav>
        <a href="view_planes.php">Back</a>
    </nav>
    <!-- Form to edit plane details -->
    <form action="edit_plane.php?id=<?php echo $plane['plane_id']; ?>" method="POST">
        <label for="plane_code">Plane Code:</label>
        <input type="text" name="plane_code" id="plane_code" value="<?php echo htmlspecialchars($plane['plane_code']); ?>" required><br>

        <label for="aircraft">Aircraft:</label>
        <input type="text" name="aircraft" id="aircraft" value="<?php echo htmlspecialchars($plane['aircraft']); ?>" required><br>

        <label for="manufacturer">Manufacturer:</label>
        <input type="text" name="manufacturer" id="manufacturer" value="<?php echo htmlspecialchars($plane['manufacturer']); ?>"><br>

        <label for="crew_pilots">Crew Pilots:</label>
        <input type="number" name="crew_pilots" id="crew_pilots" value="<?php echo htmlspecialchars($plane['crew_pilots']); ?>"><br>

        <label for="crew_crew">Crew Crew:</label>
        <input type="number" name="crew_crew" id="crew_crew" value="<?php echo htmlspecialchars($plane['crew_crew']); ?>"><br>

        <label for="crew_engineers">Crew Engineers:</label>
        <input type="number" name="crew_engineers" id="crew_engineers" value="<?php echo htmlspecialchars($plane['crew_engineers']); ?>"><br>

        <label for="crew_tech">Crew Tech:</label>
        <input type="number" name="crew_tech" id="crew_tech" value="<?php echo htmlspecialchars($plane['crew_tech']); ?>"><br>

        <label for="cruise_speed">Cruise Speed (kph):</label>
        <input type="number" name="cruise_speed" id="cruise_speed" value="<?php echo htmlspecialchars($plane['cruise_speed']); ?>"><br>

        <label for="capacity">Capacity (pax):</label>
        <input type="number" name="capacity" id="capacity" value="<?php echo htmlspecialchars($plane['capacity']); ?>"><br>

        <label for="runway_required">Runway Required (ft):</label>
        <input type="number" name="runway_required" id="runway_required" value="<?php echo htmlspecialchars($plane['runway_required']); ?>"><br>

        <label for="delivery_time">Delivery Time:</label>
        <input type="time" name="delivery_time" id="delivery_time" value="<?php echo htmlspecialchars($plane['delivery_time']); ?>"><br>

        <label for="a_check_cost">A-Check Cost ($):</label>
        <input type="number" name="a_check_cost" id="a_check_cost" step="0.01" value="<?php echo htmlspecialchars($plane['a_check_cost']); ?>"><br>

        <label for="flight_range">Flight Range (km):</label>
        <input type="number" name="flight_range" id="flight_range" value="<?php echo htmlspecialchars($plane['flight_range']); ?>"><br>

        <label for="consumption">Consumption (lbs/km):</label>
        <input type="number" name="consumption" id="consumption" step="0.01" value="<?php echo htmlspecialchars($plane['consumption']); ?>"><br>

        <label for="service_ceiling">Service Ceiling (ft):</label>
        <input type="number" name="service_ceiling" id="service_ceiling" value="<?php echo htmlspecialchars($plane['service_ceiling']); ?>"><br>

        <label for="co2_emission">CO2 Emission (kg/pax/km):</label>
        <input type="number" name="co2_emission" id="co2_emission" step="0.01" value="<?php echo htmlspecialchars($plane['co2_emission']); ?>"><br>

        <label for="maint_check">Maintenance Check (hrs):</label>
        <input type="number" name="maint_check" id="maint_check" value="<?php echo htmlspecialchars($plane['maint_check']); ?>"><br>

        <label for="short_name">Short Name:</label>
        <input type="text" name="short_name" id="short_name" value="<?php echo htmlspecialchars($plane['short_name']); ?>"><br>

        <label for="my_short_name">My Short Name:</label>
        <input type="text" name="my_short_name" id="my_short_name" value="<?php echo htmlspecialchars($plane['my_short_name']); ?>"><br>

        <label for="hub">Hub:</label>
        <input type="text" name="hub" id="hub" value="<?php echo htmlspecialchars($plane['hub']); ?>"><br>

        <label for="flight_route">Flight Route:</label>
        <input type="text" name="flight_route" id="flight_route" value="<?php echo htmlspecialchars($plane['flight_route']); ?>"><br>

        <button type="submit">Update Plane</button>
    </form>
</body>
</html>
