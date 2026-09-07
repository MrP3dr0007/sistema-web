<?php

session_start();
include 'conexao.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

/* VERIFICAR MÉTODO */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

/* VERIFICAR CSRF */

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("❌ Requisição inválida!");
}

/* VERIFICAR ID */

if (!isset($_POST['id'])) {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    header("Location: index.php");
    exit;
}

// Admin pode excluir qualquer usuário; usuário comum só o próprio.
$souAdmin = ($_SESSION['role'] ?? 'user') === 'admin';

if (!$souAdmin && $id !== (int) $_SESSION['user_id']) {
    header("Location: index.php");
    exit;
}

/* EXCLUIR USUÁRIO */

$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("❌ Erro ao excluir usuário!");
}

$stmt->close();
$conn->close();

// Se apagou a própria conta, encerra a sessão em vez de mandar
// pro index.php como se ainda estivesse logado.
if ($id === (int) $_SESSION['user_id']) {
    session_destroy();
    header("Location: login.php");
    exit;
}

header("Location: index.php");
exit;

?>