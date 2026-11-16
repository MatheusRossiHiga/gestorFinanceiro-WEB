<?php
include 'conexao.php';
include 'verifica_login.php';

$erro = '';

if (isset($_POST['salvar'])) {
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $saldo = $_POST['saldo'];

    if ($nome == '') {
        $erro = 'Informe o nome da conta.';
    } else {
        if ($saldo == '') {
            $saldo = 0;
        }
        mysqli_query($conexao, "INSERT INTO Contas (nm_conta, tp_conta, vl_conta_atual, id_usuario)
                                VALUES ('$nome', '$tipo', $saldo, $id_usuario)");
        header('Location: contas.php');
        exit;
    }
}

$titulo = 'Nova conta';
$pagina = 'contas';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Nova conta</h1>
    <p>Cadastre onde seu dinheiro fica.</p>
  </div>
</div>

<div class="formulario">
  <?php include 'mostra_erro.php'; ?>

  <form method="post" class="painel">
    <div class="mb-3">
      <label class="form-label" for="nome">Nome da conta<span class="obrigatorio">*</span></label>
      <input class="form-control" type="text" id="nome" name="nome" placeholder="Ex.: Nubank, Carteira" required>
    </div>

    <div class="mb-3">
      <label class="form-label" for="tipo">Tipo<span class="obrigatorio">*</span></label>
      <select class="form-select" id="tipo" name="tipo">
        <option>Carteira</option>
        <option>Conta corrente</option>
        <option>Conta poupança</option>
        <option>Carteira digital</option>
        <option>Investimentos</option>
      </select>
    </div>

    <div class="mb-4">
      <label class="form-label" for="saldo">Saldo atual</label>
      <div class="campo-moeda">
        <input class="form-control" type="number" step="0.01" id="saldo" name="saldo" placeholder="0,00">
      </div>
      <div class="form-text">Deixe em branco se a conta começa zerada.</div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <button class="btn btn-primary" name="salvar">Salvar</button>
      <a class="btn btn-outline-secondary" href="contas.php">Cancelar</a>
    </div>
  </form>
</div>

<?php include 'footer.php'; ?>
