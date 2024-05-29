define(["jquery", "uiRegistry"], function ($, registry) {
    "use strict";
    var stockUpdate = {
        _create: function (config, element) {
            $(document).ready(function () {
                var productId = config.product;

                $('.swatch-opt, .swatch-opt-' + productId).on('click', function (event) {
                    stockUpdate.updateStockStatus(productId, event.target);
                });
            });
        },

        updateStockStatus(productId, target) {
            var swatch = $(target).closest('.swatch-opt, .swatch-opt-' + productId);
            if (!this.checkSelectedItems(swatch)) {
                return;
            }

            var data = this.getSelectedOptions(swatch, productId);

            $.ajax({
                url:  BASE_URL + 'mageworx_stock_status/stock/update',
                type: 'POST',
                isAjax: true,
                dataType: 'html',
                data: data,
                success: function (xhr, status, errorThrown) {
                    $('#status-' + productId).html(JSON.parse(xhr));
                },
                error: function (xhr, status, errorThrown) {
                    console.log('There was an error loading stock data.');
                    console.log(errorThrown);
                }
            });
        },

        checkSelectedItems(swatch) {
            var result = true;
            $.each(swatch.find('.swatch-attribute'), function (i, e) {
                var option = $(e).attr('option-selected');
                if (typeof (option) === "undefined") {
                    result = false;
                }
            });

            return result;
        },
        getSelectedOptions(swatch, productId) {
            var data = swatch.closest('form').serializeArray();

            if (!data.length) {
                var superAttribute = function (name, value) {
                    this.name = name;
                    this.value = value;
                };

                data.push(new superAttribute('product', productId));

                $.each(swatch.find('.swatch-attribute'), function (i, e) {
                    var name = 'super_attribute[' + $(e).attr('attribute-id') + ']';
                    var value = $(e).attr('option-selected');
                    var obj = new superAttribute(name, value);

                    data.push(obj);
                });
            }

            return data;
        }
    };
    return {
        'stockUpdate': stockUpdate._create
    };

});