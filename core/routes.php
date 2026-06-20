<?php

$router->get('', 'AuthController@index');
$router->get('login/admin', 'AuthController@admin');
$router->get('login/camat', 'AuthController@camat');
$router->get('login/opd', 'AuthController@opd');
$router->post('login', 'AuthController@login');
$router->post('logout', 'AuthController@logout');

$router->get('admin/dashboard', 'DashboardController@admin');
$router->get('admin/dashboard/data', 'DashboardController@getDashboardData');
$router->get('admin/dashboard/export', 'DashboardController@exportLaporan');
$router->get('camat/dashboard', 'DashboardController@camat');
$router->get('opd/dashboard', 'DashboardController@opd');

$router->get('data-pelapor', 'DataPelaporController@index');
$router->get('data-pelapor/create', 'DataPelaporController@create');
$router->get('data-pelapor/search', 'DataPelaporController@searchPelapor');
$router->get('data-pelapor/statistics', 'DataPelaporController@getStatistics');
$router->get('data-pelapor/export', 'DataPelaporController@export');
$router->get('data-pelapor/list', 'DataPelaporController@getDataPelapor');
$router->get('data-pelapor/{id}/edit', 'DataPelaporController@edit');
$router->post('data-pelapor', 'DataPelaporController@store');
$router->put('data-pelapor/{id}', 'DataPelaporController@update');
$router->delete('data-pelapor/{id}', 'DataPelaporController@delete');

$router->get('opd', 'OPDController@index');
$router->get('opd/create', 'OPDController@create');
$router->get('opd/list', 'OPDController@getOPDList');
$router->get('opd/{id}/edit', 'OPDController@edit');
$router->post('opd', 'OPDController@store');
$router->put('opd/{id}', 'OPDController@update');
$router->delete('opd/{id}', 'OPDController@delete');

$router->get('kecamatan', 'KecamatanController@index');
$router->get('kecamatan/create', 'KecamatanController@form');
$router->get('kecamatan/{id}/stats', 'KecamatanController@getStats');
$router->get('kecamatan/{id}/edit', 'KecamatanController@form');
$router->post('kecamatan', 'KecamatanController@save');
$router->put('kecamatan/{id}', 'KecamatanController@save');
$router->delete('kecamatan/{id}', 'KecamatanController@delete');

$router->get('desa', 'DesaController@index');
$router->get('desa/create', 'DesaController@form');
$router->get('desa/options/kecamatan', 'DesaController@getKecamatanOptions');
$router->get('desa/by-kecamatan', 'DesaController@getDesaByKecamatan');
$router->get('desa/{id}/edit', 'DesaController@form');
$router->post('desa', 'DesaController@save');
$router->put('desa/{id}', 'DesaController@save');
$router->delete('desa/{id}', 'DesaController@delete');

$router->get('profiles', 'ProfileController@index');
$router->get('profiles/create', 'ProfileController@create');
$router->get('profiles/list', 'ProfileController@getProfileList');
$router->get('profiles/{id}/edit', 'ProfileController@edit');
$router->post('profiles', 'ProfileController@store');
$router->put('profiles/{id}', 'ProfileController@update');
$router->delete('profiles/{id}', 'ProfileController@delete');

$router->get('wa-messages', 'WAGatewayController@index');
$router->get('wa-messages/create', 'WAGatewayController@form');
$router->get('wa-messages/list', 'WAGatewayController@getMessages');
$router->get('wa-messages/search-contacts', 'WAGatewayController@searchContacts');
$router->get('wa-messages/export', 'WAGatewayController@export');
$router->get('wa-messages/{id}/edit', 'WAGatewayController@form');
$router->post('wa-messages', 'WAGatewayController@sendMessage');
$router->put('wa-messages/{id}', 'WAGatewayController@sendMessage');
$router->delete('wa-messages/{id}', 'WAGatewayController@delete');
$router->post('wa-messages/bulk-send', 'WAGatewayController@bulkSend');

$router->get('laporan', 'LaporanController@index');
$router->get('laporan/pdf', 'LaporanController@generatePDF');
$router->get('laporan/excel', 'LaporanController@generateExcel');
$router->get('laporan/tanda-tangan/{type}/{id}', 'LaporanController@tandaTangan');
$router->post('laporan/tanda-tangan', 'LaporanController@uploadTandaTangan');
$router->get('laporan/tanda-tangan/{type}/{id}/pdf', 'LaporanController@generatePDFWithSignature');

$router->get('laporan-opd', 'LaporanOPDController@index');
$router->get('laporan-opd/create', 'LaporanOPDController@create');
$router->get('laporan-opd/stats', 'LaporanOPDController@getStats');
$router->get('laporan-opd/{id}/download', 'LaporanOPDController@download');
$router->get('laporan-opd/{id}/edit', 'LaporanOPDController@edit');
$router->get('laporan-opd/{id}', 'LaporanOPDController@detail');
$router->post('laporan-opd', 'LaporanOPDController@store');
$router->put('laporan-opd/{id}', 'LaporanOPDController@update');
$router->delete('laporan-opd/{id}', 'LaporanOPDController@delete');

$router->get('laporan-camat', 'LaporanCamatController@index');
$router->get('laporan-camat/create', 'LaporanCamatController@create');
$router->get('laporan-camat/export', 'LaporanCamatController@exportToExcel');
$router->get('laporan-camat/{id}/download', 'LaporanCamatController@download');
$router->get('laporan-camat/{id}/edit', 'LaporanCamatController@edit');
$router->get('laporan-camat/{id}', 'LaporanCamatController@detail');
$router->post('laporan-camat', 'LaporanCamatController@store');
$router->put('laporan-camat/{id}', 'LaporanCamatController@update');
$router->patch('laporan-camat/{id}/status', 'LaporanCamatController@updateStatus');
$router->delete('laporan-camat/{id}', 'LaporanCamatController@delete');

$router->get('admin/laporan-opd', 'LaporanOPDAdminController@index');
$router->get('admin/laporan-opd/export', 'LaporanOPDAdminController@export');
$router->get('admin/laporan-opd/{id}/edit', 'LaporanOPDAdminController@edit');
$router->post('admin/laporan-opd/{id}/edit', 'LaporanOPDAdminController@edit');
$router->put('admin/laporan-opd/{id}/edit', 'LaporanOPDAdminController@edit');
$router->get('admin/laporan-opd/{id}', 'LaporanOPDAdminController@detail');
$router->patch('admin/laporan-opd/{id}/status', 'LaporanOPDAdminController@updateStatus');
$router->delete('admin/laporan-opd/{id}', 'LaporanOPDAdminController@delete');

$router->get('admin/laporan-camat', 'LaporanCamatAdminController@index');
$router->get('admin/laporan-camat/export', 'LaporanCamatAdminController@export');
$router->get('admin/laporan-camat/{id}/edit', 'LaporanCamatAdminController@edit');
$router->post('admin/laporan-camat/{id}/edit', 'LaporanCamatAdminController@edit');
$router->put('admin/laporan-camat/{id}/edit', 'LaporanCamatAdminController@edit');
$router->get('admin/laporan-camat/{id}', 'LaporanCamatAdminController@detail');
$router->patch('admin/laporan-camat/{id}/status', 'LaporanCamatAdminController@updateStatus');
$router->delete('admin/laporan-camat/{id}', 'LaporanCamatAdminController@delete');
