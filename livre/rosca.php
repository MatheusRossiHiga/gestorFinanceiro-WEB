<?php

$cores = ['#146B49', '#D4A62A', '#C4452D', '#1E8A5E', '#B8860B', '#5B6B61', '#0F5238', '#EBD08A'];

$itens = [];
$total_rosca = 0;
while ($linha = mysqli_fetch_assoc($consulta)) {
    $itens[] = $linha;
    $total_rosca = $total_rosca + $linha['total'];
}

$gradiente = '';
$inicio = 0;
foreach ($itens as $posicao => $item) {
    $porcentagem = $item['total'] / $total_rosca * 100;
    $fim = $inicio + $porcentagem;
    $cor = $cores[$posicao % 8];

    $gradiente = $gradiente . $cor . ' ' . $inicio . '% ' . $fim . '%, ';
    $itens[$posicao]['cor'] = $cor;
    $itens[$posicao]['porcentagem'] = $porcentagem;

    $inicio = $fim;
}
$gradiente = rtrim($gradiente, ', ');

if (count($itens) == 0) { ?>
  <div class="grafico-vazio">Nenhum valor neste período.</div>
<?php } else { ?>
  <div class="rosca-bloco">
    <div class="rosca" style="background: conic-gradient(<?php echo $gradiente; ?>)">
      <div class="rosca-centro">
        <small><?php echo $rotulo_centro; ?></small>
        <strong><?php echo moeda($total_rosca); ?></strong>
      </div>
    </div>
    <ul class="rosca-legenda">
      <?php foreach ($itens as $item) { ?>
        <li>
          <span class="ponto-cor" style="background: <?php echo $item['cor']; ?>"></span>
          <span class="leg-nome"><?php echo $item['nm_categoria']; ?></span>
          <span class="leg-valor"><?php echo moeda($item['total']); ?> <small><?php echo round($item['porcentagem']); ?>%</small></span>
        </li>
      <?php } ?>
    </ul>
  </div>
<?php } ?>
