<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f3f3f3;
        padding: 0;
        margin: 0;
    }

    .container {
        max-width: 500px;
        margin: 50px auto;
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

    .qr-code {
        text-align: center;
        margin: 20px 0;
    }

    img {
        max-width: 200px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .secret-code {
        text-align: center;
        margin-top: 20px;
    }

    .code-box {
        display: inline-block;
        background: #e7f3ff;
        color: #007bff;
        padding: 10px 20px;
        font-size: 18px;
        font-weight: bold;
        border-radius: 4px;
        margin-right: 10px;
    }

    .copy-btn {
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .copy-btn:hover {
        background-color: #0056b3;
    }

    .process-bar {
        width: 100%;
        background-color: #ddd;
        border-radius: 20px;
        overflow: hidden;
        margin-top: 20px;
    }

    .process-bar-inner {
        height: 20px;
        width: 0;
        background-color: #007bff;
        text-align: center;
        color: white;
        line-height: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Register</h1>
        <form id="registerForm">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="button" onclick="submitRegistration()">Register</button>
        </form>

        <div id="responseMessage" class="message"></div>

        <!-- Process Bar -->
        <div class="process-bar">
            <div id="process-bar-inner" class="process-bar-inner">0%</div>
        </div>

        <div id="otp-section" style="display: none;">
            <input type="hidden" id="user_id" value="<?= $userId ?>">
            <h2>Step 2: Two-Step Verification</h2>
            <p>Scan this QR code using Google Authenticator or a compatible app:</p>
            <div class="qr-code">
                <!-- QR code will be displayed here -->
                <img src="" alt="QR Code">
            </div>

            <div class="secret-code">
                <span class="code-box" id="secret-code"></span>
                <button class="copy-btn" onclick="copyCode()">Copy Secret</button>
            </div>

            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <button type="button" onclick="submitOtp()">Verify OTP</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Function to copy the secret key to clipboard
    function copyCode() {
        const codeElement = document.getElementById('secret-code');
        const code = codeElement.textContent;
        navigator.clipboard.writeText(code).then(() => {
            alert("Secret code copied to clipboard!");
        }).catch(err => {
            console.error("Failed to copy code: ", err);
        });
    }

    // Function to submit the registration form
    function submitRegistration() {
        const username = $('#username').val();
        const email = $('#email').val();
        const password = $('#password').val();

        if (!username || !email || !password) {
            alert("Please fill in all fields.");
            return;
        }

        $.ajax({
            url: "<?= base_url('/usercontroller/register') ?>",
            type: "POST",
            data: {
                username: username,
                email: email,
                password: password
            },
            dataType: "json",
            success: function(response) {
                const responseMessage = $('#responseMessage');
                if (response.status === 'otp_required') {
                    $('#otp-section').show(); // Show OTP section
                    $('#registerForm').hide(); // Show OTP section
                    $('#process-bar-inner').width('50%').text('50%');

                    // Display the QR code
                    $('.qr-code img').attr('src', response.qr_code_url);
                    $('#user_id').val(response.user_id);
                    $('#secret-code').text(response.secret); // Show the secret key for Google Authenticator
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

    // Function to submit OTP for verification
    function submitOtp() {
        const otp = $('#otp').val();
        const secret = $('#secret-code').text();
        const userId = $('#user_id').val(); // Get the user_id from a hidden field or variable

        if (!otp) {
            alert("Please enter the OTP.");
            return;
        }

        $.ajax({
            url: "<?= base_url('/usercontroller/verify_otpR') ?>",
            type: "POST",
            data: {
                otp: otp,
                secret: secret,
                user_id: userId // Pass the user_id to the server
            },
            dataType: "json",
            success: function(response) {
                const responseMessage = $('#responseMessage');
                if (response.status === 'success') {
                    responseMessage.css('color', 'green');
                    responseMessage.text(response.message);
                    $('#process-bar-inner').width('100%').text('100%');
                    window.location.href = "<?= base_url('/UserController/login') ?>";
                } else {
                    responseMessage.css('color', 'red');
                    responseMessage.text(response.message);
                }
            },
            error: function() {
                alert("An error occurred while verifying OTP.");
            }
        });
    }
    </script>
</body>

</html>