define([
    'jquery',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/step-navigator',
    'ko'
], function ($, totals, stepNavigator, ko) {
    'use strict';

    return function () {
        const buttonId = 'calculatorPragmaPay';
        const calculatedPrice = ko.observable(0);

        function updatePriceFromTotals() {
            const grandTotal = totals.totals()?.grand_total;
            if (!isNaN(grandTotal)) {
                calculatedPrice(Math.round(grandTotal * 100));
            }
        }

        function setupCalculator($btn) {
            $btn.hide();

            // Setup and track price
            updatePriceFromTotals();
            totals.totals.subscribe(updatePriceFromTotals);
            calculatedPrice.subscribe(price => $btn.attr('data-price', price));

            // Use KO to wait until steps are available
            ko.computed(() => {
                const steps = stepNavigator.steps();
                const paymentStep = steps.find(step => step.code === 'payment');

                if (paymentStep && typeof paymentStep.isVisible === 'function') {
                    // Show immediately if already visible
                    if (paymentStep.isVisible()) {
                        $btn.show();
                    }

                    // Dynamically observe visibility
                    paymentStep.isVisible.subscribe((visible) => {
                        visible ? $btn.show() : $btn.hide();
                    });
                } else {
                    // Not checkout (e.g. cart page)
                    $btn.show();
                }
            });

            // Click logic
            $btn.off('click').on('click', async function () {
                const partnerKey = $btn.data('partner-key');
                const price = calculatedPrice();

                if (!partnerKey || isNaN(price)) return;

                import('https://partners-loanfront.box.pragmago.tech/sdk.js')
                    .then(({ PragmaGo }) => {
                        const { initializePragmaPayCalculator } = new PragmaGo();
                        initializePragmaPayCalculator({
                            partnerKey,
                            calculator: {
                                amount: price,
                                currency: 'PLN'
                            }
                        });
                    })
                    .catch((err) => {
                        console.error('❌ Failed to load PragmaGo SDK:', err);
                    });
            });
        }

        // Wait for button to exist in DOM
        const observer = new MutationObserver(() => {
            const $btn = $('#' + buttonId);
            if ($btn.length) {
                observer.disconnect();
                setupCalculator($btn);
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });
    };
});
