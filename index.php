<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php';

// Query to fetch dashboard data
$query = "SELECT * FROM my_planes WHERE user_id = :user_id";

$stmt = $conn->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$planes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT * FROM airports WHERE user_id = :user_id";

$stmt = $conn->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$airports = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html>

<head>
    <title>AM 4</title>
    <style>
        /* Add style for the wrapper div to enable horizontal scrolling */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* For smooth scrolling on iOS */
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 2000px;
            /* Ensure table doesn't shrink too much, adjust as necessary */
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        /* Optional: Add hover effect for rows */
        tr:hover {
            background-color: rgb(228, 228, 228);
            cursor: pointer;
        }

        /* Style the navigation bar */
        nav {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding: 1em;
        }

        /* Style the links inside the navigation bar */
        nav a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin-right: 10px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        nav a.logout {
            margin-left: auto;
            /* Pushes this item to the far right */
        }

        /* Style the active or hovered link */
        nav a:hover {
            background-color: #575757;
            color: #fff;
        }

        /* Add a separator line between links */
        nav a:not(:last-child) {
            border-right: 2px solid #444;
        }

        /* Optional: Add a border for the active link */
        nav a.active {
            background-color: #4CAF50;
            color: white;
        }

        /* Mobile responsiveness: Stack nav items on smaller screens */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: stretch;
            }

            nav a {
                text-align: center;
                padding: 12px;
                margin-right: 0;
            }

            nav a:not(:last-child) {
                border-right: none;
            }

            h1 {
                font-size: 28px;
            }
        }

        h1 {
            font-size: 36px;
            color: #333;
            font-weight: bold;
            text-align: center;
            /* Center the heading */
            margin-bottom: 20px;
            /* Add space below the heading */
            padding: 10px 0;
            background-color: #4CAF50;
            /* Add a background color */
            color: white;
            /* Make text white */
            border-radius: 8px;
            /* Rounded corners for a smoother look */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Add subtle shadow */
        }

        .rrrr:hover {
            background-color: rgba(255, 0, 0, 0.5);
        }

        .bbbb:hover {
            background-color: rgba(0, 60, 255, 0.5);
        }

        .vvvv:hover {
            background-color: rgba(21, 255, 0, 0.5);
        }

        .ffff:hover {
            background-color: rgba(255, 0, 247, 0.5);
        }

        .gggg:hover {
            background-color: rgba(255, 247, 0, 0.5);
        }
    </style>
</head>

<body>
    <h1>Airline Management Dashboard - <?php echo strtoupper($_SESSION['airline_name']); ?></h1>
    <nav>
        <a href="routes.php" class="bbbb">Routes</a>
        <a href="view_my_routes.php" class="bbbb">My routes</a>
        <a href="upload_csv.php" class="bbbb">Upload routes from CSV</a>
        <a href="add_airport.php" class="ffff">Add airport</a>
        <a href="add_plane_model.php" class="ffff">Add plane model</a>
        <a href="add_plane.php" class="vvvv">Add plane to the fleet</a>
        <a href="manage_plane.php" class="vvvv">Manage fleet</a>
        <a href="view_planes.php" class="vvvv">View fleet</a>
        <a href="add_flight.php" class="gggg">Add flight</a>
        <a href="view_flights.php" class="gggg">View flights</a>
        <a href="import_flights.php" class="gggg">Import flights</a>
        <a href="logout.php" class="rrrr logout">Logout</a>
    </nav>


    <!-- Table with scrollable content -->
    <br><br>
    <div class="table-wrapper">
        <table style="white-space: nowrap;">
            <thead>
                <tr>
                    <?php
                    $q = "SELECT count(plane_code) AS counter FROM my_planes WHERE user_id = :user_id;";
                    $st = $conn->prepare($q);
                    $st->execute([':user_id' => $_SESSION['user_id']]);
                    $result = $st->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <td><?php echo $result[0]['counter']; ?> / 30</td>
                    <td colspan="5"></td>
                    <?php
                    $q = "SELECT sum(num_of_flights) as sum FROM my_planes WHERE user_id = :user_id;";
                    $st = $conn->prepare($q);
                    $st->execute([':user_id' => $_SESSION['user_id']]);
                    $result = $st->fetchAll(PDO::FETCH_ASSOC);
                    $tot_flights = $result[0]['sum'];

                    $total_total_total = 0;
                    $total_total_quotas = 0;
                    $total_total_fuel = 0;
                    foreach ($planes as $plane):
                        $query = "SELECT * FROM flights WHERE user_id = :user_id AND plane_code = :plane_code ORDER BY flight_id DESC LIMIT 1";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([':user_id' => $_SESSION['user_id'], ':plane_code' => $plane['plane_code']]);
                        $flight = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($flight) {
                            if (isset($flight['profit'])) {
                                $total_total_total = $total_total_total + $flight['profit'];
                                $total_total_fuel = $total_total_fuel + $flight['fuel'] * 1000;
                                $total_total_quotas = $total_total_quotas + $flight['route_quotas'] * 1000;
                            } else {
                                if (!empty($flight['profit'])) {
                                    echo "Profit: " . $flight['profit'];
                                } else {
                                    echo "Profit data is missing or not set.";
                                }
                            }
                        } else {
                        }
                    endforeach;
                    ?>
                    <td style="background-color: rgba(65, 198, 160, 0.31);"><?php echo $result[0]['sum']; ?></td>
                    <td style="background-color: rgba(65, 198, 65, 0.31);"><?php echo "$ " . number_format($total_total_total, 2); ?></td>
                    <?php
                    $q = "SELECT sum(profit) as sum, sum(route_quotas) as rsum, sum(fuel) as fsum FROM flights WHERE user_id = :user_id";
                    $st = $conn->prepare($q);
                    $st->execute([':user_id' => $_SESSION['user_id']]);
                    $result = $st->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <td style="background-color: rgba(65, 198, 65, 0.31);"><?php echo "$ " . number_format($result[0]['sum']) ?></td>
                    <td style="background-color: rgba(65, 198, 65, 0.31);"><?php echo "$ " . number_format($result[0]['sum'] / $tot_flights, 2) ?></td>
                    <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($total_total_quotas, 2) ?></td>
                    <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($result[0]['rsum'] * 1000, 2) ?></td>
                    <td></td>
                    <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($total_total_fuel, 2) ?> L</td>
                    <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($result[0]['fsum'] * 1000, 2) ?> L</td>
                    <td></td>
                    <?php
                    $q = "SELECT sum(cfg_y) as y, sum(cfg_j) as j, sum(cfg_f) as f FROM my_planes as m INNER JOIN routes as r ON m.plane_code = r.plane_code WHERE m.user_id = :user_id";
                    $st = $conn->prepare($q);
                    $st->execute([':user_id' => $_SESSION['user_id']]);
                    $result = $st->fetchAll(PDO::FETCH_ASSOC);
                    $tttttooottt = $result[0]['y'] + $result[0]['j'] + $result[0]['f'];
                    ?>
                    <td style="background-color: rgba(151, 65, 198, 0.31);"><?php echo htmlspecialchars($result[0]['y']) ?></td>
                    <td style="background-color: rgba(151, 65, 198, 0.31);"><?php echo htmlspecialchars($result[0]['j']) ?></td>
                    <td style="background-color: rgba(151, 65, 198, 0.31);"><?php echo htmlspecialchars($result[0]['f']) ?></td>
                    <td style="background-color: rgba(151, 65, 198, 0.31);"><?php echo htmlspecialchars($tttttooottt) ?></td>
                    <td colspan="11"></td>
                </tr>
                <tr>
                    <th>Plane Code</th>
                    <th>Manufacturer</th>
                    <th>Model</th>
                    <th>Hub Location</th>
                    <th>Hub ICAO/IATA</th>
                    <th>Route</th>
                    <th>Num of Flights</th>
                    <th>Last Flight Income</th>
                    <th>All Time Income</th>
                    <th>Avg Income</th>
                    <th>Last Flight Quotas</th>
                    <th>All Time Quotas</th>
                    <th>Avg Quotas</th>
                    <th>Last Flight Fuel</th>
                    <th>All Time Fuel</th>
                    <th>Avg Fuel</th>
                    <th>Economy Seats</th>
                    <th>Business Seats</th>
                    <th>First Seats</th>
                    <th>Total Seats</th>
                    <th>Seats Pricing</th>
                    <th>Economy Price</th>
                    <th>Business Price</th>
                    <th>First Price</th>
                    <th>Destination</th>
                    <th>Dest ICAO/IATA</th>
                    <th>Stop</th>
                    <th>Stop ICAO/IATA</th>
                    <th>Maint Check</th>
                    <th>Maintenance In</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

                <?php
                foreach ($planes as $plane):
                    $search_iata_code = $plane['hub'];

                    // Search for the IATA code in the $airports array
                    $filtered_airports = array_filter($airports, function ($airport) use ($search_iata_code) {
                        return $airport['iata_code'] === $search_iata_code;
                    });

                    // Check if we found the airport
                    if (!empty($filtered_airports)) {
                        foreach ($filtered_airports as $airport) {
                            $icaoiata = $airport['icao_code'] . " / " . $airport['iata_code'];
                            $location = $airport['location'] . ", " . $airport['country'];
                        }
                    } else {
                        echo "No airport found with IATA code: $search_iata_code";
                    }

                    $query = "SELECT * FROM flights WHERE user_id = :user_id AND plane_code = :plane_code ORDER BY flight_id DESC LIMIT 1";

                    $stmt = $conn->prepare($query);
                    $stmt->execute([':user_id' => $_SESSION['user_id'], ':plane_code' => $plane['plane_code']]);
                    $flight = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($flight) {
                        $found = true;
                        if (isset($flight['profit'])) {
                        } else {
                            if (!empty($flight['profit'])) {
                                echo "Profit: " . $flight['profit'];
                            } else {
                                echo "Profit data is missing or not set.";
                            }
                        }
                    } else {
                        $found = false;
                    }
                    if ($found) {
                        $query = "SELECT SUM(profit) AS total_profit, SUM(route_quotas) AS total_quotas, SUM(fuel) AS total_fuel
                            FROM flights 
                            WHERE user_id = :user_id AND plane_code = :plane_code";

                        $stmt = $conn->prepare($query);
                        $stmt->execute([
                            ':user_id' => $_SESSION['user_id'],
                            ':plane_code' => $plane['plane_code']
                        ]);

                        $sum = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($sum && isset($sum['total_profit'])) {
                            $sum_profit = $sum['total_profit'];
                        } else {
                            echo "No profit data available.";
                        }

                        if ($sum && isset($sum['total_quotas'])) {
                            $total_quotas = $sum['total_quotas'];
                            $total_quotas = $total_quotas * 1000;
                        } else {
                            echo "No quotas data available.";
                        }

                        if ($sum && isset($sum['total_fuel'])) {
                            $total_fuel = $sum['total_fuel'];
                            $total_fuel = $total_fuel * 1000;
                        } else {
                            echo "No fuel data available.";
                        }

                        $query = "SELECT * FROM routes as r inner join my_routes as my on r.route_id = my.imported_route_id inner join my_planes as p on my.plane_code = p.plane_code where p.user_id = :user_id and p.plane_code = :plane_code";

                        $stmt = $conn->prepare($query);
                        $stmt->execute([
                            ':user_id' => $_SESSION['user_id'],
                            ':plane_code' => $plane['plane_code']
                        ]);

                        $routejoin = $stmt->fetch(PDO::FETCH_ASSOC);

                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plane['plane_code']); ?></td>
                            <td><?php echo htmlspecialchars($plane['manufacturer']); ?></td>
                            <td><?php echo htmlspecialchars($plane['aircraft']); ?></td>
                            <td style="background-color: rgba(65, 198, 160, 0.31);"><?php echo htmlspecialchars($location); ?></td>
                            <td style="background-color: rgba(65, 198, 160, 0.31);"><?php echo htmlspecialchars($icaoiata); ?></td>
                            <td style="background-color: rgba(65, 198, 160, 0.31);"><?php echo htmlspecialchars($plane['flight_route']); ?></td>
                            <td style="background-color: rgba(65, 198, 160, 0.31);"><?php echo htmlspecialchars($plane['num_of_flights']); ?></td>
                            <td style="background-color: rgba(65, 198, 65, 0.31);">$<?php echo number_format($flight['profit'], 2); ?></td>
                            <td style="background-color: rgba(65, 198, 65, 0.31);">$<?php echo number_format($sum_profit, 2); ?></td>
                            <td style="background-color: rgba(65, 198, 65, 0.31);">$<?php echo number_format($sum_profit / $plane['num_of_flights'], 2); ?></td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($flight['route_quotas'] * 1000, 2); ?></td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($total_quotas, 2); ?></td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($total_quotas / $plane['num_of_flights'], 2); ?></td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($flight['fuel'] * 1000, 2); ?> L</td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($total_fuel, 2); ?> L</td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);"><?php echo number_format($total_fuel / $plane['num_of_flights'], 2); ?> L</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);"><?php echo htmlspecialchars($routejoin['cfg_y']); ?></td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);"><?php echo htmlspecialchars($routejoin['cfg_j']); ?></td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);"><?php echo htmlspecialchars($routejoin['cfg_f']); ?></td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);"><?php echo htmlspecialchars($routejoin['cfg_y'] + $routejoin['cfg_j'] + $routejoin['cfg_f']); ?></td>
                            <form method="POST" action="process_selection.php?plane_code=<?php echo $plane['plane_code']; ?>">
                                <td style="background-color: rgba(151, 65, 198, 0.31);">
                                    <select name="seats_pricing">
                                        <option value="manually" <?php echo $routejoin['seat_pricing'] === 'manually' ? 'selected' : ''; ?>>Manually</option>
                                        <option value="csv" <?php echo $routejoin['seat_pricing'] === 'csv' ? 'selected' : ''; ?>>CSV</option>
                                        <option value="optimized" <?php echo $routejoin['seat_pricing'] === 'optimized' ? 'selected' : ''; ?>>Optimized</option>
                                        <option value="auto" <?php echo $routejoin['seat_pricing'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
                                    </select>
                                    <button type="submit" name="update_seats_pricing">Update</button>
                                </td>
                            </form>

                            <td style="background-color: rgba(151, 65, 198, 0.31);">
                                <?php
                                if ($routejoin['seat_pricing'] == 'csv') {
                                    echo "$ " . number_format($routejoin['tkt_y'], 2);
                                } else if ($routejoin['seat_pricing'] == 'auto') {
                                    echo "auto";
                                } else if ($routejoin['seat_pricing'] == 'manually') {
                                    echo "manual";
                                } else if ($routejoin['seat_pricing'] == 'optimized') {
                                    echo "optimized";
                                } else {
                                    echo "not set";
                                }
                                ?>
                            </td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">
                                <?php
                                if ($routejoin['seat_pricing'] == 'csv') {
                                    echo "$ " . number_format($routejoin['tkt_j'], 2);
                                } else if ($routejoin['seat_pricing'] == 'auto') {
                                    echo "auto";
                                } else if ($routejoin['seat_pricing'] == 'manually') {
                                    echo "manual";
                                } else if ($routejoin['seat_pricing'] == 'optimized') {
                                    echo "optimized";
                                } else {
                                    echo "not set";
                                }
                                ?>
                            </td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">
                                <?php
                                if ($routejoin['seat_pricing'] == 'csv') {
                                    echo "$ " . number_format($routejoin['tkt_f'], 2);
                                } else if ($routejoin['seat_pricing'] == 'auto') {
                                    echo "auto";
                                } else if ($routejoin['seat_pricing'] == 'manually') {
                                    echo "manual";
                                } else if ($routejoin['seat_pricing'] == 'optimized') {
                                    echo "optimized";
                                } else {
                                    echo "not set";
                                }
                                ?>
                            </td>



                            <td style="background-color: rgba(65, 100, 198, 0.31);"><?php echo htmlspecialchars($routejoin['dest_name'] . ", " . $routejoin['dest_country']); ?></td>
                            <td style="background-color: rgba(65, 100, 198, 0.31);"><?php echo htmlspecialchars($routejoin['dest_icao'] . " / " . $routejoin['dest_iata']); ?></td>
                            <td style="background-color: rgba(65, 100, 198, 0.31);"><?php echo htmlspecialchars($routejoin['stop_name'] . ", " . $routejoin['stop_country']); ?></td>
                            <td style="background-color: rgba(65, 100, 198, 0.31);"><?php echo htmlspecialchars($routejoin['stop_icao'] . " / " . $routejoin['stop_iata']); ?></td>
                            <td><?php echo htmlspecialchars($routejoin['maint_check'] . " hrs"); ?></td>
                            <td><?php echo htmlspecialchars($routejoin['hours_to_check'] . " hrs"); ?></td>
                            <td><?php echo htmlspecialchars($routejoin['status']); ?></td>
                        </tr><?php
                            } else { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plane['plane_code']); ?></td>
                            <td><?php echo htmlspecialchars($plane['manufacturer']); ?></td>
                            <td><?php echo htmlspecialchars($plane['aircraft']); ?></td>
                            <td style="background-color: rgba(65, 198, 160, 0.31);"><?php echo htmlspecialchars($location); ?></td>
                            <td style="background-color: rgba(65, 198, 160, 0.31);"><?php echo htmlspecialchars($icaoiata); ?></td>
                            <td style="background-color: rgba(65, 198, 160, 0.31);"><?php echo htmlspecialchars($plane['flight_route']); ?></td>
                            <td style="background-color: rgba(65, 198, 160, 0.31);">N/A</td>
                            <td style="background-color: rgba(65, 198, 65, 0.31);">$ N/A</td>
                            <td style="background-color: rgba(65, 198, 65, 0.31);">$ N/A</td>
                            <td style="background-color: rgba(65, 198, 65, 0.31);">$ N/A</td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);">N/A</td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);">N/A</td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);">N/A</td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);">N/A L</td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);">N/A L</td>
                            <td style="background-color: rgba(198, 191, 65, 0.31);">N/A L</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(151, 65, 198, 0.31);">N/A</td>
                            
                            <td style="background-color: rgba(65, 100, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(65, 100, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(65, 100, 198, 0.31);">N/A</td>
                            <td style="background-color: rgba(65, 100, 198, 0.31);">N/A</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>Pending</td>
                        </tr>
                <?php }

                        endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>