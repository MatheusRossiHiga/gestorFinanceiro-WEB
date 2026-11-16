<?php
include 'conexao.php';

$erro = '';

if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmacao = $_POST['confirmacao'];

    $resultado = mysqli_query($conexao, "SELECT * FROM usuarios WHERE email = '$email'");

    if ($nome == '' || $email == '' || $senha == '') {
        $erro = 'Preencha todos os campos.';
    } else if ($senha != $confirmacao) {
        $erro = 'As senhas não são iguais.';
    } else if (mysqli_num_rows($resultado) > 0) {
        $erro = 'Este e-mail já está cadastrado.';
    } else {
        $senha_segura = password_hash($senha, PASSWORD_DEFAULT);

        mysqli_query($conexao, "INSERT INTO usuarios (nm_usuario, email, senha)
                                VALUES ('$nome', '$email', '$senha_segura')");

        $id = mysqli_insert_id($conexao);

        mysqli_query($conexao, "INSERT INTO Contas (nm_conta, tp_conta, id_usuario)
                                VALUES ('Carteira', 'Carteira', $id)");

        mysqli_query($conexao, "INSERT INTO Categorias (nm_categoria, id_usuario) VALUES
                                ('Salário', $id),
                                ('Alimentação', $id),
                                ('Transporte', $id),
                                ('Moradia', $id),
                                ('Lazer', $id),
                                ('Outros', $id)");

        $_SESSION['id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
        header('Location: index.php');
        exit;
    }
}

$titulo = 'Criar conta';
include 'head.php';
?>
<body class="pagina-publica">
<div class="auth">
  <?php include 'auth_painel.php'; ?>

  <section class="auth-formulario">
    <div class="auth-caixa">
      <a class="marca" href="login.php"><?php include 'logo.php'; ?><span>livre</span></a>
      <h1>Criar conta</h1>
      <p class="subtitulo">Comece a descobrir quanto do seu dinheiro é livre.</p>

      <?php include 'mostra_erro.php'; ?>

      <form method="post">
        <div class="mb-3">
          <label class="form-label" for="nome">Nome<span class="obrigatorio">*</span></label>
          <input class="form-control" type="text" id="nome" name="nome" required autofocus>
        </div>

        <div class="mb-3">
          <label class="form-label" for="email">E-mail<span class="obrigatorio">*</span></label>
          <input class="form-control" type="email" id="email" name="email" required>
        </div>

        <div class="mb-3">
          <label class="form-label" for="senha">Senha<span class="obrigatorio">*</span></label>
          <input class="form-control" type="password" id="senha" name="senha" required>
        </div>

        <div class="mb-4">
          <label class="form-label" for="confirmacao">Confirme a senha<span class="obrigatorio">*</span></label>
          <input class="form-control" type="password" id="confirmacao" name="confirmacao" required>
        </div>

        <button class="btn btn-primary w-100" name="cadastrar">Criar conta</button>
      </form>

      <p class="auth-rodape">Já tem conta? <a href="login.php">Entrar</a></p>
    </div>
  </section>
</div>
</body>
</html>
