<?php

session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    header("Location: login.php?erro=preencha");
    exit;
}

$sql = $conn->prepare(
    "SELECT id, nome, senha, role FROM usuarios WHERE email = ?"
);

$sql->bind_param("s", $email);
$sql->execute();

$result = $sql->get_result();

if ($user = $result->fetch_assoc()) {

    if (password_verify($senha, $user['senha'])) {

        session_regenerate_id(true);

        $_SESSION['user'] = $user['nome'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php");
        exit;

    } else {

        header("Location: login.php?erro=senha");
        exit;
    }

} else {

    header("Location: login.php?erro=usuario");
    exit;
}
?>