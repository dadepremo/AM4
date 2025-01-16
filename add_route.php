<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Fetch planes from the user's fleet
$queryPlanes = "
    SELECT * 
    FROM my_planes 
    WHERE user_id = :user_id
";
$stmtPlanes = $conn->prepare($queryPlanes);
$stmtPlanes->execute([':user_id' => $_SESSION['user_id']]);
$planes = $stmtPlanes->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['clear_filter'])) {
    // Remove any GET parameters (clear filters)
    unset($_GET['origin_airport_iata']);
    unset($_GET['plane_model']);
    header("Location: add_route.php"); // Reload the page without filters
    exit;
}

// Fetch available routes from the 'routes' table with status = 'none' and plane_code = 'none'
if(isset($_GET['origin_airport_iata']) && !isset($_GET['plane_model'])) {
    echo "<h3>" . $_GET['origin_airport_iata'] . "</h3>";
    $queryRoutes = "
        SELECT * 
        FROM routes 
        WHERE user_id = :user_id AND status = 'none' AND plane_code = 'none' AND origin_airport_iata = :origin_airport_iata
    ";
    $stmtRoutes = $conn->prepare($queryRoutes);
    $stmtRoutes->execute([':user_id' => $_SESSION['user_id'], ':origin_airport_iata' => $_GET['origin_airport_iata']]);
    $routes = $stmtRoutes->fetchAll(PDO::FETCH_ASSOC);
}
else if(!isset($_GET['origin_airport_iata']) && isset($_GET['plane_model'])) {
    echo "<h3>" . $_GET['plane_model'] . "</h3>";
    $queryRoutes = "
        SELECT * 
        FROM routes 
        WHERE user_id = :user_id AND status = 'none' AND plane_code = 'none' AND plane_model = :plane_model
    ";
    $stmtRoutes = $conn->prepare($queryRoutes);
    $stmtRoutes->execute([':user_id' => $_SESSION['user_id'], ':plane_model' => $_GET['plane_model']]);
    $routes = $stmtRoutes->fetchAll(PDO::FETCH_ASSOC);
}
else if(isset($_GET['origin_airport_iata']) && isset($_GET['plane_model'])) {
    echo "<h3>" . $_GET['origin_airport_iata'] . "</h3>";
    echo "<h3>" . $_GET['plane_model'] . "</h3>";
    $queryRoutes = "
        SELECT * 
        FROM routes 
        WHERE user_id = :user_id AND status = 'none' AND plane_code = 'none' AND plane_model = :plane_model AND origin_airport_iata = :origin_airport_iata
    ";
    $stmtRoutes = $conn->prepare($queryRoutes);
    $stmtRoutes->execute([':user_id' => $_SESSION['user_id'], ':plane_model' => $_GET['plane_model'], ':origin_airport_iata' => $_GET['origin_airport_iata']]);
    $routes = $stmtRoutes->fetchAll(PDO::FETCH_ASSOC);
}
else {
    $queryRoutes = "
        SELECT * 
        FROM routes 
        WHERE user_id = :user_id AND status = 'none' AND plane_code = 'none'
    ";
    $stmtRoutes = $conn->prepare($queryRoutes);
    $stmtRoutes->execute([':user_id' => $_SESSION['user_id']]);
    $routes = $stmtRoutes->fetchAll(PDO::FETCH_ASSOC);
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imported_route_id = $_POST['route_id'];
    $route_code = $_POST['route_code'];
    $notes = $_POST['notes'];
    $plane_code = $_POST['plane_code'];
    $user_id = $_SESSION['user_id'];
    $status = 'Active'; // Default status

    $queryInsert = "
        INSERT INTO my_routes (imported_route_id, route_code, notes, user_id, status, created_at, updated_at, plane_code)
        VALUES (:imported_route_id, :route_code, :notes, :user_id, :status, NOW(), NOW(), :plane_code)
    ";
    								
    $stmtInsert = $conn->prepare($queryInsert);
    $stmtInsert->execute([
        ':imported_route_id' => $imported_route_id,
        ':route_code' => $route_code,
        ':notes' => $notes,
        ':user_id' => $user_id,
        ':status' => $status,
        ':plane_code' => $plane_code
    ]);

    $queryUpdate = "
        UPDATE my_planes SET flight_route = :route_code WHERE user_id = :user_id AND plane_code = :plane_code
    ";

    $stmtUpdate = $conn->prepare($queryUpdate);
    $stmtUpdate->execute([
        ':route_code' => $route_code,
        ':user_id' => $user_id,
        ':plane_code' => $plane_code
    ]);

    $queryUpdate = "
        UPDATE routes SET status = :status, plane_code = :plane_code WHERE user_id = :user_id AND route_id = :imported_route_id
    ";

    $stmtUpdate = $conn->prepare($queryUpdate);
    $stmtUpdate->execute([
        ':status' => $status,
        ':user_id' => $user_id,
        ':plane_code' => $plane_code,
        ':imported_route_id' => $imported_route_id
    ]);
    
    header("Location: add_route.php");
    echo "Route added successfully!";
    
}



?>

<style>
    /* Body Styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
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
    nav {
        text-align: center;
        margin: 20px 0;
    }

    nav a {
        font-size: 16px;
        margin: 0 15px;
        text-decoration: none;
        color: #007bff;
        padding: 8px 15px;
        border-radius: 4px;
    }

    nav a:hover {
        background-color: #f2f2f2;
        color: #0056b3;
    }

    /* Form Styling */
    form {
        width: 60%;
        margin: 0 auto;
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

    select, input[type="text"], textarea {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
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

    #clr_button {
        background-color:rgb(175, 76, 76);
        color: white;
        padding: 12px 20px;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 20%;
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

        nav a {
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
    <title>Add New Route</title>
</head>
<body>

    <h1>Add New Route</h1>

    <nav>
        <a href="view_my_routes.php">Back to Routes</a>
    </nav>

    <form method="GET" action="add_route.php">
    <label for="origin_airport_iata">Origin Airport IATA:</label>
    <select name="origin_airport_iata">
        <option value="">Select Airport</option>
        <?php
        // Fetch available airports from routes table
        $airport_query = "SELECT DISTINCT a.location, a.icao_code, a.iata_code 
            FROM routes AS r
            INNER JOIN airports AS a ON r.origin_airport_iata = a.iata_code
            WHERE r.user_id = :user_id;
        ";
        $airport_stmt = $conn->prepare($airport_query);
        $airport_stmt->execute([':user_id' => $_SESSION['user_id']]);
        $airports = $airport_stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($airports as $airport) {
            echo "<option value='" . htmlspecialchars($airport['iata_code']) . "'>" . htmlspecialchars($airport['location'] . " [" . $airport['icao_code'] . " / " . $airport['iata_code'] . "]") . "</option>";
        }
        ?>
    </select><br>

    <label for="plane_model">Plane Model:</label>
    <select name="plane_model">
        <option value="">Select Plane Model</option>
        <?php
        // Fetch available plane models
        $plane_query = "SELECT DISTINCT aircraft, manufacturer, short_name FROM my_planes WHERE user_id = :user_id";
        $plane_stmt = $conn->prepare($plane_query);
        $plane_stmt->execute([':user_id' => $_SESSION['user_id']]);
        $planes = $plane_stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($planes as $plane) {
            echo "<option value='" . htmlspecialchars($plane['short_name']) . "'>" . htmlspecialchars($plane['manufacturer'] . " " . $plane['aircraft'] . " (" . $plane['short_name'] . ")") . "</option>";
        }
        ?>
    </select><br>

    <button type="submit" name="filter">Filter</button><br><br>
    <button type="submit" name="clear_filter" id="clr_button">Clear Filter</button>
</form><br><br>


    <form action="" method="POST">
        <label for="route_id">Select a Route:</label>
        <select name="route_id" id="route_id" required>
            <option value="">-- Select Route --</option>
            <?php foreach ($routes as $route): ?>
                <option value="<?php echo $route['route_id']; ?>">
                    <?php echo htmlspecialchars(" [" . $route['route_id'] . "] " .  $route['stop_country'] . ", " . $route['stop_name'] . " - " . $route['dest_country'] . ", " . $route['dest_name']  . " (" . $route['origin_airport_iata'] . "-" . $route['dest_iata'] . ")"); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="plane_code">Select a Plane:</label>
        <select name="plane_code" id="plane_code" required>
            <option value="">-- Select Plane --</option>
            <?php
        // Fetch available plane models
        $plane_query = "SELECT aircraft, manufacturer, short_name, plane_code FROM my_planes WHERE user_id = :user_id AND (flight_route IS NULL OR flight_route = '')";
        $plane_stmt = $conn->prepare($plane_query);
        $plane_stmt->execute([':user_id' => $_SESSION['user_id']]);
        $planes = $plane_stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach ($planes as $plane): ?>
                <option value="<?php echo $plane['plane_code']; ?>">
                    <?php echo htmlspecialchars($plane['plane_code'] . " (" . $plane['manufacturer'] . " " . $plane['aircraft'] . ")"); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="route_code">Route Code:</label>
        <input type="text" name="route_code" id="route_code" required><br>

        <label for="notes">Notes:</label>
        <textarea name="notes" id="notes" rows="4" cols="50"></textarea><br>

        <button type="submit">Add Route</button>
    </form>

</body>
</html>
