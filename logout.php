<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
session_destroy();
// unset($_SESSION['name']);
// unset($_SESSION['user_id']);
header('Location: index.php');
