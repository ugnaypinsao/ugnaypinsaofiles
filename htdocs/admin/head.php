<?php
if (
    !isset($_SESSION['logged_in'], $_SESSION['role']) ||
    $_SESSION['logged_in'] !== true
    //  ||
    // $_SESSION['role'] !== 'admin' || !$_SESSION['verified']
) {
    // header('Location: ../');
    // exit;
}
?>
