define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        if (window.checkoutConfig.payment.pragmaPayment.isActive) {
            rendererList.push(
                {
                    type: 'pragma_payment',
                    component: 'Pragma_PragmaPayFrontendUi/js/view/payment/method-renderer/pragma_payment'
                },
            );
        }

        return Component.extend({});
    }
);
