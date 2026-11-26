<?php
require_once __DIR__ . '/../includes/functions.php';
unset($_SESSION['user']);
session_regenerate_id(true);
set_flash('You have been signed out.');
header('Location: ../index.php');
exit;
