<?php
session_start();
if (
  !isset($_SESSION['logged_in'], $_SESSION['role']) ||
  $_SESSION['logged_in'] !== true ||
  $_SESSION['role'] !== 'admin'
) {
  header('Location: index.php');
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>2FA Verification</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const email = 'ugnaypinsao@gmail.com';

      // Send 2FA code button click handler
      document.getElementById('sendCodeBtn').addEventListener('click', function() {
        console.log('Send 2FA Code button clicked');
        fetch('../php/send_2fa.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `email=${encodeURIComponent(email)}`
          })
          .then(response => response.text())
          .then(data => {
            if (data.trim() === 'sent') {
              alert('2FA code has been sent to your email.');
            } else {
              alert('Failed to send 2FA code. Response: ' + data);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the 2FA code.');
          });
      });

      // Handle automatic verification when the code is typed
      const verificationCodeInput = document.getElementById('verificationCode');
      verificationCodeInput.addEventListener('input', function() {
        const code = verificationCodeInput.value;
        if (code.length === 6) {
          fetch('../php/verify_2fa.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: `email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}`
            })
            .then(response => response.text())
            .then(data => {
              if (data.trim() === 'success') {
                window.location.href = '../admin'; // Redirect to admin page on success
              } else {
                alert('Invalid code, please try again.');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('An error occurred while verifying the code.');
            });
        }
      });

      // Resend code button
      document.getElementById('resendCodeBtn').addEventListener('click', function() {
        alert('Resending 2FA code...');
        document.getElementById('message').textContent = ''; // Clear any previous message
        verificationCodeInput.value = ''; // Clear the input field
        fetch('../php/send_2fa.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `email=${encodeURIComponent(email)}`
          })
          .then(response => response.text())
          .then(data => {
            if (data.trim() === 'sent') {
              alert('2FA code has been resent to your email.');
            } else {
              alert('Failed to resend 2FA code. Response: ' + data);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resending the 2FA code.');
          });
      });

      // Verify code button click handler
      document.getElementById('verifyBtn').addEventListener('click', function() {
        const code = verificationCodeInput.value;
        if (code.length === 6) {
          fetch('../php/verify_2fa.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: `email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}`
            })
            .then(response => response.text())
            .then(data => {
              if (data.trim() === 'success') {
                window.location.href = 'admin_page.html'; // Redirect to admin page on success
              } else if (data.trim() === 'expired') {
                alert('The code has expired. Please request a new one.');
              } else {
                alert('Invalid code, please try again.');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('An error occurred while verifying the code.');
            });
        } else {
          alert('Please enter a valid 6-digit code.');
        }
      });
    });
  </script>
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">
  <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
    <h2 class="text-2xl font-semibold mb-4 text-center">2FA Verification</h2>
    <button id="sendCodeBtn" class="w-full bg-blue-500 text-white py-2 rounded-md mb-4 hover:bg-blue-600 focus:outline-none">
      Send 2FA Code
    </button>

    <p id="message" class="text-center mb-4 text-gray-700"></p>

    <input type="text" id="verificationCode" maxlength="6" placeholder="Enter 6-digit code" class="w-full p-2 border border-gray-300 rounded-md text-center mb-4 text-xl" />

    <button id="resendCodeBtn" class="w-full bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 focus:outline-none">
      Resend Code
    </button>

    <!-- Verify button -->
    <button id="verifyBtn" class="w-full bg-green-500 text-white py-2 rounded-md mt-4 hover:bg-green-600 focus:outline-none">
      Verify Code
    </button>
  </div>
</body>

</html>