<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'database.php'; // Include your database connection file

// Fetch routes from the database
$query = "SELECT route_id, imported_route_id, status, plane_code, route_code, notes, user_id, created_at, updated_at FROM my_routes WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$routes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Body Styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
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

    /* Table Styling */
    table {
        width: 90%;
        margin: 0 auto;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        color: #333;
        font-size: 16px;
    }

    td {
        font-size: 14px;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    /* No Routes Message Styling */
    td[colspan="8"] {
        text-align: center;
        font-style: italic;
        color: #888;
    }

    /* Responsive Table for Mobile Devices */
    @media (max-width: 768px) {
        table {
            width: 100%;
            font-size: 12px;
        }

        th, td {
            padding: 8px;
        }

        h1 {
            font-size: 28px;
        }

        nav {
            font-size: 14px;
        }

        nav a {
            margin: 0 10px;
            padding: 6px 12px;
        }
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Routes</title>
</head>
<body>
    <h1>Your Routes</h1>

    <nav>
        <a href="add_route.php">Add New Route</a>
        <a href="index.php">Home</a>
    </nav>

    <table>
        <thead>
            <tr>
                <th>Route ID</th>
                <th>Imported Route ID</th>
                <th>Plane Code</th>
                <th>Route Code</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($routes) > 0): ?>
                <?php foreach ($routes as $route): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($route['route_id']); ?></td>
                        <td><?php echo htmlspecialchars($route['imported_route_id']); ?></td>
                        <td><?php echo htmlspecialchars($route['plane_code']); ?></td>
                        <td><?php echo htmlspecialchars($route['route_code']); ?></td>
                        <td><?php echo htmlspecialchars($route['status']); ?></td>
                        <td><?php echo htmlspecialchars($route['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($route['updated_at']); ?></td>
                        <td><?php echo htmlspecialchars($route['notes']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No routes found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
