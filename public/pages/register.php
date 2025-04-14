<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" type="text/css" href="../assets/commonstyles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        .error {
    color: #ff4d4d;
    font-size: 0.8rem;
    margin-top: 4px;
    display: block;
    }

    </style>
</head>
<body>
    <div class="wrapper">
    <form id="signupForm" action="../../api/signup.php" method="post" novalidate>
    <h1>Sign Up</h1>

    <div class="input-box">
        <input type="text" id="username" name="name" placeholder="Username">
        <i class='bx bxs-user'></i>
        <span class="error" id="usernameError"></span>
    </div>

    <div class="input-box">
        <input type="email" id="email" name="email" placeholder="Email">
        <i class='bx bxs-envelope'></i>
        <span class="error" id="emailError"></span>
    </div>

    <div class="input-box">
        <input type="password" id="password" name="password" placeholder="Password">
        <span class="error" id="passwordError"></span>
    </div>

    <div class="input-box">
        <input type="text" id="phone" name="phone_number" placeholder="Enter your phone number">
        <i class='bx bxs-phone'></i>
        <span class="error" id="phoneError"></span>
    </div>

    <div class="input-box">
        <input type="date" id="dob" name="age" placeholder="Date of Birth">
        <span class="error" id="dobError"></span>
    </div>

    <div class="input-box">
        <select id="gender" name="gender">
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        <span class="error" id="genderError"></span>
    </div>

    <button type="submit" class="btn">Sign Up</button>
</form>

    </div>

    <!-- Inline JavaScript validation -->
    <script>
   document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signupForm");

    form.addEventListener("submit", function (e) {
        let valid = true;

        // Clear all previous errors
        document.querySelectorAll(".error").forEach(el => el.textContent = "");

        const username = document.getElementById("username").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const dob = document.getElementById("dob").value;
        const gender = document.getElementById("gender").value;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9]{10}$/;

        // Username check
        if (!username) {
            document.getElementById("usernameError").textContent = "Username is required.";
            valid = false;
        } else if (username.length < 3) {
            document.getElementById("usernameError").textContent = "Username must be at least 3 characters.";
            valid = false;
        }

        // Email check
        if (!email) {
            document.getElementById("emailError").textContent = "Email is required.";
            valid = false;
        } else if (!emailRegex.test(email)) {
            document.getElementById("emailError").textContent = "Invalid email format.";
            valid = false;
        }

        // Password check
        if (!password) {
            document.getElementById("passwordError").textContent = "Password is required.";
            valid = false;
        } else if (password.length < 6) {
            document.getElementById("passwordError").textContent = "Password must be at least 6 characters.";
            valid = false;
        }

        // Phone number check
        if (!phone) {
            document.getElementById("phoneError").textContent = "Phone number is required.";
            valid = false;
        } else if (!phoneRegex.test(phone)) {
            document.getElementById("phoneError").textContent = "Phone must be exactly 10 digits.";
            valid = false;
        }

        // DOB check
        if (!dob) {
            document.getElementById("dobError").textContent = "Date of birth is required.";
            valid = false;
        }

        // Gender check
        if (!gender) {
            document.getElementById("genderError").textContent = "Please select your gender.";
            valid = false;
        }

        if (!valid) {
            e.preventDefault(); // stop form submission
        }
    });
});

</script>

</body>
</html>
