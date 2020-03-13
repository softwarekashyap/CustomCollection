# Magento 2 Custom Collection

## Installation without composer
* Download zip file of this extension
* Place all the files of the extension in your Magento 2 installation in the folder `app/code/Kashyap/Customcollection`
* Enable the extension: `php bin/magento --clear-static-content module:enable Kashyap_Customcollection`
* Upgrade db scheme: `php bin/magento setup:upgrade`
* Deply Static Content: `php bin/magento setup:static-content:deploy -f` Developer Mode
* Deply Static Content: `php bin/magento setup:static-content:deploy` Production Mode

## Features
* 