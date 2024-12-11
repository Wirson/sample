define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'loader'
], function ($, customerData) {
    'use strict';

    $.widget('wira.productListPrices', {
        options: {
            priceBoxes: []
        },
        _create: function () {
            if (customerData.get('customer')().fullname) {
                this.options.priceBoxes = $('[data-role="priceBox"]');
                let self = this,
                    ids = [];

                this.options.priceBoxes.each(function (key, box) {
                    let $box = $(box);
                    $box.loader({icon: self.options.loaderIconUrl});
                    $box.loader('show');
                    ids.push(box.dataset.productId);
                });
                if (ids.length) {
                    $.ajax({
                        url: this.options.priceUrl,
                        type: 'POST',
                        dataType: 'json',
                        // showLoader: true,
                        data: {ids: ids, display_mode: this.options.displayMode}
                    }).done(function (response) {
                        if (response.result) {
                            this.replace(response.result)
                        }
                    }.bind(this))
                }
            }
        },

        replace: function (prices) {
            this.options.priceBoxes.each(function (key, box) {
                let boxElement = $(box);
                if (prices[box.dataset.productId]) {
                    boxElement.replaceWith(prices[box.dataset.productId]);
                }
            })
        }
    });

    return $.wira.productListPrices;
});
