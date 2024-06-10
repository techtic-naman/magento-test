#Installation

Magento2 Helpdesk module installation is very easy, please follow the steps for installation-

1. Unzip the respective extension zip and then move "app" folder (inside "src" folder) into magento root directory.

Run Following Command via terminal
-----------------------------------
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy

Run Following command to install tedivm/fetch library
-----------------------------------------------------
composer require tedivm/fetch

2. Flush the cache and reindex all.

now module is properly installed

#User Guide

For Magento2 Helpdesk module's working process follow user guide - https://webkul.com/blog/magento2-helpdesk-extension/

#Support

Find us our support policy - https://store.webkul.com/support.html/

#Refund

Find us our refund policy - https://store.webkul.com/refund-policy.html/
