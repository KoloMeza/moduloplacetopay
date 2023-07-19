define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/translate',
        'mage/url',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/totals'
    ],
    function (
        $,
        Component,
        placeOrderAction,
        selectPaymentMethodAction,
        customer,
        checkoutData,
        additionalValidators,
        errorProcessor,
        fullScreenLoader,
        $t,
        url,
        globalMessageList,
        totals
    ) {
        'use strict';

        return Component.extend({
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'PlacetoPay_Payments/payment/placetopay'
            },

            placeOrder: function (data, event) {
                if (event)
                    event.preventDefault();
                var self = this,
                    placeOrder,
                    emailValidationResult = customer.isLoggedIn(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';
                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
                if (emailValidationResult && this.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                    $.when(placeOrder).done(function () {
                        self.isPlaceOrderActionAllowed(true);
                    }).done(this.afterPlaceOrder.bind(this));
                    return true;
                }
                return false;
            },

            selectPaymentMethod: function () {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                return true;
            },

            afterPlaceOrder: function () {
                $.post(url.build('placetopay/payment/data'), 'json')
                    .done(data => {
                        window.location = data.url;
                    }).fail(response => {
                    console.log(this.messageContainer);
                    errorProcessor.process('error', this.messageContainer);
                }).always(() => {
                    fullScreenLoader.stopLoader();
                });
            },

            getLogo: function () {
                return '<img src="'+window.checkoutConfig.payment.placetopay.logo+'" height="48" alt="PlacetoPay"/>';
            },

            hasPendingPayment: function () {
                return window.checkoutConfig.payment.placetopay.order.hasPendingOrder;
            },

            hasCifin: function () {
                return window.checkoutConfig.payment.placetopay.hasCifinMessage;
            },

            pendingMessage: function () {
                let orderId = window.checkoutConfig.payment.placetopay.order.id;
                let phone = window.checkoutConfig.payment.placetopay.order.phone;

                let email = '<a href="mailto:' +
                    window.checkoutConfig.payment.placetopay.order.email + '">' +
                    window.checkoutConfig.payment.placetopay.order.email + '</a>';

                let authorization = window.checkoutConfig.payment.placetopay.order.authorization;
                let data = [orderId, phone, email, authorization];

                return $t("At this time your order #%1 display a checkout transaction which is pending receipt of confirmation from your financial institution, please wait a few minutes and check back later to see if your payment was successfully confirmed. For more information about the current state of your operation you may contact our customer service line at %2 or send your concerns to the email %3 and ask for the status of the transaction: '%4'.")
                    .replace(/%(\d+)/g, (_, n) => data[+n - 1]);
            },

            securityMessage: function () {
                let url = window.checkoutConfig.payment.placetopay.url;
                let name = window.checkoutConfig.payment.placetopay.legalName;
                let merchant = '<b>EGM Ingeniería Sin Fronteras S.A.S</b>';
                let brand = '<b>Placetopay</b>';
                let data = [url, name, merchant, brand];

                return $t('Any person who realizes a purchase in the site %1, acting freely and voluntarily, authorizes to %2, through the service provider %3 y/o %4 to consult and request information from credit, financial, commercial performance and services to third parties, even in countries of the same nature in the central risk, generating a footprint consultation.')
                    .replace(/%(\d+)/g, (_, n) => data[+n - 1]);
            },

            getPaymentIcons: function () {
                let paymentMethods = window.checkoutConfig.payment.placetopay.paymentMethods;
                let icons = [];
                const franchises = {
                    'CDNSA': 'codensa',
                    'CR_AM': 'american_express',
                    'CR_CR': 'credencial',
                    'CR_DN': 'diners_club',
                    'CR_MC': 'mastercard',
                    'CR_VE': 'visa',
                    'CR_VS': 'visa',
                    'DF_AM': 'american_express',
                    'DF_DN': 'diners_club',
                    'DF_DS': 'discover',
                    'DF_MC': 'mastercard',
                    'DF_VS': 'visa',
                    'DISCO': 'discover',
                    'ID_AM': 'american_express',
                    'ID_DN': 'diners_club',
                    'ID_DS': 'discover',
                    'ID_MC': 'mastercard',
                    'ID_VS': 'visa',
                    'RM_AM': 'american_express',
                    'RM_DN': 'diners_club',
                    'RM_MC': 'mastercard',
                    'RM_VS': 'visa',
                    'SOMOS': 'somos',
                    'TC_AM': 'mastercard',
                    'TC_DN': 'diners_club',
                    'TC_DS': 'discover',
                    'TC_MC': 'mastercard',
                    'TC_VS': 'visa',
                    'TYDAK': 'alkosto',
                    'TYDEX': 'exito_card',
                    'TS_AM': 'mastercard',
                    'TS_DN': 'diners_club',
                    'TS_DS': 'discover',
                    'TS_MC': 'mastercard',
                    'TS_VS': 'visa',
                    'TSIAM': 'mastercard',
                    'TSIDN': 'diners_club',
                    'TSIDS': 'discover',
                    'TSIMC': 'mastercard',
                    'TSIVS': 'visa',
                    'MT_AM': 'mastercard',
                    'MT_DN': 'diners_club',
                    'MT_DS': 'discover',
                    'MT_MC': 'mastercard',
                    'MT_VS': 'visa',
                    'AT_AM': 'mastercard',
                    'AT_DN': 'diners_club',
                    'AT_DS': 'discover',
                    'AT_MC': 'mastercard',
                    'AT_VS': 'visa',
                    'COMDI': 'comfandi',
                    'PS_AM': 'american_express',
                    'PS_DN': 'diners_club',
                    'PS_DS': 'discover',
                    'PS_MC': 'mastercard',
                    'PS_VS': 'visa',
                    'EB_VS': 'visa',
                    'EB_MC': 'mastercard',
                    'EB_AM': 'american_express',
                    'EBATH': 'ath_card',
                    'PS_MS': 'maestro',
                    'ATHMV': 'ath_movil',
                    'T1_BC': 'bancolombia',
                };

                paymentMethods.forEach(function (icon) {
                    // console.log(franchises[icon]);
                    // icons.push('<img src="'+window.checkoutConfig.payment.placetopay.media+'/icon_card/'+franchises[icon]+'.svg" class="acceptance_logo" alt="'+icon+'" />');
                    icons.push('<img src="https://www.placetopay.com/images/providers/' + icon + '.png" alt="" class="acceptance_logo" />');
                });

                return icons.join(' ');
            },

            getMinimum: function () {
                return window.checkoutConfig.payment.placetopay.minimum;
            },

            getMaximum: function () {
                return window.checkoutConfig.payment.placetopay.maximum;
            },

            getTotal: function () {
                return totals.totals().grand_total;
            },

            showErrorMessage: function (message) {
                document.getElementById('payment-method-placetopay').scrollIntoView(true);
                this.messageContainer.addErrorMessage({message: message});
            },

            validate: function () {
                let isValid = true;

                if (! this.isMinimumValid()) {
                    this.showErrorMessage(
                        $t('Does not meet the minimum amount to process the order, the minimum amount must be greater or equal to value to use this payment gateway.')
                            .replace('value', this.getMinimum())
                    );

                    isValid = false;
                } else if (! this.isMaximumValid()) {
                    this.showErrorMessage(
                        $t('Exceeds the maximum amount allowed to process the order, it must be less or equal to value to use this payment gateway.')
                            .replace('value', this.getMaximum())
                    );

                    isValid = false;
                } else if (! this.allowPendingPayment()) {
                    if (this.hasPendingPayment()) {
                        this.showErrorMessage(
                            $t('The payment could not be continued because a pending order has been found.')
                        );

                        isValid = false;
                    }
                }

                return isValid;
            },

            isMinimumValid: function () {
                return ! (this.getMinimum() != null && this.getTotal() < this.getMinimum());
            },

            isMaximumValid: function () {
                return ! (this.getMaximum() != null && this.getTotal() > this.getMaximum());
            },

            allowPendingPayment: function () {
                return window.checkoutConfig.payment.placetopay.allowPendingPayments;
            }
        });
    }
);
