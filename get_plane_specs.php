<?php
include 'database.php';

if (isset($_GET['plane_model_id'])) {
    $plane_model_id = $_GET['plane_model_id'];

    // Fetch the plane specifications
    $query = "
        SELECT 
            manufacturer, aircraft, cruise_speed, capacity, runway_required, delivery_time, 
            a_check_cost, flight_range, consumption, service_ceiling, co2_emission, maint_check, 
            crew_pilots, crew_crew, crew_engineers, crew_tech, short_name, my_short_name
        FROM plane_models
        WHERE plane_model_id = :plane_model_id
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':plane_model_id', $plane_model_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the result
    $plane_specs = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Return the data as a JSON response
    echo json_encode($plane_specs);
}
?>
