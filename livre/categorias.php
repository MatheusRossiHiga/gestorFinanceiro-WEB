<?php
include 'conexao.php';
include 'verifica_login.php';

$erro = '';

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    $resultado = mysqli_query($conexao, "SELECT * FROM Transacoes WHERE id_categoria = $id");

    if (mysqli_num_rows($resultado) > 0) {
        $erro = 'Esta categoria tem movimentações e não pode ser excluída.';
    } else {
        mysqli_query($conexao, "DELETE FROM Categorias WHERE cd_categoria = $id AND id_usuario = $id_usuario");
        header('Location: categorias.php');
        exit;
    }
}

$categorias = mysqli_query($conexao, "SELECT Categorias.*, COUNT(cd_transacoes) AS qtd
        FROM Categorias
        LEFT JOIN Transacoes ON id_categoria = cd_categoria
        WHERE id_usuario = $id_usuario
        GROUP BY cd_categoria
        ORDER BY nm_categoria");

$titulo = 'Categorias';
$pagina = 'categorias';
include 'header.php';
?>

<div class="pagina-cabecalho">
  <div>
    <h1>Categorias</h1>
    <p>Organize para onde o dinheiro vai e de onde ele vem.</p>
  </div>
  <div class="pagina-acoes">
    <a class="btn btn-primary" href="categoria_nova.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Nova categoria
    </a>
  </div>
</div>

<?php include 'mostra_erro.php'; ?>

<section class="grupo-lista">
  <div class="tabela-envoltorio">
    <?php while ($k = mysqli_fetch_assoc($categorias)) { ?>
      <div class="cat-linha">
        <div class="nome"><?php echo $k['nm_categoria']; ?><small><?php echo $k['qtd']; ?> movimentação(ões)</small></div>
        <a class="btn-icone btn-perigo" href="categorias.php?excluir=<?php echo $k['cd_categoria']; ?>"
           onclick="return confirm('Excluir esta categoria?')" title="Excluir">
          <svg class="icone" viewBox="0 0 24 24"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
        </a>
      </div>
    <?php } ?>
  </div>
</section>

<?php include 'footer.php'; ?>
