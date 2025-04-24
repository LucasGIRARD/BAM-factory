<?php
session_start();

if (isset($_SESSION) && !empty($_SESSION) && $_SESSION['bamColletifOK'] == TRUE) {
    if (isset($_GET['p'])) {
        include $_GET['p'].'.php';
    } else {
        include 'manage.php';
    }    
} else {
    include 'login.php';
}