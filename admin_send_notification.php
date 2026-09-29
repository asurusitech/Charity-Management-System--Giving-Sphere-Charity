<?php
session_start();
include 'connection.php';

// Fetch all users who have donated
$donors_query = $database->query("SELECT donorid, first_name, last_name, donemail FROM donors WHERE EXISTS (SELECT 1 FROM donations WHERE donations.donorid = donors.donorid)");
$donors = $donors_query->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $selected_donors = $_POST['donors'];

    // Prepare and execute notification insertion
    $query = $database->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, current_timestamp())");

    foreach ($selected_donors as $donor_id) {
        $query->bind_param("is", $donor_id, $message);
        $query->execute();
    }

    echo "Notifications sent successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification - Giving Sphere Charity</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        /* Form Styling */
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #FDF6EC; /* Off-white */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container form {
            display: grid;
            gap: 15px;
        }

        .form-container label {
            font-weight: bold;
        }

        .form-container textarea {
            padding: 8px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ddd;
            height: 100px;
        }

        .form-container select {
            padding: 8px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .form-container input[type=submit] {
            background-color: #4CAF50; /* Green */
            color: #FFF;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .form-container input[type=submit]:hover {
            background-color: #45a049; /* Darker green */
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

    <div class="form-container">
        <h2>Send Notification</h2>
        <form method="POST" action="">
            <div>
                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <div>
                <label for="donors">Select Donors:</label>
                <select id="donors" name="donors[]" multiple required>
                    <?php foreach ($donors as $donor): ?>
                        <option value="<?php echo $donor['donorid']; ?>"><?php echo $donor['first_name'] . ' ' . $donor['last_name'] . ' (' . $donor['donemail'] . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <input type="submit" value="Send Notification">
            </div>
        </form>
    </div>

    <footer>
        <div class="container">
            <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
            <a href="https://wa.me/254707962238" class="fa fa-whatsapp" style="font-size: 28px; text-decoration: none;"></a>
        </div>
    </footer>
</body>
</html>
