<?php
if ( !function_exists( 'wplib_csv_upload_csv_file' ) ) :

/**
 * Extracts the rows in the csv files
 *
 * Use inside a try catch statement
 * 
 * @param  string $csv_file_path The server path of the csv file
 * @param  array  $args          The arguments of the extraction	
 * @return array                 The extracted rows
 */
function wplib_csv_extract_rows( $csv_file_path, array $args = array() ) {

	// Check if the file extension is csv
	if ( pathinfo( $csv_file_path, PATHINFO_EXTENSION ) !== 'csv' ) {
		
		throw new Exception("File should be a .csv file");
	}

	// Check if the path of the csv exists in the server
	if ( !file_exists( $csv_file_path ) ) {
		
		throw new Exception("CSV file not do not exist.");
	}

	$args = wp_parse_args( $args, array(
		'headers' => array(),
	));

	$custom_headers = $args['headers'];
	$headers 	= array();
	$rows 		= array();
	$counter 	= 0;
	$_file 		= fopen( $csv_file_path, 'r' );

	while (($csv_row = fgetcsv($_file, 100000, ",")) !== FALSE) {

		// Header
		if ( $counter == 0 ) {

			$headers = array_flip( $csv_row );

			// Limit the headers to export
			if ( !empty( $custom_headers ) ) {

				foreach( $csv_row as $header_key => $header ) {
					if ( !in_array( $header, $custom_headers) ) {
						unset( $headers[$header] );
					}
				}
			}

		} else {

			$data = array();

			foreach( $headers as $k => $v ) {

				$data[ $k ] = isset( $csv_row[ $v ] ) ? $csv_row[ $v ] : '';
			}

			$rows[] = $data;
		}

		$counter++;
	}

	return $rows;
}	

endif;


if ( !function_exists( 'wplib_csv_upload_csv_file' ) ) :

/**
 * Uploads a csv
 *
 * Use this function inside a try catch statement
 *
 * @param  array  $tmp_file The file from the $_FILE global variable
 * @param  array  $args     The arguments of the upload
 * @return string 			The server path of the uploaded file             
 */
function wplib_csv_upload( $tmp_file, array $args = array() ) {

	// Require library if not included
	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	$args = wp_parse_args( $args, array(
		'max_filesize' => 10000*1024 // 10MB max
	));

	// Get max file size
	$max_filesize 	= (float) $args['max_filesize'];

	// Validate file extension
	if ( pathinfo( $tmp_file['name'], PATHINFO_EXTENSION ) !== 'csv' ) {
		
		throw new Exception("File should be a .csv file");
	}

	// Validate file size
	if ( $tmp_file['size'] > $max_filesize  ) {
		throw new Exception(sprintf('File should be not more than %fKB', $max_filesize ));
	}

	// Upload file
	$uploaded_file = wp_handle_upload( $tmp_file, array(
		'test_form' => false
	));

	// Check if upload is valid
	if ( $uploaded_file && !isset( $uploaded_file['error'] ) ) {

		// Return file path
		return $uploaded_file['file'];
	}

	// Throw error
	throw new Exception($uploaded_file['error']);
}

endif;


if( !function_exists( 'wplib_csv_import' ) ) :

function wplib_csv_import_to_post( $csv_file_path, array $args = array() ) {

	$rows = wplib_csv_extract_rows( $csv_file_path );

	$imported 	= 0;
	$failed 	= 0;
	$errors 	= array();

	/* Prepare for saving user */
	if ( ! empty( $rows ) ) {
		/* Fetch user data */
		foreach( $rows as $row_key => $row ) {
			/**
			 * Edit the below statement if you want
			 * to customize the saving of data
			 */
			
			/* Add post order */
			$order_id = wp_insert_post( array(
				'post_author'	=> $row['o_post_author'],
				'post_title'	=> 'Order - '. date('F d, Y @ h:i A'),
				'post_status'	=> $row['o_post_status'],
				'post_parent'	=> $row['o_post_parent'],
				'post_type'		=> $row['o_post_type']
			) );

			/* Add post meta of order */
			$order_metadata = array(
				'_completed_date'         => '',
				'_date_paid'              => '',
				'_paid_date'              => '',
				'_cart_hash'              => '',
				'_order_currency'         => $row['o_pm_order_currency'],
				'_order_currency'         => 0,
				'_order_tax'              => 0,
				'_order_total'            => $row['o_pm_order_total'],
				'_order_version'          => '3.3.5',
				'_prices_include_tax'     => 'no',
				'_billing_address_index'  => '',
				'_shipping_address_index' => '',
				'_wp_page_template'       => 'default',
				'_vc_post_settings'       => 'default',
				'_billing_first_name'     => $row['o_pm_billing_first_name'],
				'_billing_last_name'      => '',
				'_billing_company'        => '',
				'_billing_address_1'      => '',
				'_billing_address_2'      => '',
				'_billing_city'           => '',
				'_billing_postcode'       => '',
				'_billing_country'        => '',
				'_billing_state'          => '',
				'_billing_phone'          => '',
				'_billing_email'          => '',
				'_customer_user'          => 0,
				'_shipping_first_name'    => $row['o_pm_shipping_first_name'],
				'_shipping_last_name'     => '',
				'_shipping_company'       => '',
				'_shipping_address_1'     => '',
				'_shipping_address_2'     => '',
				'_shipping_city'          => '',
				'_shipping_postcode'      => '',
				'_shipping_country'       => '',
				'_shipping_state'         => '',
				'_shipping_phone'         => '',
				'_shipping_email'         => '',
				'_payment_method'         => 'offline_cc',
				'_payment_method_title'   => 'Credit Card',
				'_transaction_id'         => '',
				'_cart_discount'          => 0,
				'_cart_discount_tax'      => 0,
				'_order_shipping'         => 0,
				'_order_key'           	  => '',
				'_customer_ip_address'    => '',
				'_customer_user_agent'    => '',
				'_created_via'            => '',
				'_date_completed'         => '',
			);
			foreach( $order_metadata as $key => $value ) {
				add_post_meta( $order_id, $key, $value, true );
			}

			/* Add post voucher */
			$voucher_id = wp_insert_post( array(
				'post_author'	=> $row['v_post_author'],
				'post_title'	=> strtoupper( substr( uniqid(), 5, 13 ) ) . '-' . $row['v_post_title'],
				'post_status'	=> $row['v_post_status'],
				'post_parent'	=> $row['v_post_parent'],
				'post_type'		=> $row['v_post_type']
			) );

			/* Add post meta voucher */
			$voucher_metadata = array(
				'_recipient_name'	=> $row['v_pm_recipient_name'],
				'_purchaser_name'	=> $row['v_pm_purchaser_name'],
				'_remaining_value'	=> $row['v_pm_remaining_value'],
				'_product_quantity'	=> $row['v_pm_product_quantity'],
				'_product_price'	=> $row['v_pm_product_price'],
				'_voucher_currency'	=> $row['v_pm_voucher_currency'],
				'_voucher_type'		=> $row['v_pm_voucher_type'],
				'_thumbnail_id'		=> $row['v_pm_thumbnail_id'],
				'_order_item_id'	=> $row['v_pm_order_item_id'],
				'_product_id'		=> $row['v_pm_product_id'],
				'_order_id'			=> $order_id,
				'_vc_post_settings'	=> '',
				'_wp_page_template'	=> 'default'
			);
			foreach( $voucher_metadata as $key => $value ) {
				add_post_meta( $voucher_id, $key, $value, true );
			}

			/* Check if there is an error in saving data */
			if ( $wpdb->last_error == '' ) {
				$imported++;
			} else {
				$failed++;
				$errors[] = 'Row: ' . ($row_key + 1) . ' - ' . $wpdb->last_error;
			}
		}
	}

	return array(
		'imported' 	=> $imported,
		'total'		=> COUNT( $rows ),
		'errors'	=> $errors,
		'failed'	=> $failed
	);
}

endif;

