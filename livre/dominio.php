<?php
include 'conexao.php';
include 'verifica_login.php';
include 'calculos.php';

$nome = '';
$valor = '';
$parcelas = 1;
$simulou = false;

if (isset($_GET['simular'])) {
    $nome = $_GET['nome'];
    $valor = $_GET['valor'];
    $parcelas = $_GET['parcelas'];

    if ($valor > 0 && $parcelas >= 1) {
        $simulou = true;

        $parcela = $valor / $parcelas;
        $restante = $valor - $parcela;

        $saldo_depois = $saldo - $parcela;
        $comprometido_depois = $comprometido + $restante;
        $livre_depois = $saldo_depois - $comprometido_depois - $guardado;
    }
}

$titulo = 'Efeito Dominó';
$pagina = 'dominio';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Efeito Dominó</h1>
    <p>Veja o que uma compra faz com seu dinheiro antes de fazê-la. Nada é registrado.</p>
  </div>
</div>

<div class="grade grade-2">
  <form method="get" class="painel">
    <div class="mb-3">
      <label class="form-label" for="nome">O que você quer comprar?<span class="obrigatorio">*</span></label>
      <input class="form-control" type="text" id="nome" name="nome" value="<?php echo $nome; ?>" placeholder="Ex.: Notebook novo" required>
    </div>

    <div class="mb-3">
      <label class="form-label" for="valor">Valor total<span class="obrigatorio">*</span></label>
      <div class="campo-moeda">
        <input class="form-control" type="number" step="0.01" id="valor" name="valor" value="<?php echo $valor; ?>" placeholder="0,00" required>
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label" for="parcelas">Parcelas</label>
      <input class="form-control" type="number" id="parcelas" name="parcelas" min="1" max="48" value="<?php echo $parcelas; ?>">
      <div class="form-text">1 = à vista.</div>
    </div>

    <button class="btn btn-primary w-100" name="simular" value="1">Simular</button>
  </form>

  <div>
    <?php if (!$simulou) { ?>
      <section class="painel">
        <div class="vazio">
          <h2>Preencha ao lado</h2>
          <p class="mb-0">Informe a compra para comparar sua situação de hoje com a situação depois dela.</p>
        </div>
      </section>
    <?php } else { ?>
      <section class="painel">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
          <h2 class="mb-0"><?php echo $nome; ?></h2>
          <span class="selo selo-pendente">Projeção</span>
        </div>

        <p class="texto-suave">
          <?php if ($parcelas == 1) { ?>
            À vista: <?php echo moeda($valor); ?>.
          <?php } else { ?>
            <?php echo $parcelas; ?> parcelas de <?php echo moeda($parcela); ?> (total <?php echo moeda($valor); ?>).
          <?php } ?>
        </p>

        <?php if ($livre_depois < 0) { ?>
          <div class="alert alert-danger alerta-livre">
            <svg class="icone" viewBox="0 0 24 24"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 10v4M12 17h0"/></svg>
            <div><strong>Seu dinheiro livre ficaria negativo</strong> em <?php echo moeda(-$livre_depois); ?>. Essa compra usaria dinheiro que já tem destino.</div>
          </div>
        <?php } else { ?>
          <div class="alert alert-success alerta-livre">
            <svg class="icone" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
            <div>A compra cabe no seu dinheiro livre sem comprometer contas nem metas.</div>
          </div>
        <?php } ?>

        <div class="tabela-envoltorio">
          <div class="table-responsive">
            <table class="table tabela-livre">
              <thead>
                <tr><th></th><th class="numero">Hoje</th><th class="numero">Após a compra</th></tr>
              </thead>
              <tbody>
                <tr><td>Saldo nas contas</td><td class="numero"><?php echo moeda($saldo); ?></td><td class="numero"><?php echo moeda($saldo_depois); ?></td></tr>
                <tr><td>Contas a pagar (comprometido)</td><td class="numero"><?php echo moeda($comprometido); ?></td><td class="numero"><?php echo moeda($comprometido_depois); ?></td></tr>
                <tr><td>Guardado em metas</td><td class="numero"><?php echo moeda($guardado); ?></td><td class="numero"><?php echo moeda($guardado); ?></td></tr>
                <tr class="table-warning"><td><strong>Dinheiro livre</strong></td><td class="numero"><?php echo moeda($livre); ?></td><td class="numero <?php if ($livre_depois < 0) { echo 'texto-saida'; } ?>"><?php echo moeda($livre_depois); ?></td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <?php if ($parcelas > 1) { ?>
          <p class="form-text mt-2 mb-0">Parcelado: só a 1ª parcela sai do saldo agora; as outras entram em "comprometido".</p>
        <?php } ?>
      </section>
    <?php } ?>
  </div>
</div>

<?php include 'footer.php'; ?>
