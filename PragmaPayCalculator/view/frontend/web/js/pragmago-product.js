define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        const $btn = $('#calculatorPragmaPay');
        if (!$btn.length) {
            return;
        }

        $btn.on('click', function () {
            const partnerKey = $btn.data('partner-key');
            const price = parseInt($btn.data('price'));

            if (!partnerKey || isNaN(price)) {
                return;
            }

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
    };
});
