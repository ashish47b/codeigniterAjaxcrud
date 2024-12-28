<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f3f3f3;
        padding: 0;
        margin: 0;
    }

    .container {
        max-width: 500px;
        margin: 100px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #007bff;
    }

    label {
        font-weight: bold;
    }

    input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #0056b3;
    }

    .message {
        margin-top: 10px;
        text-align: center;
    }

    #otp-section {
        display: none;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <div id="responseMessage" class="message"></div>
        <div id="login-section">
            <form id="loginForm">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="button" onclick="submitLogin()">Login</button>
            </form>

        </div>

        <div id="otp-section">
            <h2>Enter OTP</h2>
            <label for="otp">OTP</label>
            <input type="text" id="otp" name="otp" required>
            <button type="button" onclick="verifyOtp()">Verify OTP</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function submitLogin() {
        const username = $('#username').val();
        const password = $('#password').val();

        if (!username || !password) {
            alert("Please fill in all fields.");
            return;
        }

        $.ajax({
            url: "<?= base_url('/usercontroller/login') ?>",
            type: "POST",
            data: {
                username: username,
                password: password
            },
            dataType: "json",
            success: function(response) {
                const responseMessage = $('#responseMessage');
                if (response.status === 'otp_required') {
                    // Show OTP input form
                    $('#login-section').hide();
                    $('#otp-section').show();
                } else if (response.status === 'success') {
                    window.location.href = "<?= base_url('/dashboard') ?>"; // Redirect to the dashboard
                } else {
                    responseMessage.css('color', 'red');
                    responseMessage.text(response.message);
                }
            },
            error: function() {
                alert("An error occurred.");
            }
        });
    }

    function verifyOtp() {
        const otp = $('#otp').val();

        if (!otp) {
            alert("Please enter the OTP.");
            return;
        }

        const userId =
            "<?= $this->session->userdata('user_id') ?>"; // You can fetch this value from session or hidden field
        $.ajax({
            url: "<?= base_url('/usercontroller/verify_otp') ?>",
            type: "POST",
            data: {
                otp: otp,
                user_id: userId
            },
            dataType: "json",
            success: function(response) {
                const responseMessage = $('#responseMessage');
                if (response.status === 'success') {
                    responseMessage.css('color', 'blue');
                    responseMessage.text(response.message);
                    // window.location.href =
                    // "<?= base_url('/dashboard') ?>"; // Redirect to dashboard after OTP verification
                } else {
                    responseMessage.css('color', 'red');
                    responseMessage.text(response.message);
                }
            },
            error: function() {
                alert("An error occurred during OTP verification.");
            }
        });
    }
    </script>
</body>

</html>