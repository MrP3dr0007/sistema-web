<?php

session_start();
include("conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Busca os usuários
$sql = $conn->prepare(
    "SELECT id, nome, email FROM usuarios ORDER BY id DESC"
);

$sql->execute();

$result = $sql->get_result();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Lista de Usuários</title>

<style>

* {
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', 'Segoe UI', sans-serif;
    background: #000;
    color: #fff;

    margin: 0;
    padding: 40px;
}

.container {
    max-width: 900px;
    margin: auto;
}

h2 {
    color: #00ff66;
    text-align: center;
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #111;

    border: 1px solid #00ff66;
    border-radius: 10px;
    overflow: hidden;
}

th {
    background: #00ff66;
    color: #000;
    padding: 15px;
    text-align: left;
}

td {
    padding: 15px;
    border-bottom: 1px solid #333;
}

tr:hover {
    background: #1a1a1a;
}

.voltar {
    display: inline-block;

    margin-top: 20px;
    padding: 10px 20px;

    background: #111;
    color: #00ff66;

    border: 1px solid #00ff66;
    border-radius: 8px;

    text-decoration: none;
    font-weight: bold;

    transition: 0.2s;
}

.voltar:hover {
    background: #00ff66;
    color: #000;
}

/* RESPONSIVO PARA CELULAR */

@media (max-width: 600px) {

    body {
        padding: 15px;
    }

    .container {
        width: 100%;
    }

    h2 {
        font-size: 22px;
        margin-bottom: 20px;
    }

    /* Permite rolar a tabela horizontalmente */
    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    th,
    td {
        padding: 12px;
        font-size: 14px;
    }

    .voltar {
        width: 100%;
        text-align: center;
        padding: 12px;
    }
}

</style>

</head>

<body>

<div class="container">

    <h2>Usuários cadastrados</h2>

    <table>

        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
        </tr>

        <?php while ($u = $result->fetch_assoc()): ?>

        <tr>

            <td>
                <?= (int) $u['id'] ?>
            </td>

            <td>
                <?= htmlspecialchars($u['nome']) ?>
            </td>

            <td>
                <?= htmlspecialchars($u['email']) ?>
            </td>

        </tr>

        <?php endwhile; ?>

    </table>

    <a href="index.php" class="voltar">← Voltar para o dashboard</a>
</div>

</body>

</html>

<?php

$sql->close();

?>