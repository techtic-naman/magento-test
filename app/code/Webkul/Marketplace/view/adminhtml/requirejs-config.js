/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
 var config = {
    'shim': {
        'Webkul_Marketplace/js/extjs/ext-tree-checkboxCategory': [
            'extjs/defaults'
        ],
    },
    "map": {
        "*": {
            sellerCheckboxTree : "Webkul_Marketplace/js/extjs/ext-tree-checkboxCategory",
            sellerCheckboxCategoryTree : "Webkul_Marketplace/js/extjs/seller-tree-checkboxCategory",
            verifyShop : "Webkul_Marketplace/js/verifyShop"
        }
    },
};
