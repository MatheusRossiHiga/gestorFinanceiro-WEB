<?php
if ($erro != '') { ?>
  <div class="alert alert-danger alerta-livre">
    <svg class="icone" viewBox="0 0 24 24"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 10v4M12 17h0"/></svg>
    <div><?php echo $erro; ?></div>
  </div>
<?php } ?>
