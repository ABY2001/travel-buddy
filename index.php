<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: public/pages/login.php");
    exit();
}
?>
