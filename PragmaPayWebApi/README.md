# Pragma_PragmaWebUi module

## Description

The `Pragma_PragmaWebUi` module provides API endpoing for PragmaPay Gateway notifications(webhook).

## Installation details

For information about a module installation in Magento 2, see [Enable or disable modules](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-subcommands-enable.html).

## Extensibility

Extension developers can interact with the `Pragma_PragmaWebUi` module. For more information about the Magento extension mechanism, see [Magento plug-ins](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/plugins.html).

[The Magento dependency injection mechanism](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/depend-inj.html) enables you to override the functionality of the `Pragma_PragmaWebUi` module.

## Web Endpoints

The `Pragma_PragmaWebUi` module introduces the following web endpoints to handle API notifications from PragmaPay Gateway:

### 1. `/rest/V1/pragma/calculate`

**Description:**
Handles requests to calculate payment options based on product or cart data.

**Method:** `POST`

**Request Payload:**
```json
{
    "id": "eb1c1a38-d667-4c06-a7f8-c983db600cd2",
    "object": {
        "timestamp": 1744120792028304,
        "paymentId": "76da9d7d-a898-5f0f-b255-16f9aea11f54",
        "repaymentPeriod": {
            "value": 14,
            "type": "DAYS"
        },
        "items": [
            {
                "partnerItemId": "000000045",
                "status": "FINANCED",
                "value": {
                    "format": "MINOR",
                    "amount": 15744,
                    "currency": "PLN"
                }
            }
        ]
    },
    "type": "PAYMENT_CHANGED",
    "date": "2025-04-08 15:59:54",
    "timestamp": 1744120792028304
}
