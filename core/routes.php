<?php

$router->get('', 'AuthController@index');
$router->get('auth', 'AuthController@index');
$router->get('auth/index', 'AuthController@index');
$router->get('auth/admin', 'AuthController@admin');
$router->get('auth/camat', 'AuthController@camat');
$router->get('auth/opd', 'AuthController@opd');
$router->post('auth/login', 'AuthController@login');
$router->any('auth/logout', 'AuthController@logout');

$router->get('dashboard/index', 'DashboardController@index');
$router->get('dashboard/admin', 'DashboardController@admin');
$router->get('dashboard/camat', 'DashboardController@camat');
$router->get('dashboard/opd', 'DashboardController@opd');
$router->get('dashboard/data', 'DashboardController@getDashboardData');
$router->get('dashboard/export', 'DashboardController@exportLaporan');

$router->get('dataPelapor/index', 'DataPelaporController@index');
$router->get('dataPelapor/form', 'DataPelaporController@form');
$router->post('dataPelapor/save', 'DataPelaporController@save');
$router->post('dataPelapor/delete', 'DataPelaporController@delete');
$router->get('dataPelapor/getData', 'DataPelaporController@getDataPelapor');
$router->get('dataPelapor/search', 'DataPelaporController@searchPelapor');
$router->get('dataPelapor/statistics', 'DataPelaporController@getStatistics');
$router->get('dataPelapor/export', 'DataPelaporController@export');

$router->get('opd/index', 'OPDController@index');
$router->get('opd/create', 'OPDController@create');
$router->post('opd/store', 'OPDController@store');
$router->get('opd/edit', 'OPDController@edit');
$router->post('opd/update', 'OPDController@update');
$router->post('opd/delete', 'OPDController@delete');
$router->get('opd/list', 'OPDController@getOPDList');

$router->get('laporan/index', 'LaporanController@index');
$router->get('laporan/generatePDF', 'LaporanController@generatePDF');
$router->get('laporan/generateExcel', 'LaporanController@generateExcel');
$router->get('laporan/tandaTangan', 'LaporanController@tandaTangan');
$router->post('laporan/uploadTandaTangan', 'LaporanController@uploadTandaTangan');

$router->get('wilayah/index', 'WilayahController@index');
$router->get('wilayah/indexKecamatan', 'WilayahController@indexKecamatan');
$router->get('wilayah/indexDesa', 'WilayahController@indexDesa');
$router->get('wilayah/formKecamatan', 'WilayahController@formKecamatan');
$router->get('wilayah/formDesa', 'WilayahController@formDesa');
$router->post('wilayah/saveKecamatan', 'WilayahController@saveKecamatan');
$router->post('wilayah/saveDesa', 'WilayahController@saveDesa');
$router->post('wilayah/deleteKecamatan', 'WilayahController@deleteKecamatan');
$router->post('wilayah/deleteDesa', 'WilayahController@deleteDesa');
$router->get('wilayah/kecamatanOptions', 'WilayahController@getKecamatanOptions');
$router->get('wilayah/kecamatanStats', 'WilayahController@getKecamatanStats');

$router->get('kecamatan/index', 'KecamatanController@index');
$router->get('kecamatan/form', 'KecamatanController@form');
$router->post('kecamatan/save', 'KecamatanController@save');
$router->post('kecamatan/delete', 'KecamatanController@delete');
$router->get('kecamatan/getStats', 'KecamatanController@getStats');

$router->get('desa/index', 'DesaController@index');
$router->get('desa/form', 'DesaController@form');
$router->post('desa/save', 'DesaController@save');
$router->post('desa/delete', 'DesaController@delete');
$router->get('desa/getKecamatanOptions', 'DesaController@getKecamatanOptions');
$router->get('desa/getDesaByKecamatan', 'DesaController@getDesaByKecamatan');

$router->get('profile/index', 'ProfileController@index');
$router->get('profile/create', 'ProfileController@create');
$router->post('profile/store', 'ProfileController@store');
$router->get('profile/edit', 'ProfileController@edit');
$router->post('profile/update', 'ProfileController@update');
$router->post('profile/delete', 'ProfileController@delete');
$router->get('profile/getProfileList', 'ProfileController@getProfileList');

$router->get('waGateway/index', 'WAGatewayController@index');
$router->get('waGateway/form', 'WAGatewayController@form');
$router->post('waGateway/send', 'WAGatewayController@sendMessage');
$router->post('waGateway/delete', 'WAGatewayController@delete');
$router->get('waGateway/getData', 'WAGatewayController@getMessages');
$router->get('waGateway/searchContacts', 'WAGatewayController@searchContacts');
$router->get('waGateway/export', 'WAGatewayController@export');
$router->post('waGateway/bulkSend', 'WAGatewayController@bulkSend');

$router->get('laporanOPD/index', 'LaporanOPDController@index');
$router->get('laporanOPD/create', 'LaporanOPDController@create');
$router->get('laporanOPD/edit', 'LaporanOPDController@edit');
$router->get('laporanOPD/detail', 'LaporanOPDController@detail');
$router->post('laporanOPD/store', 'LaporanOPDController@store');
$router->post('laporanOPD/update', 'LaporanOPDController@update');
$router->post('laporanOPD/delete', 'LaporanOPDController@delete');
$router->get('laporanOPD/download', 'LaporanOPDController@download');
$router->get('laporanOPD/getStats', 'LaporanOPDController@getStats');

$router->get('laporanCamat/index', 'LaporanCamatController@index');
$router->get('laporanCamat/create', 'LaporanCamatController@create');
$router->post('laporanCamat/store', 'LaporanCamatController@store');
$router->get('laporanCamat/edit', 'LaporanCamatController@edit');
$router->post('laporanCamat/update', 'LaporanCamatController@update');
$router->post('laporanCamat/delete', 'LaporanCamatController@delete');
$router->get('laporanCamat/detail', 'LaporanCamatController@detail');
$router->post('laporanCamat/updateStatus', 'LaporanCamatController@updateStatus');
$router->get('laporanCamat/download', 'LaporanCamatController@download');
$router->get('laporanCamat/exportToExcel', 'LaporanCamatController@exportToExcel');

$router->get('laporanOPDAdmin/index', 'LaporanOPDAdminController@index');
$router->get('laporanOPDAdmin/detail', 'LaporanOPDAdminController@detail');
$router->get('laporanOPDAdmin/edit', 'LaporanOPDAdminController@edit');
$router->post('laporanOPDAdmin/updateStatus', 'LaporanOPDAdminController@updateStatus');
$router->post('laporanOPDAdmin/delete', 'LaporanOPDAdminController@delete');
$router->get('laporanOPDAdmin/export', 'LaporanOPDAdminController@export');

$router->get('laporanCamatAdmin/index', 'LaporanCamatAdminController@index');
$router->get('laporanCamatAdmin/detail', 'LaporanCamatAdminController@detail');
$router->get('laporanCamatAdmin/edit', 'LaporanCamatAdminController@edit');
$router->post('laporanCamatAdmin/updateStatus', 'LaporanCamatAdminController@updateStatus');
$router->post('laporanCamatAdmin/delete', 'LaporanCamatAdminController@delete');
$router->get('laporanCamatAdmin/export', 'LaporanCamatAdminController@export');

$router->get('laporanOPDCetak/index', 'LaporanOPDCetakController@index');
$router->get('laporanOPDCetak/generatePDF', 'LaporanOPDCetakController@generatePDF');
$router->get('laporanOPDCetak/generateExcel', 'LaporanOPDCetakController@generateExcel');
