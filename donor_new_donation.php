<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Donation - Giving Sphere Charity</title>
    <link rel="stylesheet" href="donor.css">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMGWTnBSr12/37tmwv4IqPEsFt9kTP5BbbF0pT3" crossorigin="anonymous">
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
                    <li><a href="index.html"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="donor.html"><i class="fas fa-user"></i> Donor</a></li>
                    <li><a href="logout.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="donor-container">
        <aside class="sidebar">
            <ul>
                <li><a href="donor_dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="donor_donations.html"><i class="fas fa-hand-holding-usd"></i> My Donations</a></li>
           <!--     <li><a href="donor_new_donation.php"><i class="fas fa-donate"></i> Make a Donation</a></li>-->
           <li><a href="donor_profile.html"><i class="fas fa-user-cog"></i> Update Profile</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2>Make a Donation</h2>
            <!-- Donation Form Section -->
            <form action="#" method="post">
                <div class="form-group">
                    <label for="charity">Select Charity:</label>
                    <select id="charity" name="charity">
                        <option value="charity1">Charity 1</option>
                        <option value="charity2">Charity 2</option>
                        <!-- More charities -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="number" id="amount" name="amount" required>
                </div>
                <div class="form-group">
                    <button type="submit">Donate Now</button>
                </div>
            </form>
        </main>
    </div>

    <footer>
        <div class="container">
           
        <p>2024 Giving Sphere Charity - Where Compassion Meets Action.</p>
</div>
    </footer>
</body>
</html>
