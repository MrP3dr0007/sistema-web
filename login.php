<?php include("conexao.php"); ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login</title>

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

    box-shadow:
        0 0 20px rgba(0, 255, 102, 0.15);

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

    font-size: 14px;
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

    padding: 13px;

    border: none;

    border-radius: 8px;

    cursor: pointer;

    font-weight: bold;

    font-size: 14px;

    transition: 0.2s;
}

/* BOTÃO LOGIN */

.btn-login {
    background: #00ff66;
    color: #000;

    margin-top: 10px;
}

.btn-login:hover {
    background: #00cc52;
}

/* BOTÃO CADASTRO */

.btn-cadastro {
    background: #111;

    color: #00ff66;

    border: 1px solid #00ff66;

    margin-top: 10px;
}

.btn-cadastro:hover {
    background: #00ff66;
    color: #000;
}

/* MENSAGENS */

.msg {
    margin: 10px 0;

    padding: 10px;

    border-radius: 8px;

    font-size: 14px;
}

.erro {
    background: #2b0606;

    color: #ff5555;

    border-left: 4px solid #ff4444;
}

.sucesso {
    background: #062b16;

    color: #00ff66;

    border-left: 4px solid #00ff66;
}


/* =========================================
   RESPONSIVO PARA CELULAR
   ========================================= */

@media (max-width: 500px) {

    body {
        padding: 15px;
    }

    .card {
        width: 100%;

        padding: 25px 20px;

        border-radius: 12px;
    }

    h2 {
        font-size: 24px;

        margin-bottom: 20px;
    }

    input {
        padding: 14px;

        font-size: 16px;
    }

    button {
        padding: 14px;

        font-size: 15px;
    }

    .msg {
        font-size: 13px;

        padding: 10px 8px;
    }
}


/* =========================================
   CELULARES MUITO PEQUENOS
   ========================================= */

@media (max-width: 360px) {

    body {
        padding: 10px;
    }

    .card {
        padding: 22px 16px;
    }

    h2 {
        font-size: 22px;
    }

    input {
        padding: 13px;
    }

    button {
        padding: 13px;
    }
}

</style>

</head>

<body>

<div class="card">

    <h2>Login</h2>

    <?php

    if (isset($_GET['erro'])) {

        if ($_GET['erro'] === 'senha') {
            echo "<div class='msg erro'>❌ Senha incorreta!</div>";
        }

        if ($_GET['erro'] === 'usuario') {
            echo "<div class='msg erro'>❌ Usuário não encontrado!</div>";
        }

        if ($_GET['erro'] === 'preencha') {
            echo "<div class='msg erro'>❌ Preencha todos os campos!</div>";
        }

    }

    ?>

    <form action="verifica_login.php" method="POST">

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
            required
        >

        <button
            type="submit"
            class="btn-login"
            name="login"
        >
            Entrar
        </button>

    </form>


    <!-- BOTÃO CADASTRAR -->

    <form action="cadastrar.php">

        <button
            type="submit"
            class="btn-cadastro"
        >
            + Criar conta
        </button>

    </form>

</div>


<script>

setTimeout(function() {

    const mensagem = document.querySelector('.msg');

    if (mensagem) {

        mensagem.classList.add('saindo');

        setTimeout(function() {

            mensagem.remove();

        }, 600);

    }

}, 3000);

</script>

</body>

</html>