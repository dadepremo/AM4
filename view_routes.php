<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}?>
<!DOCTYPE html>
<html>
<head>
    <title>AM Routes - <?php echo $_SESSION['airline_name'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="routes.php">Back</a>
        <a href="index.php">Home</a>
    </nav>

</body>
</html>

<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $origin_airport_iata = strtoupper($_GET['origin_airport_iata']) ?? '';
    $plane_model = $_GET['plane_model'] ?? '';
    $limit = 10; // Number of records per page
    $current_page = $_GET['page'] ?? 1; // Current page number
    $offset = ($current_page - 1) * $limit; // Calculate the offset

    try {
        // Count total records
        $count_query = "
            SELECT COUNT(*) as total 
            FROM routes 
            WHERE origin_airport_iata = :origin_airport_iata AND plane_model = :plane_model AND user_id = :user_id
        ";
        $count_stmt = $conn->prepare($count_query);
        $count_stmt->execute([
            ':origin_airport_iata' => $origin_airport_iata,
            ':plane_model' => $plane_model,
            ':user_id' => $_SESSION['user_id']
        ]);
        $total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $total_pages = ceil($total_records / $limit); // Calculate total pages

        // Fetch paginated records
        $query = "
            SELECT * 
            FROM routes 
            WHERE origin_airport_iata = :origin_airport_iata AND plane_model = :plane_model AND user_id = :user_id
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':origin_airport_iata', $origin_airport_iata, PDO::PARAM_STR);
        $stmt->bindValue(':plane_model', $plane_model, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->execute();

        $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display results
        echo "<h1>Routes for Plane Model '$plane_model' and Origin Airport '$origin_airport_iata'</h1>";
        if (count($routes) > 0) {
            echo "<table border='1' style='border-collapse: collapse;' width='100%'>";
            echo "<tr>
                    <th>Status</th>
                    <th>Plane Code</th>
                    <th>Destination Name</th>
                    <th>Destination Country</th>
                    <th>Time</th>
                    <th>Trips per Day</th>
                    <th>Plane Model</th>
                    <th>Origin Airport</th>
                  </tr>";
            foreach ($routes as $route) {
                echo "<tr>
                        <td style=' padding: .5%;'>{$route['status']}</td>
                        <td style=' padding: .5%;'>{$route['plane_code']}</td>
                        <td style=' padding: .5%;'>{$route['dest_name']}</td>
                        <td style=' padding: .5%;'>{$route['dest_country']}</td>
                        <td style=' padding: .5%;'>{$route['time']}</td>
                        <td style=' padding: .5%;'>{$route['trips_pd_pa']}</td>
                        <td style=' padding: .5%;'>{$route['plane_model']}</td>
                        <td style=' padding: .5%;'>{$route['origin_airport_iata']}</td>
                      </tr>";
            }
            echo "</table>";

            // Pagination links
            echo "<div>";
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i == $current_page) ? 'style="font-weight: bold;"' : '';
                echo "<a href='view_routes.php?origin_airport_iata=$origin_airport_iata&plane_model=$plane_model&page=$i' $active>$i</a> ";
            }
            echo "</div>";
        } else {
            echo "<h2>No routes found for the specified criteria.</h2>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
