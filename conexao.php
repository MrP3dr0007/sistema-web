<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "sistema_web";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("<div style='color:red;'>❌ Erro na conexão com o banco!</div>");
}
?>