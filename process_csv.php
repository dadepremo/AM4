<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Process CSV</title>
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
    </nav>
</body>
</html>
<?php
include 'database.php';

// File upload and processing logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
    $origin_airport_iata = $_POST['origin_airport_iata'];
    $plane_model = $_POST['plane_model'];

    try {
        // Skip the header row
        fgetcsv($csvFile);

        $count = 0; // Counter for rows imported

        while (($row = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
            // Insert the row
            $insertQuery = "
                INSERT INTO routes ( status, plane_code,
                    dest_id, dest_name, dest_country, dest_iata, dest_icao,
                    stop_id, stop_name, stop_country, stop_iata, stop_icao,
                    full_dist, dem_y, dem_j, dem_f, cfg_y, cfg_j, cfg_f, tkt_y, tkt_j, tkt_f,
                    direct_dist, time, trips_pd_pa, num_ac, income, fuel, co2, chk_cost, repair_cost, profit_pt, ci, contrib_pt, plane_model, origin_airport_iata, user_id
                ) VALUES ( :status, :plane_code,
                    :dest_id, :dest_name, :dest_country, :dest_iata, :dest_icao,
                    :stop_id, :stop_name, :stop_country, :stop_iata, :stop_icao,
                    :full_dist, :dem_y, :dem_j, :dem_f, :cfg_y, :cfg_j, :cfg_f, :tkt_y, :tkt_j, :tkt_f,
                    :direct_dist, :time, :trips_pd_pa, :num_ac, :income, :fuel, :co2, :chk_cost, :repair_cost, :profit_pt, :ci, :contrib_pt, :plane_model, :origin_airport_iata, :user_id
                )
            ";
            $stmt = $conn->prepare($insertQuery);
            $stmt->execute([
                ':status' => 'none',
                ':plane_code' => 'none',
                ':dest_id' => $row[0],
                ':dest_name' => $row[1],
                ':dest_country' => $row[2],
                ':dest_iata' => $row[3],
                ':dest_icao' => $row[4],
                ':stop_id' => $row[5],
                ':stop_name' => $row[6],
                ':stop_country' => $row[7],
                ':stop_iata' => $row[8],
                ':stop_icao' => $row[9],
                ':full_dist' => $row[10],
                ':dem_y' => $row[11],
                ':dem_j' => $row[12],
                ':dem_f' => $row[13],
                ':cfg_y' => $row[14],
                ':cfg_j' => $row[15],
                ':cfg_f' => $row[16],
                ':tkt_y' => $row[17],
                ':tkt_j' => $row[18],
                ':tkt_f' => $row[19],
                ':direct_dist' => $row[20],
                ':time' => $row[21],
                ':trips_pd_pa' => $row[22],
                ':num_ac' => $row[23],
                ':income' => $row[24],
                ':fuel' => $row[25],
                ':co2' => $row[26],
                ':chk_cost' => $row[27],
                ':repair_cost' => $row[28],
                ':profit_pt' => $row[29],
                ':ci' => $row[30],
                ':contrib_pt' => $row[31],
                ':plane_model' => $plane_model,
                ':origin_airport_iata' => strtoupper($origin_airport_iata),
                ':user_id' => $_SESSION['user_id']
            ]);

            $count++;
        }

        echo "$count rows successfully imported.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        fclose($csvFile);
    }
}
?>
