<?php
session_start(); // Start a session to store user data
require('../config/database.php'); // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $EmployeeID = mysqli_real_escape_string($connection, trim($_POST['EmployeeID']));
    $Password = trim($_POST['password']);

    // Admin login check
    if ($EmployeeID === "Admin" && $Password === "123") {
        echo '<script>alert("Admin Login Successfully!"); window.location.href = "http://localhost/capstoneproject/admin/dashboard.php";</script>';
        exit;
    }

    // Check if EmployeeID or password is empty
    if (empty($EmployeeID) || empty($Password)) {
        echo '<script>alert("Please enter both Employee ID and password."); window.history.back();</script>';
        exit;
    }

    // Query to fetch the employee details
    $query = "SELECT ID, FullName, Password FROM srccapstoneproject.employeedb WHERE EmployeeID = ?";

    if ($stmt = mysqli_prepare($connection, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $EmployeeID); // Bind EmployeeID parameter
        mysqli_stmt_execute($stmt); // Execute the query
        mysqli_stmt_bind_result($stmt, $user_id, $FName, $HashedPassword); // Bind results to variables

        // Check if an employee record is fetched
        if (mysqli_stmt_fetch($stmt)) {
            // Verify the entered password against the hashed password
            if (password_verify($Password, $HashedPassword)) {
                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_employeeid'] = $EmployeeID;
                $_SESSION['user_fname'] = $FName;

                echo '<script>alert("Login Successfully!"); window.location.href = "index.php";</script>';
            } else {
                echo '<script>alert("Invalid Employee ID or password."); window.history.back();</script>';
            }
        } else {
            echo '<script>alert("No employee found with that Employee ID. Please enter valid credentials."); window.history.back();</script>';
        }

        mysqli_stmt_close($stmt); // Close the prepared statement
    } else {
        echo '<script>alert("Error in SQL preparation: ' . mysqli_error($connection) . '");</script>';
    }
}

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link href="img/SRCLogoNB.png" rel="icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url("img/newSRC.jpg") no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 25px;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            background-color: #5cb85c;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: 0.3s;
            margin-bottom: 10px; /* Added space below the button */
        }

        button:hover {
            background-color: #4cae4c;
        }

        .show-password {
            display: flex;
            align-items: center;
            margin-top: 5px;
            font-size: 14px;
            text-align: left;
        }

        .show-password input {
            margin-right: 5px;
        }
    </style>

    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- WOW.js for animation triggers -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>
</head>

<body>
    <div class="form-container wow animate__animated animate__zoomIn" data-wow-delay="0.5s">
        <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
            <img src="img/SRCLogoNB.png" alt="SRC Logo" style="width: 50px; height: auto;">
            <h2 style="margin: 0;">Log In</h2>
        </div>
        <form id="loginForm" method="POST" action="">
            <input type="text" name="EmployeeID" placeholder="Employee ID" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <div class="show-password">
                <input type="checkbox" id="togglePassword"> Show Password
            </div>
            <br>
            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('change', function() {
            var passwordField = document.getElementById('password');
            passwordField.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>

</html>
