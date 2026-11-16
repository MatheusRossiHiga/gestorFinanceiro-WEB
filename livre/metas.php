<?php
include 'conexao.php';
include 'verifica_login.php';
include 'calculos.php';

$erro = '';

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    mysqli_query($conexao, "DELETE FROM Metas WHERE cd_meta = $id AND id_usuario = $id_usuario");
    header('Location: metas.php');
    exit;
}

if (isset($_POST['guardar']) || isset($_POST['retirar'])) {
    $id = $_POST['id'];
    $valor = $_POST['valor'];

    $resultado = mysqli_query($conexao, "SELECT * FROM Metas WHERE cd_meta = $id");
    $meta = mysqli_fetch_assoc($resultado);

    if ($valor <= 0) {
        $erro = 'Informe um valor maior que zero.';
    } else if (isset($_POST['guardar'])) {
        if ($valor > $livre) {
            $erro = 'Você só tem ' . moeda($livre) . ' livres para guardar.';
        } else {
            mysqli_query($conexao, "UPDATE Metas SET vl_guardado = vl_guardado + $valor WHERE cd_meta = $id");
            header('Location: metas.php');
            exit;
        }
    } else {
        if ($valor > $meta['vl_guardado']) {
            $erro = 'Essa meta só tem ' . moeda($meta['vl_guardado']) . ' guardados.';
        } else {
            mysqli_query($conexao, "UPDATE Metas SET vl_guardado = vl_guardado - $valor WHERE cd_meta = $id");
            header('Location: metas.php');
            exit;
        }
    }
}

$metas = mysqli_query($conexao, "SELECT * FROM Metas WHERE id_usuario = $id_usuario ORDER BY nm_meta");

$titulo = 'Metas';
$pagina = 'metas';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Metas</h1>
    <p>Separe dinheiro para o que importa.</p>
  </div>
  <div class="pagina-acoes">
    <a class="btn btn-ouro" href="meta_nova.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Nova meta
    </a>
  </div>
</div>

<?php include 'mostra_erro.php'; ?>

<div class="faixa-total">
  <div><small>Reservado em metas</small><strong><?php echo moeda($guardado); ?></strong></div>
  <div class="text-end"><small>Dinheiro livre agora</small><strong><?php echo moeda($livre); ?></strong></div>
</div>

<div class="caixa-explica mb-3">Guardar em uma meta <strong>não tira o dinheiro da conta</strong>: ele continua no saldo, mas deixa de contar como dinheiro livre. Por isso só é possível guardar até o valor livre.</div>

<?php if (mysqli_num_rows($metas) == 0) { ?>
  <section class="painel">
    <div class="vazio">
      <h2>Nenhuma meta ainda</h2>
      <p class="mb-0">Crie uma meta (viagem, reserva de emergência, computador novo) e vá guardando aos poucos.</p>
      <a class="btn btn-primary" href="meta_nova.php">Criar meta</a>
    </div>
  </section>
<?php } else { ?>
  <div class="cartoes">
    <?php while ($m = mysqli_fetch_assoc($metas)) { ?>
      <?php
        $faltam = $m['vl_objetivo'] - $m['vl_guardado'];
        if ($faltam < 0) {
            $faltam = 0;
        }
        $porcentagem = $m['vl_guardado'] / $m['vl_objetivo'] * 100;
        if ($porcentagem > 100) {
            $porcentagem = 100;
        }
      ?>
      <article class="cartao-meta">
        <div class="cartao-topo">
          <h3><?php echo $m['nm_meta']; ?></h3>
          <?php if ($m['vl_guardado'] >= $m['vl_objetivo']) { ?>
            <span class="selo selo-ok">Atingida</span>
          <?php } else { ?>
            <span class="selo selo-pendente">Em andamento</span>
          <?php } ?>
        </div>

        <div class="cartao-valor"><?php echo moeda($m['vl_guardado']); ?> <small class="texto-suave fs-6 fw-normal">de <?php echo moeda($m['vl_objetivo']); ?></small></div>

        <div class="progresso"><span style="width: <?php echo $porcentagem; ?>%"></span></div>

        <div class="meta-numeros">
          <span><strong><?php echo round($porcentagem); ?>%</strong> concluído</span>
          <span>Faltam <strong><?php echo moeda($faltam); ?></strong></span>
        </div>

        <small class="texto-suave">
          <?php if ($m['dt_limite']) { ?>Prazo: <?php echo date('d/m/Y', strtotime($m['dt_limite'])); ?><?php } else { ?>Sem prazo definido<?php } ?>
        </small>

        <details>
          <summary>Guardar ou retirar dinheiro</summary>
          <form method="post" class="mt-2">
            <input type="hidden" name="id" value="<?php echo $m['cd_meta']; ?>">
            <label class="form-label" for="v<?php echo $m['cd_meta']; ?>">Valor</label>
            <div class="campo-moeda mb-2">
              <input class="form-control" type="number" step="0.01" id="v<?php echo $m['cd_meta']; ?>" name="valor" placeholder="0,00" required>
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <button class="btn btn-sm btn-primary" name="guardar">Guardar</button>
              <button class="btn btn-sm btn-outline-secondary" name="retirar">Retirar</button>
            </div>
          </form>
        </details>

        <div class="cartao-rodape">
          <a class="btn-icone btn-perigo" href="metas.php?excluir=<?php echo $m['cd_meta']; ?>"
             onclick="return confirm('Excluir esta meta?')" title="Excluir">
            <svg class="icone" viewBox="0 0 24 24"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
          </a>
        </div>
      </article>
    <?php } ?>
  </div>
<?php } ?>

<?php include 'footer.php'; ?>
