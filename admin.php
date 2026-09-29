<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Charity Management</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
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
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="admin.php"><i class="fas fa-user-shield"></i> Admin</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
            <ul>
                <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt" style="font-size: 24px;"></i> Dashboard</a></li>
                <li><a href="admin_users.php"><i class="fas fa-users " style="font-size: 24px;"></i> Manage Users</a></li>
                <li><a href="admin_reports.php"><i class="fas fa-chart-line" style="font-size: 2 4px;"></i> Reports</a></li>
                <li><a href="admin_settings.php"><i class="fas fa-cogs" style="font-size: 24px;"></i> Settings</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2>Dashboard</h2>
            <p>Welcome to the Admin Dashboard. Here you can manage the overview of the system.</p>
        </main>
    </div>

    <footer>
        <div class="container">
                <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
</div>
    </footer>
</body>
</html>
