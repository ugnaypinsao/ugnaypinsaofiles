// Update last activity dynamically (Example)
document.addEventListener("DOMContentLoaded", function () {
    let lastActivity = localStorage.getItem("lastActivity") || "No recent activity";
    let lastActivityTime = localStorage.getItem("lastActivityTime") || "N/A";
    document.getElementById("last-activity").textContent = lastActivity;
    document.getElementById("last-activity-time").textContent = lastActivityTime;

    // Simulate updating activity (replace with real logic)
    document.querySelectorAll(".button").forEach(button => {
        button.addEventListener("click", function () {
            localStorage.setItem("lastActivity", this.textContent.trim());
            localStorage.setItem("lastActivityTime", new Date().toLocaleString());
        });
    });
});