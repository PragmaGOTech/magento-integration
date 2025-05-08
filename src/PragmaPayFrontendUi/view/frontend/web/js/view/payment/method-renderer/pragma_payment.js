define(
    [
        'paymentPragmaExtended',
        'ko',
    ],
    function (Component,ko) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Pragma_PragmaPayFrontendUi/pragma_payment',
                logoSrc: window.checkoutConfig.payment.pragmaPayment.logoSrc,
                logoDarkSrc: window.checkoutConfig.payment.pragmaPayment.logoDarkSrc,
                postPlaceOrderData: 'pragma/data/getPostPlaceOrderData',
                pragmaAgreement: ko.observable(true),
                agreementText: window.checkoutConfig.payment.pragmaPayment.agreementText,
                language: window.checkoutConfig.payment.pragmaPayment.language,
            }
        });
    }
);
