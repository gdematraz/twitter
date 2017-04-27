<?php
require_once("public_html/db.php");

$mysqli = connect();

$admin_role = 1;
$admin_username = 'admin';

if (!$mysqli) {
    die(__FILE__ . ": Cannot connect to database");
}

$admin = getAdmin($mysqli);
if (!$admin) {
    $query = "insert into user (username, password, role) values (?, ?, ?)";
    $stmt = $mysqli->prepare($query);

    // Le mot de passe par défaut d'admin
    $password = 'admin1234';
    $stmt->bind_param("ssi", $admin_username, $password, $admin_role);
    $stmt->execute();
}

echo "OK";
