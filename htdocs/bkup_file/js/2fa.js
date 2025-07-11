document.addEventListener('DOMContentLoaded', () => {
    const email = localStorage.getItem('userEmail');
    
    if (!email) {
        window.location.href = 'login.html'; // Redirect to login if email is not found
    }

    const codeInput = document.getElementById('codeInput');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const messageElem = document.getElementById('message');

    // Handle verification of 2FA code
    verifyBtn.addEventListener('click', () => {
        const code = codeInput.value.trim();
        if (code.length !== 6) {
            messageElem.textContent = 'Please enter a 6-digit code.';
            return;
        }

        // Show loading message or spinner
        messageElem.textContent = 'Verifying...';

        // Send the entered code to verify
        fetch('verify_2fa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `email=${email}&code=${code}`,
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                window.location.href = 'admin_page.html'; // Redirect to admin page on success
            } else if (data === 'expired') {
                messageElem.textContent = 'The code has expired. Please request a new one.';
            } else if (data === 'invalid') {
                messageElem.textContent = 'Invalid code. Please try again.';
            }
        })
        .catch(error => {
            messageElem.textContent = 'An error occurred. Please try again later.';
            console.error('Error:', error);
        });
    });

    // Handle resending the 2FA code
    resendBtn.addEventListener('click', () => {
        messageElem.textContent = 'Resending code...';

        fetch('send_2fa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `email=${email}`,
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'sent') {
                messageElem.textContent = 'A new code has been sent to your email.';
            } else {
                messageElem.textContent = 'Failed to resend the code. Please try again later.';
            }
        })
        .catch(error => {
            messageElem.textContent = 'An error occurred. Please try again later.';
            console.error('Error:', error);
        });
    });
});
