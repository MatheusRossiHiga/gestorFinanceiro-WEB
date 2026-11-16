<?php
include 'conexao.php';
include 'verifica_login.php';

$erro = '';

if (isset($_POST['salvar'])) {
    $nome = $_POST['nome'];

    if ($nome == '') {
        $erro = 'Informe o nome da categoria.';
    } else {
        mysqli_query($conexao, "INSERT INTO Categorias (nm_categoria, id_usuario) VALUES ('$nome', $id_usuario)");
        header('Location: categorias.php');
        exit;
    }
}

$titulo = 'Nova categoria';
$pagina = 'categorias';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Nova categoria</h1>
    <p>Crie uma categoria para classificar suas movimentações.</p>
  </div>
</div>

<div class="formulario">
  <?php include 'mostra_erro.php'; ?>

  <form method="post" class="painel">
    <div class="mb-4">
      <label class="form-label" for="nome">Nome<span class="obrigatorio">*</span></label>
      <input class="form-control" type="text" id="nome" name="nome" placeholder="Ex.: Mercado, Streaming" required autofocus>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <button class="btn btn-primary" name="salvar">Salvar</button>
      <a class="btn btn-outline-secondary" href="categorias.php">Cancelar</a>
    </div>
  </form>
</div>

<?php include 'footer.php'; ?>
