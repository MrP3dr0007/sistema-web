<?php

session_start();
include("conexao.php");

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cadastrar Usuário</title>

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
    padding: 35px;
    border-radius: 15px;

    width: 380px;
    max-width: 100%;

    border: 1px solid #00ff66;

    box-shadow: 0 0 30px rgba(0, 255, 102, 0.15);

    text-align: center;
}

h2 {
    color: #00ff66;
    margin-bottom: 25px;
}

input {
    width: 100%;
    padding: 13px;
    margin: 8px 0;

    border-radius: 8px;
    border: 1px solid #333;

    background: #1a1a1a;
    color: #fff;

    outline: none;
}

input:focus {
    border-color: #00ff66;
    box-shadow: 0 0 5px rgba(0, 255, 102, 0.3);
}

input::placeholder {
    color: #888;
}

button {
    width: 100%;
    padding: 13px;

    margin-top: 10px;

    background: #00ff66;
    color: #000;

    border: none;
    border-radius: 8px;

    cursor: pointer;
    font-weight: bold;

    transition: 0.2s;
}

button:hover {
    background: #00cc52;
}

/* BOTÃO DE LOGIN */

.btn-login {
    display: block;
    width: 100%;

    margin-top: 15px;
    padding: 10px;

    background: #111;
    color: #00ff66;

    border: 1px solid #00ff66;
    border-radius: 8px;

    text-decoration: none;
    font-weight: bold;

    transition: 0.2s;
}

.btn-login:hover {
    background: #00ff66;
    color: #000;
}

/* LINK VER USUÁRIOS */

.link {
    display: block;

    margin-top: 15px;

    color: #00ff66;
    text-decoration: none;

    font-weight: bold;
}

.link:hover {
    color: #00cc52;
}

.msg {
    margin: 10px 0;
    padding: 10px;

    border-radius: 8px;
}

.sucesso {
    background: #062b16;
    color: #00ff66;

    border-left: 4px solid #00ff66;
}

.erro {
    background: #2b0606;
    color: #ff5555;

    border-left: 4px solid #ff4444;
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
        font-size: 23px;
        margin-bottom: 20px;
    }

    input {
        padding: 12px;
        min-height: 45px;
    }

    button {
        min-height: 45px;
    }

    .btn-login {
        min-height: 45px;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 10px;
    }

    .link {
        font-size: 14px;
    }

    .msg {
        font-size: 14px;
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
        font-size: 21px;
    }

    input,
    button {
        font-size: 14px;
    }

    .btn-login {
        font-size: 13px;
    }

}

</style>

</head>

<body>

<div class="card">

    <h2>Cadastrar usuário</h2>

    <?php

    if (isset($_POST['cadastrar'])) {

        /* VERIFICAR CSRF */

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

        /* VERIFICAR CAMPOS */

        if (empty($nome) || empty($email) || empty($senha)) {

            echo "<div class='msg erro'>
                    ❌ Preencha todos os campos!
                  </div>";

        /* VERIFICAR SENHA */

        } elseif (strlen($senha) < 6) {

            echo "<div class='msg erro'>
                    ❌ A senha é muito fraca! Use pelo menos 6 caracteres.
                  </div>";

        } else {

            // Verifica se o e-mail já existe
            $check = $conn->prepare(
                "SELECT id FROM usuarios WHERE email = ?"
            );

            $check->bind_param("s", $email);
            $check->execute();

            $result = $check->get_result();

            if ($result->num_rows > 0) {

                echo "<div class='msg erro'>
                        ❌ Email já cadastrado!
                      </div>";

            } else {

                // Criptografa a senha antes de salvar
                $senhaHash = password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

                $sql = $conn->prepare(
                    "INSERT INTO usuarios (nome, email, senha)
                     VALUES (?, ?, ?)"
                );

                $sql->bind_param(
                    "sss",
                    $nome,
                    $email,
                    $senhaHash
                );

                if ($sql->execute()) {

                    echo "<div class='msg sucesso'>
                            ✅ Usuário cadastrado com sucesso!
                          </div>";

                } else {

                    echo "<div class='msg erro'>
                            ❌ Erro ao cadastrar usuário!
                          </div>";
                }

                $sql->close();
            }

            $check->close();
        }
    }

    ?>

    <form method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >

        <input
            type="text"
            name="nome"
            placeholder="Nome"
            required
        >

        <input
            type="email"
            name="email"
            placeholder="Email"
            required
        >

        <input
            type="password"
            name="senha"
            placeholder="Senha"
            minlength="6"
            required
        >

        <button name="cadastrar">
            Cadastrar
        </button>

        <a href="login.php" class="btn-login">
            Já tenho uma conta - Login
        </a>

    </form>

    <a class="link" href="listar.php">
        ← Ver usuários
    </a>

</div>

</body>

</html>