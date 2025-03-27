import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-analytics.js";
import { getAuth, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyCwXo9ad3mdtx3JmSTvod32lXk6-PxstLw",
    authDomain: "ugnay-pinsao-proper.firebaseapp.com",
    projectId: "ugnay-pinsao-proper",
    storageBucket: "ugnay-pinsao-proper.firebasestorage.app",
    messagingSenderId: "392821648439",
    appId: "1:392821648439:web:7717f806aaf3617f1b63b2",
    measurementId: "G-3300P9PRPK"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const auth = getAuth(app);

// Add event listener to the form
const form = document.querySelector('.login');
form.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent form from submitting traditionally

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    signInWithEmailAndPassword(auth, email, password)
        .then((userCredential) => {
            const user = userCredential.user;
            alert("Login successful");
            // Redirect to admin page
            window.location.href = "../html/admin_page.html";
        })
        .catch((error) => {
            const errorCode = error.code;
            const errorMessage = error.message;
            alert(`Error: ${errorMessage}`);
        });
});
