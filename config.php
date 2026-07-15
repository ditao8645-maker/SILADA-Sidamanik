<?php

$session_dir = __DIR__ . '/sessions';
if (!is_dir($session_dir)) {
    mkdir($session_dir, 0777, true);
}

if (session_status() === PHP_SESSION_ACTIVE && session_save_path() !== $session_dir) {
    session_write_close(); 
}


if (session_status() === PHP_SESSION_NONE) {
    session_save_path($session_dir);
    $umur_session = 28800; // 8 jam
    session_set_cookie_params($umur_session, '/'); 
    ini_set('session.gc_maxlifetime', $umur_session);
    session_start();
}

$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "db_sidamanik";

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_errno) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}
?>