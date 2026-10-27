<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title><?= $this->renderSection('title', true) ?></title>
  <?= $this->include('_templates/css'); ?>
</head>

<body class="bg-gradient-primary">
  <?= $this->renderSection('content'); ?>
  <?= $this->include('_templates/js'); ?>
  <?= $this->renderSection('script'); ?>
</body>

</html>