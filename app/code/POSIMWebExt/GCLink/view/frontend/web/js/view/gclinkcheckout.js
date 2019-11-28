define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'mage/loader',
        'mage/translate',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_SalesRule/js/model/payment/discount-messages'
    ],
    function ($,
              ko,
              Component,
              quote,
              storage,
              loader,
              $t,
              urlManager,
              getPaymentInformationAction,
              totals,
              fullScreenLoader,
              messageContainer
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'POSIMWebExt_GCLink/gclinkcheckout'
            },
            isLoading: true,
            hasGCApplied: false,
            gcNum: '',
            loadingIcon: 'gcloading.gif',
            cancelUrl : 'gclink/pay/remove',
            submitUrl : 'gclink/pay/add',


            initObservable: function () {
                this._super()
                    .observe({
                        isLoading:true,
                        hasGCApplied:false,
                        gcNum:''

                    });
                var self = this;

                var options ;
                self.isLoading.subscribe(function(newValue) {
                    if (newValue) {
                        $('div[data-role="loader"]').show();
                    } else {
                        $('div[data-role="loader"]').hide();
                    }

                },self);

                //var quoteFeedUrl =  MY_BASE_URL +  this.feedUrl;
                //self.loadingIcon = window.giftCardLoaderIcon;

                // $.ajax({
                //     url: quoteFeedUrl,
                //     async :false,
                //     global: true,
                //     contentType : 'application/json',
                // }).done(function(response){
                //     var responseData = response;
                //     if (responseData.giftcard != undefined) {
                //         self.giftcardCode(responseData.giftcard);
                //
                //         if (responseData.giftcard.length > 0) {
                //             self.hasGiftCard(true);
                //         }
                //     }
                //     self.isLoading(false);
                //
                // });
//todo:clean up above
                return this;
            },


            apply: function() {
                var self = this;
                var form = $('form[data-action="gclink-checkout-form"]');

                return  $.ajax({
                        url: STORE_URL+ self.submitUrl,
                        type: 'post',
                        async: false,
                        data: form.serialize()
                    }
                ).done(
                    function (response) {

                        if (response) {

                            var deferred = $.Deferred();
                            var responseCode = response.code;
                            var responseMessage = response.message;

                            if (responseCode == 'ok' ) {

                                self.hasGCApplied(true);
                                self.gcNum($('#posimgc_num').val());
                                totals.isLoading(true);
                                getPaymentInformationAction(deferred);
                                $.when(deferred).done(function () {
                                    totals.isLoading(false);
                                });

                                //self.regions.messages()[0].messageContainer.addSuccessMessage({'message': responseMessage}); //TODO messages()[0] seems to be undefined so messages won't pop up

                            }  else if (responseCode == 'error') {
                                self.isLoading(false);

                                //  self.regions.messages()[0].messageContainer.addErrorMessage({'message': responseMessage});

                            }

                        }
                    }
                ).fail(
                    function (response) {
                    }
                ).always(
                    function () {
                    }
                );


            },

            cancel: function() {
                var self = this;
                self.isLoading(true);
                var form = $('form[data-action="gclink-checkout-form"]');

                return $.ajax(
                    {
                        url :  STORE_URL + self.cancelUrl,
                        type: 'post',
                        async: false,
                        data: form.serialize()
                    }
                ).done(
                    function (response) {

                        var deferred = $.Deferred();
                        var responseCode = response.code;
                        var responseMessage = response.message;
                        if (responseCode == 'ok' ) {
                            totals.isLoading(true);
                            getPaymentInformationAction(deferred);

                            $.when(deferred).done(function () {
                                self.hasGCApplied(false);
                                self.gcNum('');
                                form.posimgc_num = '';
                                totals.isLoading(false);

                                //   self.regions.messages()[0].messageContainer.addSuccessMessage({'message': responseMessage}); //todo: todo

                            });
                        } else {
                            fullScreenLoader.stopLoader();

                            //   self.regions.messages()[0].messageContainer.addErrorMessage({'message': responseMessage});
                        }
                    }
                ).fail(
                    function (response) {
                        totals.isLoading(false);
                    }
                ).always(
                    function () {
                    }
                );

            }
        });
    }
);

