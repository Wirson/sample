define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'loader'
], function ($, customerData) {
    'use strict';

    $.widget('wira.productViewPrice', {
        _create: function () {
            if (customerData.get('customer')().fullname) {
                $(this.element).loader({icon: this.options.loaderIconUrl});
                $(this.element).loader('show');

                $.ajax({
                    url: this.options.priceUrl,
                    type: 'POST',
                    dataType: 'json',
                    // showLoader: true,
                    data: {sku: this.options.sku}
                }).done(function (response) {
                    if (response.result) {
                        this.replace(response.result)
                    }
                }.bind(this))
            }
        },

        replace: function (prices) {
            $(this.element).replaceWith(prices);
            $('body').trigger('contentUpdated');
        }
    });

    return $.wira.productViewPrice;
});
