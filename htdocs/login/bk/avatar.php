<?php
require_once '../conn/db.php';
$db = new DatabaseHandler();
if (isset($db->getUser()['id'])) {

    $id = $db->getUser()['id'];
    $user = $db->fetchOne("SELECT * FROM `users` WHERE id = :id AND status = 1", ['id' => $id]);

    if ($user['profile']) {
        if ($user['position'] === 'admin') {
            echo "
            <script>
            window.location.href='../admin/';
        </script>
            ";
        } else {
            echo "
        <script>
        window.location.href='../student/';
    </script>
        ";
        }
    }


} else {
    echo "
    <script>
    window.location.href='index.php';
</script>
    ";
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCUnnectED Dashboard</title>
    <link rel="stylesheet" href="../assets/css/avatar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="../assets/js/avatar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>CHOOSE YOUR AVATAR</h2>
        <p>Make your selection carefully,<br> as you can only choose an avatar once.</p>
        <div class="avatar-grid">
            <div class="avatar" onclick="selectAvatar(this, 'avatar1')"
                style="background-image: url('../assets/images/1.jfif');"></div>
            <div class="avatar" onclick="selectAvatar(this, 'avatar2')"
                style="background-image: url('../assets/images/2.png');"></div>
            <div class="avatar" onclick="selectAvatar(this, 'avatar3')"
                style="background-image: url('../assets/images/3.jfif');"></div>
            <div class="avatar" onclick="selectAvatar(this, 'avatar4')"
                style="background-image: url('../assets/images/4.jfif');"></div>
            <div class="avatar" onclick="selectAvatar(this, 'avatar5')"
                style="background-image: url('../assets/images/5.png');"></div>
            <div class="avatar" onclick="selectAvatar(this, 'avatar5')"
                style="background-image: url('../assets/images/6.jfif');"></div>
        </div>


        <button id="confirmBtn" onclick="confirmSelection()" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="24" viewBox="0 0 50 24">
                <path d="M10 12h20" stroke="black" stroke-width="2" />
                <path d="M30 8l8 4-8 4" stroke="black" stroke-width="2" />
            </svg>
        </button>
        <a href="index.php">
            <button id="backBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="24" viewBox="0 0 50 24">
                    <path d="M40 12h-20" stroke="black" stroke-width="2" />
                    <path d="M20 8l-8 4 8 4" stroke="black" stroke-width="2" />
                </svg>
            </button>
        </a>
    </div>
</body>

</html>