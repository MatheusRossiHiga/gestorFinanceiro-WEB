<?php
include 'conexao.php';
include 'verifica_login.php';
include 'calculos.php';

$de = date('Y-m-01');
$ate = date('Y-m-d');
if (isset($_GET['de']) && $_GET['de'] != '') {
    $de = $_GET['de'];
}
if (isset($_GET['ate']) && $_GET['ate'] != '') {
    $ate = $_GET['ate'];
}

$periodo = "AND Contas.id_usuario = $id_usuario AND st_transacao = 'C'
            AND DATE(dt_transacao) BETWEEN '$de' AND '$ate'";

$linha = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT IFNULL(SUM(vl_transacao), 0) AS total
        FROM Transacoes INNER JOIN Contas ON id_conta = cd_conta
        WHERE tp_transacao = 'E' $periodo"));
$entradas = $linha['total'];

$linha = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT IFNULL(SUM(vl_transacao), 0) AS total
        FROM Transacoes INNER JOIN Contas ON id_conta = cd_conta
        WHERE tp_transacao = 'S' $periodo"));
$saidas = $linha['total'];

$resultado = $entradas - $saidas;

$despesas = mysqli_query($conexao, "SELECT nm_categoria, SUM(vl_transacao) AS total
        FROM Transacoes
        INNER JOIN Contas ON id_conta = cd_conta
        INNER JOIN Categorias ON id_categoria = cd_categoria
        WHERE tp_transacao = 'S' $periodo
        GROUP BY cd_categoria, nm_categoria ORDER BY total DESC");

$receitas = mysqli_query($conexao, "SELECT nm_categoria, SUM(vl_transacao) AS total
        FROM Transacoes
        INNER JOIN Contas ON id_conta = cd_conta
        INNER JOIN Categorias ON id_categoria = cd_categoria
        WHERE tp_transacao = 'E' $periodo
        GROUP BY cd_categoria, nm_categoria ORDER BY total DESC");

$metas = mysqli_query($conexao, "SELECT * FROM Metas WHERE id_usuario = $id_usuario ORDER BY nm_meta");

$historico = mysqli_query($conexao, "SELECT * FROM Transacoes
        INNER JOIN Contas ON id_conta = cd_conta
        INNER JOIN Categorias ON id_categoria = cd_categoria
        WHERE 1 = 1 $periodo
        ORDER BY dt_transacao DESC LIMIT 10");

$titulo = 'Relatórios';
$pagina = 'relatorios';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Relatórios</h1>
    <p>Entenda para onde seu dinheiro está indo.</p>
  </div>
</div>

<form method="get" class="painel filtros mb-3">
  <div>
    <label class="form-label" for="de">De</label>
    <input class="form-control" type="date" id="de" name="de" value="<?php echo $de; ?>">
  </div>
  <div>
    <label class="form-label" for="ate">Até</label>
    <input class="form-control" type="date" id="ate" name="ate" value="<?php echo $ate; ?>">
  </div>
  <div class="filtros-rodape">
    <button class="btn btn-primary">Aplicar período</button>
  </div>
</form>

<div class="kpis mb-3">
  <div class="kpi"><small>Entradas no período</small><strong class="texto-entrada"><?php echo moeda($entradas); ?></strong></div>
  <div class="kpi"><small>Saídas no período</small><strong class="texto-saida"><?php echo moeda($saidas); ?></strong></div>
  <div class="kpi"><small>Resultado</small><strong class="<?php if ($resultado < 0) { echo 'texto-saida'; } else { echo 'texto-entrada'; } ?>"><?php echo moeda($resultado); ?></strong></div>
  <div class="kpi kpi-livre"><small>Taxa de poupança</small><strong><?php if ($entradas > 0) { echo round($resultado / $entradas * 100) . '%'; } else { echo '—'; } ?></strong></div>
</div>

<div class="grade grade-2b mb-3">
  <section class="painel">
    <div class="painel-cabecalho"><h2>Despesas por categoria</h2></div>
    <?php
      $consulta = $despesas;
      $rotulo_centro = 'Saídas';
      include 'rosca.php';
    ?>
  </section>
  <section class="painel">
    <div class="painel-cabecalho"><h2>Receitas por categoria</h2></div>
    <?php
      $consulta = $receitas;
      $rotulo_centro = 'Entradas';
      include 'rosca.php';
    ?>
  </section>
</div>

<div class="grade grade-2b mb-3">
  <section class="painel">
    <div class="painel-cabecalho"><h2>Composição do dinheiro livre</h2></div>
    <div class="caixa-explica">
      <div class="linha-conta"><span>Saldo nas contas</span><strong><?php echo moeda($saldo); ?></strong></div>
      <div class="linha-conta"><span>− Contas a pagar</span><strong><?php echo moeda($comprometido); ?></strong></div>
      <div class="linha-conta"><span>− Guardado em metas</span><strong><?php echo moeda($guardado); ?></strong></div>
      <div class="linha-conta total"><span>= Livre</span><strong class="<?php if ($livre < 0) { echo 'texto-saida'; } ?>"><?php echo moeda($livre); ?></strong></div>
    </div>
  </section>

  <section class="painel">
    <div class="painel-cabecalho"><h2>Evolução das metas</h2><a href="metas.php">Metas</a></div>
    <?php if (mysqli_num_rows($metas) == 0) { ?>
      <p class="texto-suave mb-0">Nenhuma meta cadastrada.</p>
    <?php } ?>
    <?php while ($m = mysqli_fetch_assoc($metas)) { ?>
      <?php
        $porcentagem = $m['vl_guardado'] / $m['vl_objetivo'] * 100;
        if ($porcentagem > 100) {
            $porcentagem = 100;
        }
      ?>
      <div class="mb-3">
        <div class="meta-numeros mb-1">
          <strong><?php echo $m['nm_meta']; ?></strong>
          <span><?php echo moeda($m['vl_guardado']); ?> / <?php echo moeda($m['vl_objetivo']); ?> · <strong><?php echo round($porcentagem); ?>%</strong></span>
        </div>
        <div class="progresso"><span style="width: <?php echo $porcentagem; ?>%"></span></div>
      </div>
    <?php } ?>
  </section>
</div>

<section class="painel">
  <div class="painel-cabecalho"><h2>Histórico do período</h2><a href="movimentacoes.php">Ver extrato completo</a></div>

  <?php if (mysqli_num_rows($historico) == 0) { ?>
    <p class="texto-suave mb-0">Sem movimentações neste período.</p>
  <?php } else { ?>
    <ul class="lista-itens">
      <?php while ($t = mysqli_fetch_assoc($historico)) { ?>
        <li>
          <span class="item-icone <?php if ($t['tp_transacao'] == 'E') { echo 'entrada'; } else { echo 'saida'; } ?>">
            <svg class="icone" viewBox="0 0 24 24"><path d="M7 4v16M7 4 3.5 7.5M7 4l3.5 3.5"/><path d="M17 20V4M17 20l-3.5-3.5M17 20l3.5-3.5"/></svg>
          </span>
          <div class="item-texto">
            <strong><?php echo $t['ds_descricao']; ?></strong>
            <small><?php echo date('d/m/Y', strtotime($t['dt_transacao'])); ?> · <?php echo $t['nm_categoria']; ?></small>
          </div>
          <div class="item-valor">
            <?php if ($t['tp_transacao'] == 'E') { ?>
              <span class="texto-entrada valor">+ <?php echo moeda($t['vl_transacao']); ?></span>
            <?php } else { ?>
              <span class="texto-saida valor">− <?php echo moeda($t['vl_transacao']); ?></span>
            <?php } ?>
          </div>
        </li>
      <?php } ?>
    </ul>
  <?php } ?>
</section>

<?php include 'footer.php'; ?>
