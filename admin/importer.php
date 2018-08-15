<?php

if ( !defined( 'WPINC' ) ) 
	die();

if ( !class_exists( 'CustomImporter' ) ) :

class CustomImporter {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	public function admin_init() {
		register_importer( 'custom-csv-importer', 'Custom CSV Importer', 'Import data from csv files',  array( $this, 'render_importer' ) );
	}

	public function render_importer() {

		$error = '';
		$success = '';

		// Check if form is submitted
		if ( isset( $_POST['submit'] ) ) {

			// Validate form submission
			if ( wp_verify_nonce( filter_input( INPUT_POST , 'ccsvi_nonce'), 'ccsvi_nonce' ) ) {
				/**
				 * @see vendor/wplib-csv for the functions
				 */
				$csv_file_path 	= wplib_csv_upload( $_FILES['file'] );	
				$result 		= wplib_csv_import_to_post( $csv_file_path );
				if ( $result['imported'] != 0 ) {
					$success = sprintf('<strong>Success: </strong> %d out of %d rows were successfully imported.', $result['imported'], $result['total']);
				}

				if ( $result['failed'] != 0 ) {
					foreach ($result['errors'] as $error) {
						$errors[] = $error;
					}
				}
		 	} else {

		 		$error = '<strong>Error: </strong> Invalid form submission.';
		 	}
		}
		?>
		<div class="wrap">
			<h2>Custom CSV Importer</h2>
	
			<?php if ( ! empty( $errors ) ): ?>
			<div id="message" class="error">
				<?php foreach ($errors as $error): ?>
					<p><?php echo $error ?></p>
				<?php endforeach ?>
			</div>
			<?php endif; ?>

			<?php if ( !empty( $success ) ): ?>
			<div id="message" class="updated">
				<p><?php echo $success; ?></p>
			</div>
			<?php endif; ?>

			<form method="POST" enctype="multipart/form-data">
				
				<div style="display: none;">
					
					<?php wp_nonce_field( 'ccsvi_nonce', 'ccsvi_nonce' ); ?>

				</div>

				<div style="margin-bottom: 20px;margin-top: 20px;">
					<label for=""><strong>Choose file <span style="color: red;">*</span></strong></label><br/>
					<p class="description">Please upload a <strong>.csv</strong> file only.</p>
					<input type="file" name="file" required>
				</div>

				<p>
					<input type="submit" name="submit" value="Import Data" class="button button-primary">
				</p>
	
			</form>
		</div>
		<?php
	}
}

new CustomImporter();

endif;