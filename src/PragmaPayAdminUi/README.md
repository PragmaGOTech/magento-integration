# Pragma_PragmaPayAdminUi module

## Description

The `Pragma_PragmaPayAdminUi` module provides an administrative interface for configuring and managing the PragmaPay payment gateway in Magento. It allows merchants to enable or disable the payment method, configure connection settings, and define payment-related parameters such as order limits, repayment options, and payee details. The module supports both sandbox and production environments, ensuring flexibility during development and live operations.

## Installation details

For information about a module installation in Magento 2, see [Enable or disable modules](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-subcommands-enable.html).

## Extensibility

Extension developers can interact with the Pragma_PragmaPayAdminUi module. For more information about the Magento extension mechanism, see [Magento plug-ins](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/plugins.html).

[The Magento dependency injection mechanism](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/depend-inj.html) enables you to override the functionality of the Pragma_PragmaPayAdminUi module.

## List of Possible Settings

| Setting Name                     | English Description                          | Polish Description                          |
|----------------------------------|----------------------------------------------|---------------------------------------------|
| PragmaPay                        | PragmaPay                                   | PragmaPay                                   |
| Enable                           | Enable                                      | Włącz                                      |
| Connection                       | Connection                                  | Połączenie                                 |
| Sandbox mode?                    | Sandbox mode?                               | Tryb Sandbox?                              |
| Api url                          | API URL                                    | URL API                                    |
| Sandbox Api Url                  | Sandbox API URL                            | URL API Sandbox                            |
| Partner key                      | Partner key                                | Klucz Partnera                             |
| Sandbox Partner key              | Sandbox Partner key                        | Klucz Partnera Sandbox                     |
| Partner Secret                   | Partner Secret                             | Sekret Partnera                            |
| Sandbox Partner Secret           | Sandbox Partner Secret                     | Sekret Partnera Sandbox                    |
| Return URL                       | Return URL                                 | URL Powrotu                                |
| Notification URL                 | Notification URL                           | URL Powiadomień                            |
| Cancel URL                       | Cancel URL                                 | URL Anulowania                             |
| Log cart request                 | Log cart request                           | Loguj żądanie koszyka                      |

## Additional information

For information about significant changes in patch releases, see [Release information](https://devdocs.magento.com/guides/v2.4/release-notes/bk-release-notes.html).
