<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class AlertCell extends Cell
{
  public $type; // primary, danger, warning, success, etc
  public $message;
  public $dismissible = true;
}
