<?php

session_start();
include 'conexao.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    header("Location: index.php");
    exit;
}

// Admin pode editar qualquer usuário; usuário comum só o próprio.
$souAdmin = ($_SESSION['role'] ?? 'user') === 'admin';

if (!$souAdmin && $id !== (int) $_SESSION['user_id']) {
    header("Location: index.php");
    exit;
}

// Buscar usuário
$stmt = $conn->prepare(
    "SELECT id, nome, email FROM usuarios WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    header("Location: index.php");
    exit;
}

$mensagem = "";
$tipo = "";

if (isset($_POST['salvar'])) {

    // Verificar CSRF
    if (
        !isset($_POST['csrf_token']) ||
        !isset($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("❌ Requisição inválida!");
    }

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($nome) || empty($email)) {

        $mensagem = "Preencha o nome e o e-mail!";
        $tipo = "erro";

    } else {

        // Verificar se o e-mail já pertence a outro usuário
        $check = $conn->prepare(
            "SELECT id FROM usuarios WHERE email = ? AND id != ?"
        );

        $check->bind_param("si", $email, $id);
        $check->execute();

        $resultadoCheck = $check->get_result();

        if ($resultadoCheck->num_rows > 0) {

            $mensagem = "Este e-mail já está cadastrado!";
            $tipo = "erro";

        } else {

            // Se a senha estiver vazia, mantém a senha atual
            if (!empty($senha)) {

                // Validar tamanho mínimo da nova senha
                if (strlen($senha) < 6) {

                    $mensagem = "A nova senha deve ter pelo menos 6 caracteres!";
                    $tipo = "erro";

                } else {

                    $senhaHash = password_hash(
                        $senha,
                        PASSWORD_DEFAULT
                    );

                    $update = $conn->prepare(
                        "UPDATE usuarios
                         SET nome = ?, email = ?, senha = ?
                         WHERE id = ?"
                    );

                    $update->bind_param(
                        "sssi",
                        $nome,
                        $email,
                        $senhaHash,
                        $id
                    );

                    if ($update->execute()) {

                        $mensagem = "Usuário atualizado com sucesso!";
                        $tipo = "sucesso";

                        // Atualiza os dados exibidos no formulário
                        $usuario['nome'] = $nome;
                        $usuario['email'] = $email;

                    } else {

                        $mensagem = "Erro ao atualizar usuário!";
                        $tipo = "erro";
                    }

                    $update->close();
                }

            } else {

                $update = $conn->prepare(
                    "UPDATE usuarios
                     SET nome = ?, email = ?
                     WHERE id = ?"
                );

                $update->bind_param(
                    "ssi",
                    $nome,
                    $email,
                    $id
                );

                if ($update->execute()) {

                    $mensagem = "Usuário atualizado com sucesso!";
                    $tipo = "sucesso";

                    // Atualiza os dados exibidos no formulário
                    $usuario['nome'] = $nome;
                    $usuario['email'] = $email;

                } else {

                    $mensagem = "Erro ao atualizar usuário!";
                    $tipo = "erro";
                }

                $update->close();
            }
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Editar usuário</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            background: #000;
            color: #fff;

            display: flex;
            justify-content: center;
            align-items: center;

            min-height: 100vh;
            margin: 0;

            padding: 20px;
        }

        .card {
            background: #111;

            padding: 30px;

            border-radius: 15px;

            width: 350px;
            max-width: 100%;

            border: 1px solid #00ff66;

            box-shadow:
                0 0 30px rgba(0, 255, 102, 0.15);
        }

        h2 {
            text-align: center;
            color: #00ff66;

            margin-top: 0;
            margin-bottom: 25px;
        }

        input {
            width: 100%;

            padding: 12px;

            margin: 8px 0;

            border-radius: 8px;

            border: 1px solid #333;

            background: #1a1a1a;
            color: #fff;

            outline: none;
        }

        input:focus {
            border-color: #00ff66;

            box-shadow:
                0 0 5px rgba(0, 255, 102, 0.3);
        }

        input::placeholder {
            color: #888;
        }

        button {
            width: 100%;

            padding: 12px;

            margin-top: 15px;

            background: #00ff66;

            border: none;

            color: #000;

            border-radius: 8px;

            cursor: pointer;

            font-weight: bold;

            transition: 0.2s;
        }

        button:hover {
            background: #00cc52;
        }

        .voltar {
            display: block;

            text-align: center;

            margin-top: 15px;

            padding: 10px;

            color: #00ff66;

            font-weight: bold;

            text-decoration: none;

            border: 1px solid #00ff66;

            border-radius: 8px;

            transition: 0.2s;
        }

        .voltar:hover {
            background: #00ff66;
            color: #000;
        }

        .msg {
            text-align: center;

            margin-bottom: 15px;

            padding: 10px;

            border-radius: 8px;
        }

        .msg.erro {
            background: #330000;
            color: #ff4444;

            border-left: 4px solid #ff4444;
        }

        .msg.sucesso {
            background: #003311;
            color: #00ff66;

            border-left: 4px solid #00ff66;
        }

        small {
            display: block;

            color: #aaa;

            line-height: 1.5;

            margin-top: 5px;
        }


        /* ========================= */
        /* RESPONSIVIDADE - CELULAR */
        /* ========================= */

        @media (max-width: 500px) {

            body {
                padding: 10px;
            }

            .card {
                width: 100%;

                padding: 25px 20px;

                border-radius: 12px;
            }

            h2 {
                font-size: 22px;

                margin-bottom: 20px;
            }

            input {
                min-height: 45px;

                padding: 12px;
            }

            button {
                min-height: 45px;
            }

            .voltar {
                min-height: 45px;

                display: flex;

                align-items: center;
                justify-content: center;

                padding: 10px;
            }

            .msg {
                font-size: 14px;
            }

            small {
                font-size: 12px;
            }
        }


        /* CELULARES MUITO PEQUENOS */

        @media (max-width: 360px) {

            body {
                padding: 5px;
            }

            .card {
                padding: 20px 15px;
            }

            h2 {
                font-size: 20px;
            }

            input,
            button {
                font-size: 14px;
            }

            .voltar {
                font-size: 13px;
            }

            small {
                font-size: 11px;
            }
        }

    </style>

</head>

<body>

<div class="card">

    <h2>✏️ Editar usuário</h2>

    <?php if ($mensagem): ?>

        <div class="msg <?= htmlspecialchars($tipo) ?>">

            <?= htmlspecialchars($mensagem) ?>

        </div>

    <?php endif; ?>


    <form method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >

        <input
            type="text"
            name="nome"
            value="<?= htmlspecialchars($usuario['nome']) ?>"
            placeholder="Nome"
            required
        >

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($usuario['email']) ?>"
            placeholder="E-mail"
            required
        >

        <input
            type="password"
            name="senha"
            placeholder="Nova senha (opcional)"
            minlength="6"
        >

        <small>
            Deixe em branco para manter a senha atual.
            A nova senha deve ter pelo menos 6 caracteres.
        </small>

        <button type="submit" name="salvar">
            💾 Salvar alterações
        </button>

    </form>


    <a class="voltar" href="index.php">
        ← Voltar para usuários
    </a>

</div>

</body>

</html>