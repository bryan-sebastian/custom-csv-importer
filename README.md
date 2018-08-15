# Custom CSV Importer

A wordpress plugin that can import a list of vouchers with order. 

Note that this plugin is for developers only because the saving of data are customized depends on the structure of data that will save in the database.

## Getting Started

First things first; Smile XD

### Prerequisites

Things you need to install the software and how to install them

* [WordPress](https://wordpress.com/) - The web framework used
* [WooCommerce](https://woocommerce.com/) - Dependency Management

### How to use

1. Download this repository
2. Copy the repository on project_name/wp-content/plugins
3. Go to wordpress backend
4. Click the Plugins
5. Activate the Custom CSV Importer
6. Go to Tools > Import
7. Run the Custom CSV Importer
8. Upload your CSV file
9. Boila! Your data has been imported! :)

### NOTE!!!!!

This plugin is for the developers only because the saving of the data are cuztomized depends on the data inside the file **sample-data-to-upload.csv**.

If you want to customize the saving of data go to custom-csv-importer/vendor/wplib-csv/api.php locate the function **wplib_csv_import_to_post**, inside of that function there's a loop of row in your uploaded csv then below the block comment of *Edit the below statement if you want to customize the saving of data* remove the given code on how to save the data inside **sample-data-to-upload.csv** and replace with your new code on how to save your new data structure. 

## Author

* **Bryan Sebastian** - *Portfolio* - [bryan-sebastian.github.io](http://bryan-sebastian.github.io)

## Contributing

For a pull request to be considered it must resolve a bug, or add a feature which is beneficial to a large audience.

Requests must be made against the develop branch. Pull requests submitted against the master branch will not be considered.

All pull requests are subject to approval by the repository owners, who have sole discretion over acceptance or denial.

## License
Custom CSV Importer is under MIT license - http://www.opensource.org/licenses/mit-license.php