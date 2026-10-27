<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class ToastCell extends Cell
{
  public $type;
  public $title;
  public $message;

  public function mount($type, $message)
  {
    $this->type = strtolower($type);

    switch (strtolower($type)) {
      case 'error':
        $this->title = 'Kesalahan';
        break;
      case 'success':
        $this->title = 'Sukses';
        break;
      case 'info':
        $this->title = 'Info';
        break;
      case 'warning':
        $this->title = 'Peringatan';
        break;
    }

    if (is_array($message)) {
      $msg = '';
      foreach ($message as $val) {
        $msg .= trim($val) . '<br>';
      }

      $this->message = $msg;
    } else {
      $this->message = $message;
    }
  }
}
