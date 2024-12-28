<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Authenticator</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f0f4f8;
        color: #333;
        background-image: repeating-linear-gradient(rgba(0, 0, 0, 0.05),
                rgba(0, 0, 0, 0.05)),
            url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' height='200' width='300'%3E%3Ctext x='20' y='150' font-size='80' font-family='Arial' fill='rgba(0,0,255,0.2)' transform='rotate(-30)'%3E WEFRU%3C/text%3E%3C/svg%3E");
        background-size: cover;
        background-attachment: fixed;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.9);
        /* Add a slight transparency to the container background */
    }

    h1 {
        font-size: 24px;
        text-align: center;
        color: #007bff;
    }

    p {
        font-size: 16px;
        text-align: center;
        margin-bottom: 20px;
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

    form {
        text-align: center;
        margin-top: 30px;
    }

    input[type="text"] {
        width: calc(100% - 40px);
        padding: 10px;
        margin: 10px auto;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }

    button {
        padding: 10px 20px;
        font-size: 16px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Setup Your Authenticator App</h1>
        <p>Scan this QR code using Google Authenticator or any compatible app:</p>
        <div class="qr-code">
            <img src="<?= $qrCodeUrl ?>" alt="QR Code">
        </div>

        <div class="secret-code">
            <span class="code-box" id="secret-code"><?= $secretKey ?></span>
            <button class="copy-btn" onclick="copyCode()">Copy</button>
        </div>

        <h1>Enter the OTP</h1>
        <form id="otpForm">
            <input type="hidden" name="secret" id="secret" value="<?= $secretKey ?>">
            <input type="text" name="otp" id="otp" placeholder="Enter OTP" required>
            <button type="button" onclick="submitOtp()">Verify</button>
        </form>

        <div id="responseMessage" style="margin-top: 20px; font-weight: bold;"></div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
    function copyCode() {
        const codeElement = document.getElementById('secret-code');
        const code = codeElement.textContent;
        navigator.clipboard.writeText(code).then(() => {
            alert("Secret code copied to clipboard!");
        }).catch(err => {
            console.error("Failed to copy code: ", err);
        });
    }

    // function submitOtp() {
    //     const otp = document.getElementById('otp').value;
    //     const secret = document.getElementById('secret').value;

    //     if (!otp) {
    //         alert("Please enter the OTP!");
    //         return;
    //     }

    //     fetch("<?= base_url('/GoogleTOT/verify_authenticator') ?>", {
    //             method: "POST",
    //             headers: {
    //                 "Content-Type": "application/x-www-form-urlencoded",
    //             },
    //             body: `otp=${otp}&secret=${secret}`,
    //         })
    //         .then(response => response.json())
    //         .then(data => {
    //             const responseMessage = document.getElementById('responseMessage');
    //             if (data.status === 'success') {
    //                 responseMessage.style.color = 'green';
    //                 responseMessage.textContent = data.message;
    //             } else {
    //                 responseMessage.style.color = 'red';
    //                 responseMessage.textContent = data.message;
    //             }
    //         })
    //         .catch(error => {
    //             console.error("Error:", error);
    //             alert("An error occurred while verifying the OTP.");
    //         });
    // }
    function submitOtp() {
        const otp = $('#otp').val();
        const secret = $('#secret').val();

        if (!otp) {
            alert("Please enter the OTP!");
            return;
        }

        $.ajax({
            url: "<?= base_url('/GoogleTOT/verify_authenticator') ?>",
            type: "POST",
            data: {
                otp: otp,
                secret: secret
            },
            dataType: "json",
            success: function(data) {
                const responseMessage = $('#responseMessage');
                if (data.status === 'success') {
                    responseMessage.css('color', 'green');
                    responseMessage.text(data.message);
                } else {
                    responseMessage.css('color', 'red');
                    responseMessage.text(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                alert("An error occurred while verifying the OTP.");
            }
        });
    }
    </script>
</body>

</html>