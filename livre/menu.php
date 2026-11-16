<?php
?>
<nav class="menu">
  <ul class="menu-grupo">
    <li><a class="menu-item <?php if ($pagina == 'inicio') { echo 'ativo'; } ?>" href="index.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20h14V9.5"/><path d="M10 20v-6h4v6"/></svg>
      <span>Visão geral</span></a></li>
    <li><a class="menu-item <?php if ($pagina == 'movimentacoes') { echo 'ativo'; } ?>" href="movimentacoes.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M7 4v16M7 4 3.5 7.5M7 4l3.5 3.5"/><path d="M17 20V4M17 20l-3.5-3.5M17 20l3.5-3.5"/></svg>
      <span>Movimentações</span></a></li>
    <li><a class="menu-item <?php if ($pagina == 'contas') { echo 'ativo'; } ?>" href="contas.php">
      <svg class="icone" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="13" rx="2.5"/><path d="M3 10h18M16 14.5h2"/></svg>
      <span>Contas</span></a></li>
    <li><a class="menu-item <?php if ($pagina == 'categorias') { echo 'ativo'; } ?>" href="categorias.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8Z"/><circle cx="7.5" cy="7.5" r="1"/></svg>
      <span>Categorias</span></a></li>
  </ul>
  <ul class="menu-grupo">
    <li><a class="menu-item <?php if ($pagina == 'metas') { echo 'ativo'; } ?>" href="metas.php">
      <svg class="icone" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg>
      <span>Metas</span></a></li>
    <li><a class="menu-item <?php if ($pagina == 'dominio') { echo 'ativo'; } ?>" href="dominio.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="m12 3-9 5 9 5 9-5-9-5Z"/><path d="m3 13 9 5 9-5"/></svg>
      <span>Efeito Dominó</span></a></li>
  </ul>
  <ul class="menu-grupo">
    <li><a class="menu-item <?php if ($pagina == 'relatorios') { echo 'ativo'; } ?>" href="relatorios.php">
      <svg class="icone" viewBox="0 0 24 24"><path d="M4 20h16M7 20v-7M12 20V5M17 20v-10"/></svg>
      <span>Relatórios</span></a></li>
  </ul>
</nav>
