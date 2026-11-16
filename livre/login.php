<?php
include 'conexao.php';

$erro = '';

if (isset($_POST['entrar'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $resultado = mysqli_query($conexao, "SELECT * FROM usuarios WHERE email = '$email'");
    $usuario = mysqli_fetch_assoc($resultado);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id'] = $usuario['cd_usuario'];
        $_SESSION['nome'] = $usuario['nm_usuario'];
        $_SESSION['email'] = $usuario['email'];
        header('Location: index.php');
        exit;
    } else {
        $erro = 'E-mail ou senha incorretos.';
    }
}

$titulo = 'Entrar';
include 'head.php';
?>
<body class="pagina-publica">
<div class="auth">
  <?php include 'auth_painel.php'; ?>

  <section class="auth-formulario">
    <div class="auth-caixa">
      <a class="marca" href="login.php"><?php include 'logo.php'; ?><span>livre</span></a>
      <h1>Entrar</h1>
      <p class="subtitulo">Acesse suas finanças com seu e-mail e senha.</p>

      <?php include 'mostra_erro.php'; ?>

      <form method="post">
        <div class="mb-3">
          <label class="form-label" for="email">E-mail<span class="obrigatorio">*</span></label>
          <input class="form-control" type="email" id="email" name="email" required autofocus>
        </div>

        <div class="mb-4">
          <label class="form-label" for="senha">Senha<span class="obrigatorio">*</span></label>
          <input class="form-control" type="password" id="senha" name="senha" required>
        </div>

        <button class="btn btn-primary w-100" name="entrar">Entrar</button>
      </form>

      <p class="auth-rodape">Ainda não tem conta? <a href="cadastro.php">Criar conta grátis</a></p>
    </div>
  </section>
</div>
</body>
</html>
