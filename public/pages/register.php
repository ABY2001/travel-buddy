<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" type="text/css" href="../assets/commonstyles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="wrapper">
        <form action="../../api/signup.php" method="post">
            <h1>Sign Up</h1>

            <div class="input-box">
                <input type="text" name="name" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class='bx bxs-envelope'></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-box">
                <input type="text" name="phone_number" placeholder="Enter your phone number" required>
                <i class='bx bxs-phone'></i>
            </div>

            <div class="input-box">
                <input type="date" name="age" placeholder="Date of Birth" required>
            </div>

            <button type="submit" class="btn">Sign Up</button>

            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>

</html>
