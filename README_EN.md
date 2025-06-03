# PragmaPay Module - Deferred Payments for Companies for Magento 2 version ^2.4.5

## Table of Contents

1. [Description](#description)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Cart](#cart)

## Description
PragmaPay payment module – deferred payments for businesses.  
This module is compatible with Magento 2 version 2.4.x

The module provides the following functionalities:
* Creating payments in the deferred payment system PragmaPay
* Automatic receipt of notifications from the payment system and updating the order status
* Displaying the payment method on the order summary page

## Requirements
* PHP version compatible with the installed Magento 2 version requirements

## Installation
There are 2 different solutions to install the extensions:

* Solution #1. Install via Composer (Recommend)
* Solution #2: Copying files to the server (Not recommend)

Important:
We recommend you to duplicate your live store on a staging/test site and try installation on it in advanced.
Back up Magento files and the store database.

#### Install via Composer
Run the following command in Magento 2 root folder:

* composer require pragmagotech/magento2-module-pragmapay
* bin/magento module:enable Pragma_PragmaPayAdminUi Pragma_PragmaPayCalculator Pragma_PragmaCore Pragma_PragmaFrontendUi Pragma_PragmaWebApi
* bin/magento setup:upgrade
* bin/magento setup:di:compile
* bin/magento setup:static-content:deploy

#### Copying files to the server
1. Download the latest version [download][external-link-1]
2. Unzip the downloaded zip file
3. Copy the contents of the `src` directory into the `app/code/Pragma` directory within the main Magento2 installation directory. **If the directory does not exist, create it.**

After installation, run the following commands from the console:
* bin/magento module:enable Pragma_PragmaPayAdminUi Pragma_PragmaPayCalculator Pragma_PragmaCore Pragma_PragmaFrontendUi Pragma_PragmaWebApi
* bin/magento setup:upgrade
* bin/magento setup:di:compile
* bin/magento setup:static-content:deploy

## Configuration
1. Navigate to the Magento 2 admin panel.
2. Go to **Stores** -> **Configuration**.
3. Next, navigate to **Sales** -> **Payment Methods**.
4. Find the **PragmaPay** section.
5. Press the **Save config** button to save your changes.

#### NOTE
After changing any value, for security reasons, the current **API key** or **Sandbox API key** must be provided in the configuration.

### Configuration Options
#### Main settings
| Parameter | Description                  |
|-----------|------------------------------|
| Enabled?  | Activates the payment method.|

#### Connection settings
| Setting Name           | English Description | Polish Description |
|------------------------|---------------------|--------------------|
| PragmaPay              | PragmaPay           | PragmaPay          |
| Enable                 | Enable              | Włącz              |
| Connection             | Connection          | Połączenie         |
| Sandbox mode?          | Sandbox mode?       | Tryb Sandbox       |
| API URL                | API URL             | URL API            |
| Sandbox API URL        | Sandbox API URL     | URL API Sandbox    |
| Partner key            | Partner key         | Klucz Partnera     |
| Sandbox Partner key    | Sandbox Partner key | Klucz Partnera Sandbox|
| Partner Secret         | Partner Secret      | Sekret Partnera    |
| Sandbox Partner Secret | Sandbox Partner Secret | Sekret Partnera Sandbox|
| Return URL             | Return URL          | URL Powrotu        |
| Notification URL       | Notification URL    | URL Powiadomień    |
| Cancel URL             | Cancel URL          | URL Anulowania     |
| Log cart request       | Log cart request    | Loguj żądanie koszyka|

## Cart
The PragmaPay deferred payment method for businesses appears in the order summary when the following conditions are met:
* The cart's minimum amount reaches the value set in the payment method code: `\Pragma\PragmaPayCore\Api\PragmaPayCartConfigProviderInterface::MINIMUM_AMOUNT`
* The cart's maximum amount reaches the value set in the payment method code: `\Pragma\PragmaPayCore\Api\PragmaPayCartConfigProviderInterface::MAXIMUM_AMOUNT`
* The customer enters a VAT identification number (NIP)

#### Setting visibility for VAT number in Magento 2 checkout
1. Navigate to the Magento 2 admin panel.
2. Go to **Stores** -> **Configuration**.
3. Next, navigate to **Customers** -> **Customer configuration** -> **Create New Account Options**.
4. Set **Show VAT Number on Storefront** = **yes**.
5. Press the **Save config** button to save your changes.

<!--external links:-->
[external-link-1]: 
