<?php
include 'conexao.php';
include 'verifica_login.php';

$erro = '';

$tipo = 'S';
if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];
}

if (isset($_POST['salvar'])) {
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $data = $_POST['data'];
    $conta = $_POST['conta'];
    $categoria = $_POST['categoria'];
    $status = $_POST['status'];

    if ($descricao == '' || $valor <= 0) {
        $erro = 'Preencha a descrição e um valor maior que zero.';
    } else {
        mysqli_query($conexao, "INSERT INTO Transacoes
                                (ds_descricao, vl_transacao, tp_transacao, dt_transacao, st_transacao, id_conta, id_categoria)
                                VALUES ('$descricao', $valor, '$tipo', '$data', '$status', $conta, $categoria)");

        if ($status == 'C') {
            if ($tipo == 'E') {
                $sinal = '+';
            } else {
                $sinal = '-';
            }
            mysqli_query($conexao, "UPDATE Contas SET vl_conta_atual = vl_conta_atual $sinal $valor WHERE cd_conta = $conta");
        }

        header('Location: movimentacoes.php');
        exit;
    }
}

$contas = mysqli_query($conexao, "SELECT * FROM Contas WHERE id_usuario = $id_usuario ORDER BY nm_conta");
$categorias = mysqli_query($conexao, "SELECT * FROM Categorias WHERE id_usuario = $id_usuario ORDER BY nm_categoria");

$titulo = 'Nova movimentação';
$pagina = 'movimentacoes';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Nova movimentação</h1>
    <p>Registre uma entrada ou uma saída de dinheiro.</p>
  </div>
</div>

<div class="formulario">
  <?php include 'mostra_erro.php'; ?>

  <?php if (mysqli_num_rows($contas) == 0) { ?>
    <div class="alert alert-warning alerta-livre">
      <svg class="icone" viewBox="0 0 24 24"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 10v4M12 17h0"/></svg>
      <div>Você precisa de uma conta para registrar movimentações. <a href="conta_nova.php">Criar conta</a></div>
    </div>
  <?php } ?>

  <form method="post" class="painel">
    <div class="mb-3">
      <span class="form-label d-block">Tipo<span class="obrigatorio">*</span></span>
      <div class="seg">
        <input type="radio" name="tipo" id="tipo_s" value="S" <?php if ($tipo == 'S') { echo 'checked'; } ?>><label for="tipo_s">Saída</label>
        <input type="radio" name="tipo" id="tipo_e" value="E" <?php if ($tipo == 'E') { echo 'checked'; } ?>><label for="tipo_e">Entrada</label>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label" for="valor">Valor<span class="obrigatorio">*</span></label>
      <div class="campo-moeda campo-moeda-grande">
        <input class="form-control" type="number" step="0.01" id="valor" name="valor" placeholder="0,00" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label" for="descricao">Descrição<span class="obrigatorio">*</span></label>
      <input class="form-control" type="text" id="descricao" name="descricao" placeholder="Ex.: Mercado, Salário de outubro" required>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label" for="categoria">Categoria<span class="obrigatorio">*</span></label>
        <select class="form-select" id="categoria" name="categoria" required>
          <?php while ($c = mysqli_fetch_assoc($categorias)) { ?>
            <option value="<?php echo $c['cd_categoria']; ?>"><?php echo $c['nm_categoria']; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="conta">Conta<span class="obrigatorio">*</span></label>
        <select class="form-select" id="conta" name="conta" required>
          <?php while ($c = mysqli_fetch_assoc($contas)) { ?>
            <option value="<?php echo $c['cd_conta']; ?>"><?php echo $c['nm_conta']; ?></option>
          <?php } ?>
        </select>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label" for="data">Data<span class="obrigatorio">*</span></label>
        <input class="form-control" type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
      </div>
      <div class="col-md-6">
        <span class="form-label d-block">Situação<span class="obrigatorio">*</span></span>
        <div class="seg">
          <input type="radio" name="status" id="st_c" value="C" checked><label for="st_c">Concluída</label>
          <input type="radio" name="status" id="st_p" value="P"><label for="st_p">Pendente</label>
        </div>
      </div>
    </div>

    <div class="form-text mb-4">Concluída: o dinheiro já entrou ou saiu e muda o saldo. Pendente: é uma conta a pagar ou a receber.</div>

    <div class="d-flex gap-2 flex-wrap">
      <button class="btn btn-primary" name="salvar">Salvar</button>
      <a class="btn btn-outline-secondary" href="movimentacoes.php">Cancelar</a>
    </div>
  </form>
</div>

<?php include 'footer.php'; ?>
