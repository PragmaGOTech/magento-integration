# Moduł Pragma_PragmaPayAdminUi

## Opis

Moduł `Pragma_PragmaPayAdminUi` zapewnia interfejs administracyjny do konfiguracji i zarządzania bramką płatności PragmaPay w Magento. Umożliwia sprzedawcom włączanie lub wyłączanie metody płatności, konfigurację ustawień połączenia oraz definiowanie parametrów związanych z płatnościami, takich jak limity zamówień, opcje spłaty i dane odbiorcy. Moduł obsługuje zarówno środowiska testowe (sandbox), jak i produkcyjne, zapewniając elastyczność podczas rozwoju i działania na żywo.

Dodatkowo moduł zawiera dostawców konfiguracji (`Config Providers`), które umożliwiają dostęp do ustawień takich jak szczegóły odbiorcy, konfiguracja koszyka oraz parametry połączenia. Dostawcy konfiguracji to:
- `DetailsConfigProvider` – dostarcza szczegóły nazwę metody.
- `PragmaConnectionConfigProvider` – obsługuje ustawienia połączenia, takie jak klucze partnera, URL API, tryb sandbox i inne.

## Szczegóły instalacji

Aby uzyskać informacje na temat instalacji modułu w Magento 2, zobacz [Włączanie lub wyłączanie modułów](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-subcommands-enable.html).

## Rozszerzalność

Deweloperzy rozszerzeń mogą wchodzić w interakcję z modułem `Pragma_PragmaPayAdminUi`. Aby uzyskać więcej informacji na temat mechanizmu rozszerzeń Magento, zobacz [Magento plug-ins](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/plugins.html).

[Mechanizm wstrzykiwania zależności Magento](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/depend-inj.html) umożliwia nadpisywanie funkcjonalności modułu `Pragma_PragmaPayAdminUi`.

## Lista możliwych ustawień

| Nazwa ustawienia                 | Opis w języku angielskim                    | Opis w języku polskim                       |
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

## Dodatkowe informacje

Aby uzyskać informacje o istotnych zmianach w wydaniach poprawek, zobacz [Informacje o wydaniach](https://devdocs.magento.com/guides/v2.4/release-notes/bk-release-notes.html).
