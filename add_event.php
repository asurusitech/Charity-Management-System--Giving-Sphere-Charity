<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];

    // Insert new event into the database
    $insert_query = $database->prepare("INSERT INTO events (event_name, event_date, event_location, created_at, updated_at) VALUES (?, ?, ?, current_timestamp(), current_timestamp())");
    $insert_query->bind_param("sss", $event_name, $event_date, $event_location);

    if ($insert_query->execute()) {
        // Redirect to events list after successful insertion
        header("Location: admin_events.php");
        exit();
    } else {
        echo "Error adding event: " . $database->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        /* Form Styling */
        .update-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .update-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .update-form label {
            display: block;
            margin-bottom: 8px;
        }

        .update-form input[type="text"],
        .update-form input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .update-form button[type="submit"] {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .update-form button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo-title">
                <img src="logo.png" alt="Logo" class="logo">
                <h1>Giving Sphere Charity</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="admin.php">Admin</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
        <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li ><a href="admin_users.php">Donors</a></li>
                <li><a href="admin_beneficiaries.php">Beneficiaries</a></li>
                <li><a href="viewdonations.php">Donations</a></li>
                <li class="active"><a href="admin_events.php">Events</a></li>
                <li>
                    <a href="#" class="sub-link-toggle">Reports</a>
                    <ul class="sub-menu">
                        <li><a href="donatedusers.php">Users who have donated</a></li>
                        <li><a href="#">Donations Report</a></li>
                        
                    </ul>
                </li>
          
          
            </ul>
           </aside>
        <main class="main-content">
            <div class="update-form">
                <h2>Add New Event</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <label for="event_name">Event Name:</label>
                    <input type="text" id="event_name" name="event_name" required>

                    <label for="event_date">Event Date:</label>
                    <input type="date" id="event_date" name="event_date" required>

                    <label for="event_location">Event Location:</label>
                    <input type="text" id="event_location" name="event_location">

                    <button type="submit">Add Event</button>
                </form>
            </div>
        </main>
    </div>

    <footer>
        <div class="container">
            <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
            <a href="https://wa.me/254707962238" class="fa fa-whatsapp" style="font-size: 28px; text-decoration: none;"></a>
        </div>
    </footer>
</body>
</html>
