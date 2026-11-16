<?php
include 'conexao.php';
include 'verifica_login.php';

$erro = '';

if (isset($_GET['pagar'])) {
    $id = $_GET['pagar'];

    $resultado = mysqli_query($conexao, "SELECT * FROM Transacoes WHERE cd_transacoes = $id");
    $t = mysqli_fetch_assoc($resultado);

    if ($t['st_transacao'] == 'P') {
        $valor = $t['vl_transacao'];
        $conta = $t['id_conta'];

        if ($t['tp_transacao'] == 'E') {
            $sinal = '+';
        } else {
            $sinal = '-';
        }

        mysqli_query($conexao, "UPDATE Contas SET vl_conta_atual = vl_conta_atual $sinal $valor WHERE cd_conta = $conta");
        mysqli_query($conexao, "UPDATE Transacoes SET st_transacao = 'C', dt_transacao = NOW() WHERE cd_transacoes = $id");
    }

    header('Location: movimentacoes.php');
    exit;
}

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    $resultado = mysqli_query($conexao, "SELECT * FROM Transacoes WHERE cd_transacoes = $id");
    $t = mysqli_fetch_assoc($resultado);

    if ($t['st_transacao'] == 'C') {
        $valor = $t['vl_transacao'];
        $conta = $t['id_conta'];

        if ($t['tp_transacao'] == 'E') {
            $sinal = '-';
        } else {
            $sinal = '+';
        }

        mysqli_query($conexao, "UPDATE Contas SET vl_conta_atual = vl_conta_atual $sinal $valor WHERE cd_conta = $conta");
    }

    mysqli_query($conexao, "DELETE FROM Transacoes WHERE cd_transacoes = $id");
    header('Location: movimentacoes.php');
    exit;
}

$tipo = '';
$status = '';
$conta_filtro = '';
if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];
}
if (isset($_GET['status'])) {
    $status = $_GET['status'];
}
if (isset($_GET['conta'])) {
    $conta_filtro = $_GET['conta'];
}

$filtro = '';
if ($tipo != '') {
    $filtro = $filtro . " AND tp_transacao = '$tipo'";
}
if ($status != '') {
    $filtro = $filtro . " AND st_transacao = '$status'";
}
if ($conta_filtro != '') {
    $filtro = $filtro . " AND id_conta = $conta_filtro";
}

$contas = mysqli_query($conexao, "SELECT * FROM Contas WHERE id_usuario = $id_usuario ORDER BY nm_conta");

$movimentacoes = mysqli_query($conexao, "SELECT * FROM Transacoes
        INNER JOIN Contas ON id_conta = cd_conta
        INNER JOIN Categorias ON id_categoria = cd_categoria
        WHERE Contas.id_usuario = $id_usuario $filtro
        ORDER BY dt_transacao DESC, cd_transacoes DESC
        LIMIT 100");

$titulo = 'Movimentações';
$pagina = 'movimentacoes';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Movimentações</h1>
    <p>Tudo o que entrou e saiu do seu dinheiro.</p>
  </div>
  <div class="pagina-acoes">
    <a class="btn btn-ouro" href="movimentacao_nova.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Nova movimentação
    </a>
  </div>
</div>

<?php include 'mostra_erro.php'; ?>

<form method="get" class="painel filtros mb-3">
  <div>
    <label class="form-label" for="tipo">Tipo</label>
    <select class="form-select" id="tipo" name="tipo">
      <option value="">Todos</option>
      <option value="E" <?php if ($tipo == 'E') { echo 'selected'; } ?>>Entradas</option>
      <option value="S" <?php if ($tipo == 'S') { echo 'selected'; } ?>>Saídas</option>
    </select>
  </div>
  <div>
    <label class="form-label" for="status">Situação</label>
    <select class="form-select" id="status" name="status">
      <option value="">Todas</option>
      <option value="C" <?php if ($status == 'C') { echo 'selected'; } ?>>Concluídas</option>
      <option value="P" <?php if ($status == 'P') { echo 'selected'; } ?>>Pendentes</option>
    </select>
  </div>
  <div>
    <label class="form-label" for="conta">Conta</label>
    <select class="form-select" id="conta" name="conta">
      <option value="">Todas</option>
      <?php while ($c = mysqli_fetch_assoc($contas)) { ?>
        <option value="<?php echo $c['cd_conta']; ?>" <?php if ($conta_filtro == $c['cd_conta']) { echo 'selected'; } ?>><?php echo $c['nm_conta']; ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="filtros-rodape">
    <button class="btn btn-primary">
      <svg class="icone" viewBox="0 0 24 24"><path d="M3 5h18l-7 8v6l-4-2v-4L3 5Z"/></svg>Filtrar
    </button>
    <a class="btn btn-outline-secondary" href="movimentacoes.php">Limpar</a>
  </div>
</form>

<?php if (mysqli_num_rows($movimentacoes) == 0) { ?>
  <section class="painel">
    <div class="vazio">
      <h2>Nenhuma movimentação encontrada</h2>
      <p class="mb-0">Registre sua primeira entrada ou saída para começar a acompanhar seu dinheiro.</p>
      <a class="btn btn-primary" href="movimentacao_nova.php">Registrar movimentação</a>
    </div>
  </section>
<?php } else { ?>
  <div class="tabela-envoltorio">
    <div class="table-responsive">
      <table class="table tabela-livre">
        <thead>
          <tr>
            <th>Descrição</th>
            <th>Categoria</th>
            <th>Data</th>
            <th>Conta</th>
            <th>Situação</th>
            <th class="numero">Valor</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php while ($t = mysqli_fetch_assoc($movimentacoes)) { ?>
            <tr>
              <td><?php echo $t['ds_descricao']; ?></td>
              <td><?php echo $t['nm_categoria']; ?></td>
              <td><?php echo date('d/m/Y', strtotime($t['dt_transacao'])); ?></td>
              <td><?php echo $t['nm_conta']; ?></td>
              <td>
                <?php if ($t['st_transacao'] == 'C') { ?>
                  <span class="selo selo-ok"><?php if ($t['tp_transacao'] == 'E') { echo 'Recebida'; } else { echo 'Paga'; } ?></span>
                <?php } else if ($t['dt_transacao'] < date('Y-m-d')) { ?>
                  <span class="selo selo-atraso">Atrasada</span>
                <?php } else { ?>
                  <span class="selo selo-pendente">Pendente</span>
                <?php } ?>
              </td>
              <td class="numero">
                <?php if ($t['tp_transacao'] == 'E') { ?>
                  <span class="texto-entrada valor">+ <?php echo moeda($t['vl_transacao']); ?></span>
                <?php } else { ?>
                  <span class="texto-saida valor">− <?php echo moeda($t['vl_transacao']); ?></span>
                <?php } ?>
              </td>
              <td>
                <div class="acoes-linha">
                  <?php if ($t['st_transacao'] == 'P') { ?>
                    <a class="btn-icone" href="movimentacoes.php?pagar=<?php echo $t['cd_transacoes']; ?>" title="Marcar como concluída">
                      <svg class="icone" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                    </a>
                  <?php } ?>
                  <a class="btn-icone btn-perigo" href="movimentacoes.php?excluir=<?php echo $t['cd_transacoes']; ?>"
                     onclick="return confirm('Excluir esta movimentação?')" title="Excluir">
                    <svg class="icone" viewBox="0 0 24 24"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                  </a>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
<?php } ?>

<?php include 'footer.php'; ?>
