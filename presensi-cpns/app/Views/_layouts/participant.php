<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $this->renderSection('title', true); ?></title>

  <?= $this->include('_templates/css'); ?>
  <?= $this->renderSection('css') ?>
</head>

<body id="page-top">
  <div id="wrapper">
    <?= $this->include('_templates/sidebar_participant'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php
        echo $this->include('_templates/topbar');

        echo $this->renderSection('content');

        echo $this->include('_templates/js');
        echo $this->renderSection('script');

        if (session()->getFlashdata('error')) {
          echo view_cell('ToastCell', ['type' => 'error', 'message' => session()->getFlashdata('error')]);
        }

        if (session()->getFlashdata('success')) {
          echo view_cell('ToastCell', ['type' => 'success', 'message' => session()->getFlashdata('success')]);
        }

        ?>
      </div>
      <?= $this->include('_templates/footer.php'); ?>
    </div>
    <a class="scroll-to-top rounded" style="z-index: 100;" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>
  </div>
</body>

</html>