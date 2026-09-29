<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <title>Giving Sphere Charity</title>
    <style>
        :root {
            --night: #0B0B0Bff;
            --black: #030202ff;
            --eerie-black: #1A1817ff;
            --almond: #E9D8C9ff;
            --brown-sugar: #B06D4Aff;
            --warm-light-brown: #DAB88B;
            --light-cream: #F3E9DD;
            --off-white: #FDF6EC;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow-x: hidden;
            font-family: Arial, sans-serif;
            background: url('back1.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--off-white);
        }

        .header {
            position: absolute;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
            color: #B06D4Aff;
            background: rgba(11, 11, 11, 0.8);
        }

        .header img {
            border-radius: 50%;
            height: 80px;
            width: 80px;
        }

        .header .title {
            font-size: 40px;
            margin-left: 10px;
            position: absolute;
            margin-top: 17px;
        }

        .nav-links {
            display: flex;
        }

        .nav-links a {
            margin-left: 20px;
            text-decoration: none;
            color: var(--off-white);
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .nav-links a:first-child {
            background: none;
            border: 1px solid var(--off-white);
        }

        .nav-links a:last-child {
            background: var(--brown-sugar);
            color: var(--off-white);
        }

        .nav-links a:hover {
            background: var(--warm-light-brown);
            color: var(--black);
        }

        .donate-button {
            padding: 20px 40px;
            background: var(--brown-sugar);
            color: var(--off-white);
            border: none;
            font-size: 24px;
            cursor: pointer;
            border-radius: 5px;
            text-transform: uppercase;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .donate-button a {
            text-decoration: none;
            color: var(--off-white);
        }

        .donate-button a:hover {
            background: var(--warm-light-brown);
            color: var(--black);
        }

        .donate-button:hover {
            background: var(--warm-light-brown);
            color: var(--black);
        }

        .stats {
            position: absolute;
            bottom: 0px;
            width: 100%;
            display: flex;
            justify-content: space-around;
            background: rgba(11, 11, 11, 0.8);
            padding: 10px 0;
            font-size: 18px;
            box-sizing: border-box;
        }

        .stat {
            text-align: center;
        }

        .stat h3 {
            margin: 0;
            font-size: 24px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header .nav-links {
                flex-direction: column;
                 }

            .header .title {
                font-size: 18px;
                margin-top: 60;
                position: absolute;

            }
            .header .img {
                font-size: 24px;
                margin-left: 60;
                position: relative;
                float: left;

            }

            .nav-links {
                flex-direction: column;
                margin-top: 20px;
            }

            .nav-links a {
                margin: 5px 0;
                font-size: 16px;
                
            }

            .donate-button {
                padding: 15px 30px;
                font-size: 20px;
                margin-top: 15px;
            }

           
        }

        @media (max-width: 480px) {
            .header img {
                height: 60px;
                width: 60px;
                position:absolute;
                margin-left: -30px;
            }

            .header .title {
                font-size: 20px;
                margin-top: 100px;
                position:relative;

            }

            .nav-links a {
                font-size: 14px;
            }

            .donate-button {
                padding: 10px 20px;
                font-size: 18px;
            }

             }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-title">
            <a href="admin_dashboard.php"><img src="logo.png" alt="Logo"></a>
            <span class="title">Giving Sphere Charity</span>
        </div>
        <div class="nav-links">
            <a href="signup.php">Become a Member</a>
            <a href="aboutus.html" class="donate-link">About Us</a>
        </div>
    </div>

    <button class="donate-button"><a href="login.php">Donate Now</a></button>

    <div class="stats">
        <div class="stat">
            <h3 id="donors-count">0</h3>
            <p>Donors</p>
        </div>
        <div class="stat">
            <h3 id="funds-donated">0</h3>
            <p>Total Funds Donated</p>
        </div>
        <div class="stat">
            <h3 id="beneficiaries-count">0</h3>
            <p>Beneficiaries</p>
        </div>
    </div>

    <?php
    // Include the database connection
    include 'connection.php';

    // Query to get the statistics
    $donors_query = "SELECT COUNT(*) AS count FROM donors";
    $funds_query = "SELECT SUM(amount) AS sum FROM donations";
    $beneficiaries_query = "SELECT COUNT(*) AS count FROM beneficiaries";

    $donors_result = $database->query($donors_query);
    $funds_result = $database->query($funds_query);
    $beneficiaries_result = $database->query($beneficiaries_query);

    $donors_count = $donors_result->fetch_assoc()['count'] ?? 0;
    $funds_donated = $funds_result->fetch_assoc()['sum'] ?? 0;
    $beneficiaries_count = $beneficiaries_result->fetch_assoc()['count'] ?? 0;

    $database->close();
    ?>

    <script>
        document.getElementById('donors-count').innerText = <?php echo $donors_count; ?>;
        document.getElementById('funds-donated').innerText = <?php echo $funds_donated; ?>;
        document.getElementById('beneficiaries-count').innerText = <?php echo $beneficiaries_count; ?>;

        // Number animation
        function animateValue(id, start, end, duration) {
            const obj = document.getElementById(id);
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerText = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        animateValue("donors-count", 0, <?php echo $donors_count; ?>, 2000);
        animateValue("funds-donated", 0, <?php echo $funds_donated; ?>, 2000);
        animateValue("beneficiaries-count", 0, <?php echo $beneficiaries_count; ?>, 2000);
    </script>

</body>
</html>
