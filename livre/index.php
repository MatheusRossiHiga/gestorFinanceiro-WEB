<?php
include 'conexao.php';
include 'verifica_login.php';
include 'calculos.php';

$hoje = date('Y-m-d');
$mes = date('Y-m');

$sql = "SELECT IFNULL(SUM(vl_transacao), 0) AS total
        FROM Transacoes INNER JOIN Contas ON id_conta = cd_conta
        WHERE id_usuario = $id_usuario AND tp_transacao = 'E' AND st_transacao = 'C'
        AND DATE_FORMAT(dt_transacao, '%Y-%m') = '$mes'";
$linha = mysqli_fetch_assoc(mysqli_query($conexao, $sql));
$entradas = $linha['total'];

$sql = "SELECT IFNULL(SUM(vl_transacao), 0) AS total
        FROM Transacoes INNER JOIN Contas ON id_conta = cd_conta
        WHERE id_usuario = $id_usuario AND tp_transacao = 'S' AND st_transacao = 'C'
        AND DATE_FORMAT(dt_transacao, '%Y-%m') = '$mes'";
$linha = mysqli_fetch_assoc(mysqli_query($conexao, $sql));
$saidas = $linha['total'];

$resultado = $entradas - $saidas;

$nomes_meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
$maior = 0;

for ($i = 0; $i < 6; $i++) {
    $meses_atras = 5 - $i;
    $data = strtotime("-$meses_atras month", strtotime(date('Y-m-01')));
    $ano_mes = date('Y-m', $data);

    $rotulos[$i] = $nomes_meses[date('n', $data) - 1];

    $sql = "SELECT IFNULL(SUM(vl_transacao), 0) AS total
            FROM Transacoes INNER JOIN Contas ON id_conta = cd_conta
            WHERE id_usuario = $id_usuario AND tp_transacao = 'E' AND st_transacao = 'C'
            AND DATE_FORMAT(dt_transacao, '%Y-%m') = '$ano_mes'";
    $linha = mysqli_fetch_assoc(mysqli_query($conexao, $sql));
    $fluxo_entradas[$i] = $linha['total'];

    $sql = "SELECT IFNULL(SUM(vl_transacao), 0) AS total
            FROM Transacoes INNER JOIN Contas ON id_conta = cd_conta
            WHERE id_usuario = $id_usuario AND tp_transacao = 'S' AND st_transacao = 'C'
            AND DATE_FORMAT(dt_transacao, '%Y-%m') = '$ano_mes'";
    $linha = mysqli_fetch_assoc(mysqli_query($conexao, $sql));
    $fluxo_saidas[$i] = $linha['total'];

    if ($fluxo_entradas[$i] > $maior) {
        $maior = $fluxo_entradas[$i];
    }
    if ($fluxo_saidas[$i] > $maior) {
        $maior = $fluxo_saidas[$i];
    }
}

$despesas = mysqli_query($conexao, "SELECT nm_categoria, SUM(vl_transacao) AS total
        FROM Transacoes
        INNER JOIN Contas ON id_conta = cd_conta
        INNER JOIN Categorias ON id_categoria = cd_categoria
        WHERE Contas.id_usuario = $id_usuario AND tp_transacao = 'S' AND st_transacao = 'C'
        AND DATE_FORMAT(dt_transacao, '%Y-%m') = '$mes'
        GROUP BY cd_categoria, nm_categoria
        ORDER BY total DESC");

$ultimas = mysqli_query($conexao, "SELECT * FROM Transacoes
        INNER JOIN Contas ON id_conta = cd_conta
        INNER JOIN Categorias ON id_categoria = cd_categoria
        WHERE Contas.id_usuario = $id_usuario
        ORDER BY dt_transacao DESC, cd_transacoes DESC
        LIMIT 5");

$a_pagar = mysqli_query($conexao, "SELECT * FROM Transacoes
        INNER JOIN Contas ON id_conta = cd_conta
        WHERE id_usuario = $id_usuario AND tp_transacao = 'S' AND st_transacao = 'P'
        ORDER BY dt_transacao
        LIMIT 4");

$metas = mysqli_query($conexao, "SELECT * FROM Metas WHERE id_usuario = $id_usuario ORDER BY nm_meta LIMIT 3");

$hora = date('H');
if ($hora < 12) {
    $saudacao = 'Bom dia';
} else if ($hora < 18) {
    $saudacao = 'Boa tarde';
} else {
    $saudacao = 'Boa noite';
}
$partes = explode(' ', $_SESSION['nome']);
$primeiro_nome = $partes[0];

$titulo = 'Visão geral';
$pagina = 'inicio';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1><?php echo $saudacao; ?>, <?php echo $primeiro_nome; ?></h1>
    <p><?php echo date('d/m/Y'); ?></p>
  </div>
  <div class="pagina-acoes">
    <a class="btn btn-ouro" href="movimentacao_nova.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Nova movimentação
    </a>
  </div>
</div>

<section class="hero mb-3">
  <div class="hero-rotulo">Saldo atual nas contas</div>
  <div class="hero-valor <?php if ($saldo < 0) { echo 'negativo'; } ?>"><?php echo moeda($saldo); ?></div>
  <div class="hero-divisao">
    <div><small>Comprometido</small><strong>− <?php echo moeda($comprometido); ?></strong></div>
    <div><small>Em metas</small><strong>− <?php echo moeda($guardado); ?></strong></div>
    <div class="livre"><small>Livre</small><strong class="<?php if ($livre < 0) { echo 'negativo'; } ?>"><?php echo moeda($livre); ?></strong></div>
  </div>
  <details>
    <summary>Como o dinheiro livre é calculado</summary>
    <p>Livre = saldo das contas − contas a pagar pendentes − dinheiro guardado em metas.</p>
  </details>
</section>

<?php if ($livre < 0) { ?>
  <div class="alert alert-warning alerta-livre">
    <svg class="icone" viewBox="0 0 24 24"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 10v4M12 17h0"/></svg>
    <div><strong>Atenção:</strong> suas contas a pagar e metas somam <?php echo moeda(-$livre); ?> a mais do que o seu saldo.</div>
  </div>
<?php } ?>

<div class="kpis mb-3">
  <div class="kpi"><small><span class="ponto-cor" style="background: var(--entrada)"></span>Entradas do mês</small><strong class="texto-entrada"><?php echo moeda($entradas); ?></strong></div>
  <div class="kpi"><small><span class="ponto-cor" style="background: var(--saida)"></span>Saídas do mês</small><strong class="texto-saida"><?php echo moeda($saidas); ?></strong></div>
  <div class="kpi"><small>Resultado do mês</small><strong class="<?php if ($resultado < 0) { echo 'texto-saida'; } else { echo 'texto-entrada'; } ?>"><?php echo moeda($resultado); ?></strong></div>
  <div class="kpi kpi-livre"><small>Saldo livre</small><strong><?php echo moeda($livre); ?></strong></div>
</div>

<div class="grade grade-2 mb-3">
  <section class="painel">
    <div class="painel-cabecalho"><h2>Entradas e saídas</h2><a href="relatorios.php">Relatórios</a></div>

    <?php if ($maior == 0) { ?>
      <div class="grafico-vazio">Ainda não há movimentações concluídas neste período.</div>
    <?php } else { ?>
      <div class="grafico-barras">
        <?php for ($i = 0; $i < 6; $i++) { ?>
          <div class="barra-grupo">
            <div class="barra-par">
              <span class="barra barra-entrada" style="height: <?php echo $fluxo_entradas[$i] / $maior * 100; ?>%" title="Entradas: <?php echo moeda($fluxo_entradas[$i]); ?>"></span>
              <span class="barra barra-saida" style="height: <?php echo $fluxo_saidas[$i] / $maior * 100; ?>%" title="Saídas: <?php echo moeda($fluxo_saidas[$i]); ?>"></span>
            </div>
            <span class="barra-rotulo"><?php echo $rotulos[$i]; ?></span>
          </div>
        <?php } ?>
      </div>
      <div class="legenda">
        <span><i class="ponto-cor" style="background: var(--entrada)"></i>Entradas</span>
        <span><i class="ponto-cor" style="background: var(--saida)"></i>Saídas</span>
      </div>
    <?php } ?>
  </section>

  <section class="painel">
    <div class="painel-cabecalho"><h2>Despesas por categoria</h2></div>
    <?php
      $consulta = $despesas;
      $rotulo_centro = 'Saídas';
      include 'rosca.php';
    ?>
  </section>
</div>

<div class="grade grade-2b mb-3">
  <section class="painel">
    <div class="painel-cabecalho"><h2>Últimas movimentações</h2><a href="movimentacoes.php">Ver todas</a></div>

    <?php if (mysqli_num_rows($ultimas) == 0) { ?>
      <p class="texto-suave mb-0">Suas movimentações aparecem aqui.</p>
    <?php } else { ?>
      <ul class="lista-itens">
        <?php while ($t = mysqli_fetch_assoc($ultimas)) { ?>
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
              <?php if ($t['st_transacao'] == 'P') { ?><small>pendente</small><?php } ?>
            </div>
          </li>
        <?php } ?>
      </ul>
    <?php } ?>
  </section>

  <section class="painel">
    <div class="painel-cabecalho"><h2>Próximas contas a vencer</h2><a href="movimentacoes.php?status=P">Ver todas</a></div>

    <?php if (mysqli_num_rows($a_pagar) == 0) { ?>
      <p class="texto-suave mb-0">Nenhuma conta pendente. Para acompanhar uma conta futura, registre uma saída como "pendente".</p>
    <?php } else { ?>
      <ul class="lista-itens">
        <?php while ($t = mysqli_fetch_assoc($a_pagar)) { ?>
          <li>
            <span class="item-icone <?php if ($t['dt_transacao'] < $hoje) { echo 'saida'; } else { echo 'pendente'; } ?>">
              <svg class="icone" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
            </span>
            <div class="item-texto">
              <strong><?php echo $t['ds_descricao']; ?></strong>
              <small>Vence <?php echo date('d/m/Y', strtotime($t['dt_transacao'])); ?></small>
            </div>
            <div class="item-valor">
              <span class="valor"><?php echo moeda($t['vl_transacao']); ?></span>
              <?php if ($t['dt_transacao'] < $hoje) { ?>
                <span class="selo selo-atraso">Atrasada</span>
              <?php } else { ?>
                <span class="selo selo-pendente">Pendente</span>
              <?php } ?>
            </div>
          </li>
        <?php } ?>
      </ul>
    <?php } ?>
  </section>
</div>

<section class="painel mb-3">
  <div class="painel-cabecalho"><h2>Metas em andamento</h2><a href="metas.php">Ver metas</a></div>

  <?php if (mysqli_num_rows($metas) == 0) { ?>
    <p class="texto-suave mb-0">Você ainda não tem metas. <a href="meta_nova.php">Criar uma meta</a></p>
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
        <span><?php echo moeda($m['vl_guardado']); ?> de <?php echo moeda($m['vl_objetivo']); ?> · <strong><?php echo round($porcentagem); ?>%</strong></span>
      </div>
      <div class="progresso"><span style="width: <?php echo $porcentagem; ?>%"></span></div>
    </div>
  <?php } ?>
</section>

<nav class="atalhos">
  <a class="atalho" href="movimentacao_nova.php?tipo=S"><svg class="icone" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Registrar saída</a>
  <a class="atalho" href="movimentacao_nova.php?tipo=E"><svg class="icone" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Registrar entrada</a>
  <a class="atalho" href="dominio.php"><svg class="icone" viewBox="0 0 24 24"><path d="m12 3-9 5 9 5 9-5-9-5Z"/><path d="m3 13 9 5 9-5"/></svg>Simular uma compra</a>
  <a class="atalho" href="relatorios.php"><svg class="icone" viewBox="0 0 24 24"><path d="M4 20h16M7 20v-7M12 20V5M17 20v-10"/></svg>Ver relatórios</a>
</nav>

<?php include 'footer.php'; ?>
