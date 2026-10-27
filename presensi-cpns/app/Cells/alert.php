<?php
$dismissClass = $dismissible ? 'alert-dismissible fade show' : '';
?>

<div class="alert alert-<?= esc($type) ?> <?= $dismissClass ?>" role="alert">
  <p class="text-danger m-0"><?= esc($message) ?></p>
  <?php if ($dismissible) : ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  <?php endif; ?>
</div>