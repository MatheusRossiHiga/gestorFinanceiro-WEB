<?php
$inicial = mb_strtoupper(mb_substr($_SESSION['nome'], 0, 1));
?>
<div class="menu-usuario">
  <span class="avatar"><?php echo $inicial; ?></span>
  <div class="menu-usuario-dados">
    <strong><?php echo $_SESSION['nome']; ?></strong>
    <small><?php echo $_SESSION['email']; ?></small>
  </div>
  <a class="btn-icone" href="sair.php" title="Sair do LIVRE">
    <svg class="icone" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
  </a>
</div>
