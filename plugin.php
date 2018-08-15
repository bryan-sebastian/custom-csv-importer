<?php
/*
Plugin Name: Custom CSV Importer
Plugin URI:	 https://github.com/bryan-sebastian/custom-csv-importer
Description: A wordpress plugin that can import a list of vouchers with order. Note that this plugin is for developers only because the saving of data are customized depends on the structure of data that will save in the database.
Version:     1.0.0
Author:      Bryan Sebastian
Author URI:  https://bryan-sebastian.github.io/
License:	 MIT license - http://www.opensource.org/licenses/mit-license.php
*/

if ( !defined( 'WPINC' ) )
	die();

if ( is_admin() ) {
	require 'vendor/wplib-csv/wplib-csv.php';
	require 'admin/importer.php';
}