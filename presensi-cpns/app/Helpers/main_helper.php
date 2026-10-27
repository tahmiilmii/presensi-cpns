<?php

if (!function_exists('assets')) {
  function assets(string $path, string $fallback = ''): string
  {
    $publicPath = FCPATH . 'assets/' . $path;

    if (is_file($publicPath)) {
      return base_url('assets/' . $path);
    }

    if ($fallback !== '') {
      $fallbackPath = FCPATH . 'assets/' . $fallback;
      if (is_file($fallbackPath)) {
        return base_url('assets/' . $fallback);
      }
    }

    return '';
  }
}


function indonesianDateToIntl($date)
{
  $indonesianMonths = [
    'januari' => 'january',
    'februari' => 'february',
    'maret' => 'march',
    'april' => 'april',
    'mei' => 'may',
    'juni' => 'june',
    'juli' => 'july',
    'agustus' => 'august',
    'september' => 'september',
    'oktober' => 'october',
    'november' => 'november',
    'desember' => 'december'
  ];

  return str_replace(array_keys($indonesianMonths), array_values($indonesianMonths), strtolower($date));
}
