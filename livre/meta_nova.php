<?php
include 'conexao.php';
include 'verifica_login.php';

$erro = '';

if (isset($_POST['salvar'])) {
    $nome = $_POST['nome'];
    $objetivo = $_POST['objetivo'];
    $prazo = $_POST['prazo'];

    if ($nome == '' || $objetivo <= 0) {
        $erro = 'Informe o nome e um valor objetivo maior que zero.';
    } else {
        if ($prazo == '') {
            $prazo = 'NULL';
        } else {
            $prazo = "'$prazo'";
        }

        mysqli_query($conexao, "INSERT INTO Metas (nm_meta, vl_objetivo, dt_limite, id_usuario)
                                VALUES ('$nome', $objetivo, $prazo, $id_usuario)");
        header('Location: metas.php');
        exit;
    }
}

$titulo = 'Nova meta';
$pagina = 'metas';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Nova meta</h1>
    <p>Defina quanto você quer juntar e para quê.</p>
  </div>
</div>

<div class="formulario">
  <?php include 'mostra_erro.php'; ?>

  <form method="post" class="painel">
    <div class="mb-3">
      <label class="form-label" for="nome">Nome da meta<span class="obrigatorio">*</span></label>
      <input class="form-control" type="text" id="nome" name="nome" placeholder="Ex.: Viagem, Reserva de emergência" required>
    </div>

    <div class="mb-3">
      <label class="form-label" for="objetivo">Valor do objetivo<span class="obrigatorio">*</span></label>
      <div class="campo-moeda">
        <input class="form-control" type="number" step="0.01" id="objetivo" name="objetivo" placeholder="0,00" required>
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label" for="prazo">Prazo (opcional)</label>
      <input class="form-control" type="date" id="prazo" name="prazo">
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <button class="btn btn-primary" name="salvar">Salvar</button>
      <a class="btn btn-outline-secondary" href="metas.php">Cancelar</a>
    </div>
  </form>
</div>

<?php include 'footer.php'; ?>
