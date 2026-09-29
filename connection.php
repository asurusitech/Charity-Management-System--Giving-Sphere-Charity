<?php
$database = new mysqli("localhost", "root", "", "charity");
if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
} 
?>
