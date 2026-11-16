<?php
include 'conexao.php';
include 'verifica_login.php';

$erro = '';

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    $resultado = mysqli_query($conexao, "SELECT * FROM Transacoes WHERE id_conta = $id");

    if (mysqli_num_rows($resultado) > 0) {
        $erro = 'Esta conta tem movimentações e não pode ser excluída.';
    } else {
        mysqli_query($conexao, "DELETE FROM Contas WHERE cd_conta = $id AND id_usuario = $id_usuario");
        header('Location: contas.php');
        exit;
    }
}

$contas = mysqli_query($conexao, "SELECT Contas.*, COUNT(cd_transacoes) AS qtd
        FROM Contas
        LEFT JOIN Transacoes ON id_conta = cd_conta
        WHERE id_usuario = $id_usuario
        GROUP BY cd_conta
        ORDER BY nm_conta");

$total = 0;
$lista = [];
while ($c = mysqli_fetch_assoc($contas)) {
    $lista[] = $c;
    $total = $total + $c['vl_conta_atual'];
}

$titulo = 'Contas';
$pagina = 'contas';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Contas</h1>
    <p>Onde seu dinheiro está guardado.</p>
  </div>
  <div class="pagina-acoes">
    <a class="btn btn-primary" href="conta_nova.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Nova conta
    </a>
  </div>
</div>

<?php include 'mostra_erro.php'; ?>

<?php if (count($lista) == 0) { ?>
  <section class="painel">
    <div class="vazio">
      <h2>Nenhuma conta ainda</h2>
      <p class="mb-0">Cadastre uma conta (carteira, banco, poupança) para registrar suas movimentações.</p>
      <a class="btn btn-primary" href="conta_nova.php">Criar conta</a>
    </div>
  </section>
<?php } else { ?>
  <div class="faixa-total">
    <div><small>Saldo total das contas</small><strong><?php echo moeda($total); ?></strong></div>
  </div>

  <div class="cartoes">
    <?php foreach ($lista as $c) { ?>
      <article class="cartao-conta">
        <div class="cartao-topo">
          <div>
            <h3><?php echo $c['nm_conta']; ?></h3>
            <span class="texto-suave"><?php echo $c['tp_conta']; ?></span>
          </div>
        </div>
        <div class="cartao-valor <?php if ($c['vl_conta_atual'] < 0) { echo 'negativo'; } ?>"><?php echo moeda($c['vl_conta_atual']); ?></div>
        <small class="texto-suave"><?php echo $c['qtd']; ?> movimentação(ões)</small>
        <div class="cartao-rodape">
          <a class="btn-icone btn-perigo" href="contas.php?excluir=<?php echo $c['cd_conta']; ?>"
             onclick="return confirm('Excluir esta conta?')" title="Excluir">
            <svg class="icone" viewBox="0 0 24 24"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
          </a>
        </div>
      </article>
    <?php } ?>
  </div>
<?php } ?>

<?php include 'footer.php'; ?>
