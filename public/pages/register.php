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

            <div class="input-box" style="position: relative; width: 100%; margin: 15px 0;">
                <select name="gender" required style="
                    width: 100%;
                    height: 50px;
                    background: rgba(255, 255, 255, 0.2);
                    border: none;
                    border-radius: 12px;
                    font-size: 16px;
                    color: white;
                    padding: 15px 14px;
                    outline: none;
                    appearance: none;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                ">
                    <option value="" disabled selected>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <i class='bx bx-male-female' style="
                    position: absolute;
                    right: 15px;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 22px;
                    color: rgba(255, 255, 255, 0.7);
                    pointer-events: none;
                "></i>
            </div>



            <button type="submit" class="btn">Sign Up</button>

            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
