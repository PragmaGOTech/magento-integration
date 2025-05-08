# Pragma_PragmaPayCore Module

## Overview

The `Pragma_PragmaPayCore` module integrates the PragmaPay payment gateway into Magento 2. It provides functionality for processing payments, managing transactions, and handling refunds.

## Installation Details

For information about module installation in Magento 2, see [Enable or disable modules](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-subcommands-enable.html).

## Features

- Integration with the PragmaPay payment gateway.
- Support for capturing, refunding, and voiding payments.
- Transaction history management.
- Customizable payment method settings.

---

## Using the Payment Gateway Approach

The `Pragma_PragmaPayCore` module uses Magento's payment gateway framework to integrate with the PragmaPay API. This approach ensures secure and modular communication with the payment provider.

### Key Components:
1. **Payment Method Configuration**:
   - Defined in `etc/config.xml` and `etc/adminhtml/system.xml`.
   - Allows enabling/disabling the payment method, setting API credentials, and configuring other options.

2. **API Communication**:
   - Uses Magento's HTTP client to send requests to the PragmaPay API.
   - Handles operations like authorization, capture, refund, and cancellation.

3. **Command Pool**:
   - Commands are used to execute specific payment actions (e.g., authorize, capture).
   - Each command is implemented as a service and configured in `di.xml`.

4. **Request Builders**:
   - Build the request payloads for API communication.
   - Examples: `AuthorizationHeaderBuilder`, `OrderInfoBuilder`.

5. **Response Handlers**:
   - Process the responses from the payment gateway.
   - Examples: `PragmaPayCreatePayment`, `PragmaPayRefundPayment`.

---

## Extensibility

The module is designed to be extensible, allowing developers to customize or extend its functionality.

### Extending the Payment Gateway:
1. **Override Commands**:
   - Use dependency injection (DI) to replace default commands with custom implementations.
   - Example: Replace the `authorize` command in `di.xml`.

2. **Add Custom Request Builders**:
   - Add new request builders to modify or extend the API payload.

3. **Customize Response Handlers**:
   - Add or override response handlers to process additional data from the API.

4. **Modify Checkout UI**:
   - Extend the JavaScript components or templates for the payment method.

5. **Use Plugins and Observers**:
   - Add custom logic using plugins or observers for specific events (e.g., order placement, refund).

---

## Available Payment Gateway Commands

The `Pragma_PragmaPayCore` module defines the following payment gateway commands:

1. **Authorize Command**:
   - **Name**: `authorize`
   - **Purpose**: Authorizes a payment with the PragmaPay API.
   - **Request Builder**: `PragmaSubmitRequest`
   - **Response Handler**: `PragmaResponseHandlerComposite`
   - **Validator**: `PragmaPayCreatePayment`

2. **Capture Command**:
   - **Name**: `capture`
   - **Purpose**: Captures an authorized payment.
   - **Request Builder**: `PragmaPayGetPaymentStatusRequest`
   - **Validator**: `PragmaPayGetPaymentStatus`

3. **Refund Command**:
   - **Name**: `refund`
   - **Purpose**: Processes a refund for a payment.
   - **Request Builder**: `PragmaPayRefundPaymentRequest`
   - **Response Handler**: `PragmaPayRefundResponse`
   - **Validator**: `PragmaPayRefundPayment`

4. **Cancel Command**:
   - **Name**: `cancel`
   - **Purpose**: Cancels a payment.
   - **Request Builder**: `PragmaPayCancelPaymentRequest`

---

## Developer Notes

- The module uses Magento's dependency injection mechanism for extensibility.
- Customizations can be made by overriding layout files, templates, or using plugins and observers.
- Ensure that the `pl_PL.csv` file contains all necessary translations for the module's i18n strings.
- Test all customizations thoroughly in a development environment before deploying to production.
