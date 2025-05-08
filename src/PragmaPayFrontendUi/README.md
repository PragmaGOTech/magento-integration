# Pragma_PragmaFrontendUi Module Documentation

## 🧾 Overview

The `Pragma_PragmaFrontendUi` module integrates the **PragmaPay** payment method into the Magento 2 checkout process. It provides a custom payment method with specific requirements, such as user agreement consent and billing address validation.

---

## ✨ Features

- Adds the **PragmaPay** payment method to the checkout process.
- Displays a checkbox for user agreement consent under the payment method.
- Requires a billing address for the payment method to be available.
- Dynamically loads the PragmaPay logo and agreement text based on store configuration.

---

## 💳 Payment Method Details

### ✅ Agreement Checkbox

- A checkbox is displayed under the PragmaPay payment method.
- The user must check the box to agree to the terms before placing the order.
- The agreement text is dynamically generated and includes the store name.

### 🏠 Billing Address Requirement

- The PragmaPay payment method requires a billing address to be provided during checkout.
- This is enforced by the `isBillingAddressRequired` configuration in the layout file.

---

## ⚙️ Configuration

### 📝 Agreement Text

The agreement text is defined in the `PragmaPayCheckoutConfigProvider` class and dynamically includes the store name.  
It is passed to the frontend via the `checkoutConfig` object.

### 🧾 Billing Address

The `checkout_index_index.xml` layout file specifies that a billing address is required for the PragmaPay payment method:

```xml
<item name="isBillingAddressRequired" xsi:type="boolean">true</item>
```

---

## 🧩 UI Components

The module introduces a custom UI component for the PragmaPay payment method.  
The component is defined in the `checkout_index_index.xml` layout file and implemented in the `pragma_payment.html` Knockout template.

---

## 🛠 Additional Notes

- Ensure the `pragma_payment.html` template is correctly loaded and mapped in `requirejs-config.js`.
- Clear the Magento cache after making changes to the module:

```bash
php bin/magento cache:clean
php bin/magento cache:flush
```

- For further details, refer to the module's source code and unit tests.
