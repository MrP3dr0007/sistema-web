<?php

session_start();
include 'conexao.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$souAdmin = ($_SESSION['role'] ?? 'user') === 'admin';

/* PESQUISA */

$pesquisa = trim($_GET['pesquisa'] ?? '');

if ($pesquisa !== '') {

    $termo = "%" . $pesquisa . "%";

    $stmt = $conn->prepare(
        "SELECT id, nome, email
         FROM usuarios
         WHERE nome LIKE ? OR email LIKE ?
         ORDER BY id DESC"
    );

    $stmt->bind_param("ss", $termo, $termo);
    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $result = $conn->query(
        "SELECT id, nome, email
         FROM usuarios
         ORDER BY id DESC"
    );
}


/* TOTAL DE USUÁRIOS */

$totalQuery = $conn->query(
    "SELECT COUNT(*) AS total FROM usuarios"
);

$totalUsuarios = $totalQuery->fetch_assoc()['total'];


/* ÚLTIMO USUÁRIO CADASTRADO */

$ultimoQuery = $conn->query(
    "SELECT nome FROM usuarios
     ORDER BY id DESC
     LIMIT 1"
);

$ultimoUsuario = $ultimoQuery->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistema Web - Dashboard</title>

    <link rel="stylesheet" href="style.css">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap"
        rel="stylesheet"
    >

</head>

<body>

<div class="container">

    <!-- CABEÇALHO -->

    <div class="cabecalho">

        <div>

            <h1>🖥️ Sistema Web</h1>

            <p>
                Bem-vindo,
                <b><?= htmlspecialchars($_SESSION['user']) ?></b>
                <?php if ($souAdmin): ?>
                    <span class="badge-admin">admin</span>
                <?php endif; ?>
            </p>

        </div>

        <a class="btn-sair" href="logout.php">
            🚪 Sair
        </a>

    </div>


    <!-- CARDS -->

    <div class="dashboard">

        <div class="card-dashboard">

            <div class="icone">
                👥
            </div>

            <div>

                <span class="numero">
                    <?= $totalUsuarios ?>
                </span>

                <span class="descricao">
                    Usuários
                </span>

            </div>

        </div>


        <div class="card-dashboard">

            <div class="icone">
                🕐
            </div>

            <div>

                <span class="ultimo">

                    <?php
                    if ($ultimoUsuario) {
                        echo htmlspecialchars($ultimoUsuario['nome']);
                    } else {
                        echo "Nenhum";
                    }
                    ?>

                </span>

                <span class="descricao">
                    Último cadastro
                </span>

            </div>

        </div>

    </div>


    <!-- AÇÕES -->

    <div class="acoes-topo">

    <a class="btn-novo" href="cadastrar.php">
        ➕ Novo usuário
    </a>

    <a class="btn-listar" href="listar.php">
        👥 Ver usuários
    </a>



    <!-- PESQUISA -->

    <form method="GET" class="pesquisa">

        <input
            type="text"
            name="pesquisa"
            placeholder="🔎 Pesquisar por nome ou e-mail..."
            value="<?= htmlspecialchars($pesquisa) ?>"
        >

        <button type="submit">
            Pesquisar
        </button>

        <?php if ($pesquisa !== ''): ?>

            <a class="btn-limpar" href="index.php">
                Limpar
            </a>

        <?php endif; ?>

    </form>


    <!-- TÍTULO DA LISTA -->

    <div class="titulo-lista">

        <h2>👤 Usuários cadastrados</h2>

        <?php if ($pesquisa !== ''): ?>

            <span>
                Resultado para:
                <b><?= htmlspecialchars($pesquisa) ?></b>
            </span>

        <?php endif; ?>

    </div>


    <!-- USUÁRIOS -->

    <?php if ($result->num_rows === 0): ?>

        <div class="nenhum">
             Nenhum usuário encontrado.
        </div>

    <?php else: ?>

        <?php while ($u = $result->fetch_assoc()): ?>

            <div class="usuario">

                <div class="dados-usuario">

                    <b>
                        <?= htmlspecialchars($u['nome']) ?>
                    </b>

                    <span>
                        <?= htmlspecialchars($u['email']) ?>
                    </span>

                </div>


                <div class="acoes">

                    <?php if ($souAdmin || (int) $u['id'] === (int) $_SESSION['user_id']): ?>

                    <a
                        class="btn-editar"
                        href="editar.php?id=<?= $u['id'] ?>"
                    >
                        ✏️ Editar
                    </a>


                    <form
                        method="POST"
                        action="deletar.php"
                        onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');"
>
                        <input
                            type="hidden"
                            name="id"
                            value="<?= $u['id'] ?>"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= $_SESSION['csrf_token'] ?>"
                        >

                        <button type="submit" class="btn-excluir">
                            🗑️ Excluir
                        </button>
                    </form>

                    <?php endif; ?>

                </div>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

</body>

</html>