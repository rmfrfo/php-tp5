<?php
$msg = null;

if ($alertError) {
  //var_dump($alertError);
  $msg = $alertError;
  $class = 'danger';
}

if ($alertSuccess) {
  $msg = $alertSuccess;
  $class = 'success';
}

?>
<?php if ($msg) { ?>
  <div class="alert alert-<?= $class; ?>" role="alert">
    <?= $msg; ?>
  </div>
<?php } ?>