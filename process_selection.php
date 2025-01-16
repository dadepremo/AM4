<?php
include 'database.php'; // Include your database connection

// Check if the form has been submitted
if (isset($_POST['update_seats_pricing'])) {
    // Sanitize the input (ensure no malicious input)
    $seats_pricing = $_POST['seats_pricing']; 
    
    // Ensure that the value is valid (you can further validate as needed)
    if (!in_array($seats_pricing, ['manually', 'csv', 'optimized', 'auto'])) {
        die('Invalid value selected.');
    }

    // Get the ID of the row you want to update (e.g., from a hidden field, session, or query string)
    $plane_code = $_GET['plane_code'];  // Ensure you pass this in the form or set it via session or query params
    
    // Prepare the SQL query to update the selected pricing type
    $query = "UPDATE my_planes SET seat_pricing = :seats_pricing WHERE plane_code = :plane_code";

    // Prepare the statement
    $stmt = $conn->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':seats_pricing', $seats_pricing);
    $stmt->bindParam(':plane_code', $plane_code);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect or display success message
        header("Location: index.php");  // Redirect to the routes page or any other page
        exit;
    } else {
        echo "Error updating the record.";
    }
}
?>
