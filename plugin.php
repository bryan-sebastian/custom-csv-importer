<?php
/*
Plugin Name: Custom CSV Importer
Description: Imports data from CSV Files.
Version:     1.0.0
Author:      Bryan Sebastian
Author URI:  https://bryan-sebastian.github.io/
*/

if ( !defined( 'WPINC' ) )
	die();

if ( is_admin() ) {
	require 'vendor/wplib-csv/wplib-csv.php';
	require 'admin/importer.php';
}