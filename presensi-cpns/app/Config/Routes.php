<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('admin', static function ($routes) {
  $routes->get('/', 'Admin\AuthController::index', ['as' => 'admin.index']);

  $routes->group('auth', static function ($routes) {
    $routes->post('login', 'Admin\AuthController::login', ['as' => 'admin.login']);
    $routes->get('logout', 'Admin\AuthController::logout', ['as' => 'admin.logout']);
  });

  $routes->get('dashboard', 'Admin\DashboardController::index', ['as' => 'admin.dashboard', 'filter' => 'petugas-auth']);

  $routes->group('peserta', ['filter' => 'petugas-auth'], static function ($routes) {
    $routes->get('/', 'Admin\PesertaController::index', ['as' => 'admin.peserta']);
    $routes->get('get/(:num)', 'Admin\PesertaController::get/$1', ['as' => 'admin.peserta.get']);
    $routes->get('get-qrcode/(:num)', 'Admin\PesertaController::getQrcode/$1', ['as' => 'admin.peserta.get-qrcode']);
    $routes->get('get-by-no/(:segment)', 'Admin\PesertaController::getByNoPeserta/$1', ['as' => 'admin.peserta.get-by-no']);
    $routes->get('list', 'Admin\PesertaController::list', ['as' => 'admin.peserta.list']);

    $routes->get('get-import-template', 'Admin\PesertaController::getImportTemplate', ['as' => 'admin.peserta.get-import-template']);
    $routes->get('export-excel', 'Admin\PesertaController::exportExcel', ['as' => 'admin.peserta.export-excel']);
    $routes->get('export-pdf', 'Admin\PesertaController::exportPdf', ['as' => 'admin.peserta.export-pdf']);
    $routes->get('download-export-zip', 'Admin\PesertaController::zipExport', ['as' => 'admin.peserta.download-export-zip']);

    $routes->post('save', 'Admin\PesertaController::save', ['as' => 'admin.peserta.save']);
    $routes->post('import', 'Admin\PesertaController::import', ['as' => 'admin.peserta.import']);
    $routes->delete('delete/(:num)', 'Admin\PesertaController::delete/$1', ['as' => 'admin.peserta.delete']);
  });

  $routes->group('presensi', ['filter' => 'petugas-auth'], static function ($routes) {
    $routes->get('/', 'Admin\PresensiController::index', ['as' => 'admin.presensi']);
    $routes->get('list', 'Admin\PresensiController::list', ['as' => 'admin.presensi.list']);

    $routes->get('export-excel', 'Admin\PresensiController::exportExcel', ['as' => 'admin.presensi.export-excel']);
    $routes->get('export-pdf', 'Admin\PresensiController::exportPdf', ['as' => 'admin.presensi.export-pdf']);

    $routes->post('save', 'Admin\PresensiController::save', ['as' => 'admin.presensi.save']);
    $routes->delete('delete/(:num)', 'Admin\PresensiController::delete/$1', ['as' => 'admin.presensi.delete']);
  });

  $routes->group('lokasi', ['filter' => 'admin-auth'], static function ($routes) {
    $routes->get('/', 'Admin\LocationsController::index', ['as' => 'admin.lokasi']);
    $routes->get('get/(:num)', 'Admin\LocationsController::get/$1', ['as' => 'admin.lokasi.get']);
    $routes->get('list', 'Admin\LocationsController::list', ['as' => 'admin.lokasi.list']);

    $routes->post('save', 'Admin\LocationsController::save', ['as' => 'admin.lokasi.save']);
    $routes->delete('delete/(:num)', 'Admin\LocationsController::delete/$1', ['as' => 'admin.lokasi.delete']);
  });

  $routes->group('sesi', ['filter' => 'admin-auth'], static function ($routes) {
    $routes->get('/', 'Admin\SesiController::index', ['as' => 'admin.sesi']);
    $routes->get('get/(:num)', 'Admin\SesiController::get/$1', ['as' => 'admin.sesi.get']);
    $routes->get('list', 'Admin\SesiController::list', ['as' => 'admin.sesi.list']);

    $routes->post('save', 'Admin\SesiController::save', ['as' => 'admin.sesi.save']);
    $routes->delete('delete/(:num)', 'Admin\SesiController::delete/$1', ['as' => 'admin.sesi.delete']);
  });

  $routes->group('petugas', ['filter' => 'admin-auth'], static function ($routes) {
    $routes->get('/', 'Admin\PetugasController::index', ['as' => 'admin.petugas']);
    $routes->get('get/(:num)', 'Admin\PetugasController::get/$1', ['as' => 'admin.petugas.get']);
    $routes->get('list', 'Admin\PetugasController::list', ['as' => 'admin.petugas.list']);

    $routes->post('save', 'Admin\PetugasController::save', ['as' => 'admin.petugas.save']);
    $routes->delete('delete/(:num)', 'Admin\PetugasController::delete/$1', ['as' => 'admin.petugas.delete']);
  });
});

$routes->get('/', static function () {
  return redirect()->to('/admin');
});

// $routes->group('/participant', static function ($routes) {
//   $routes->get('/', 'Participant\AuthController::index', ['as' => 'participant.index']);

//   $routes->group('auth', static function ($routes) {
//     $routes->post('login', 'Participant\AuthController::login', ['as' => 'participant.login']);
//     $routes->get('logout', 'Participant\AuthController::logout', ['as' => 'participant.logout']);
//   });

//   $routes->group('profile', static function ($routes) {
//     $routes->get('/', 'Participant\ProfileController::index', ['as' => 'participant.profile',  'filter' => 'participant-auth']);

//     $routes->post('/', 'Participant\ProfileController::save', ['as' => 'participant.profile.save',  'filter' => 'participant-auth']);
//   });
// });
