# Moduł PragmaPay - odroczone płatności dla firm dla Magento 2 w wersji ^2.4.5

## Spis treści

1. [Opis](#opis)
2. [Wymagania](#wymagania)
3. [Instalacja](#instalacja)
4. [Konfiguracja](#konfiguracja)
5. [Koszyk](#koszyk)

## Opis
Moduł płatności PragmaPay - odroczone płatności dla firmo.
Moduł współpracuje z Magento 2 w wersji 2.4.x

Moduł dodaje następujące funkcjonalności
* Utworzenie płatności w sytemie odroczonej płatności PragmaPay
* Możliwość automatycznego odbierania notyfikacji z systemu płatności i zmianę statusu zamówienia
* Wyświetlenie metody płatności na stronie podsumowania zamówienia

## Wymagania
* Wersja PHP zgodna z wymaganiami zainstalowanej wersji Magento 2

## Instalacja
#### Kopiując pliki na serwer
1. Pobierz najnowszą wersję [pobierz][external-link-1]
2. Rozpakuj pobrany plik zip
3. Skopiuj zawartość katalogu `src` do katalogu `app/code/Pragma` w głownym katalogu instalacja Magento2. **Jeżeli katalog nie istnieje - utwórz go.**

Po instalacji z poziomu konsoli uruchom:
* bin/magento module:enable Pragma_PragmaPayAdminUi Pragma_PragmaPayCalculator Pragma_PragmaCore Pragma_PragmaFrontendUi Pragma_PragmaWebApi
* bin/magento setup:upgrade
* bin/magento setup:di:compile
* bin/magento setup:static-content:deploy

## Konfiguracja
1. Przejdź do panelu administracynjego Magento 2.
2. Przejdź do  **Stores** -> **Configuration**.
3. Następnie **Sales** -> **Payment Methods**.
4. Sekcja **PragmaPay**
5. Aby zapisać wprowadzone zmiany należy wcisnąć przycisk **Save config**.

#### UWAGA
Po zmianie dowolnej wartości ze względów bezpieczeństwa, w konfiguracji należy podać aktualny **klucz API** lub **klucz API Sanbox**

### Opcje ustawień
#### Główne ustawienia
| Parameter |                            |
|-----------|----------------------------|
| Włącz?    | Aktywuje metodę płatności. |

#### Ustawienia połączenia
| Setting Name                     | English Description                        | Polish Description                 |
|----------------------------------|--------------------------------------------|------------------------------------|
| PragmaPay                        | PragmaPay                                  | PragmaPay                          |
| Enable                           | Enable                                     | Włącz                              |
| Connection                       | Connection                                 | Połączenie                         |
| Sandbox mode?                    | Sandbox mode?                              | Tryb Sandbox                       |
| Api url                          | API URL                                    | URL API                            |
| Sandbox Api Url                  | Sandbox API URL                            | URL API Sandbox                    |
| Partner key                      | Partner key                                | Klucz Partnera                     |
| Sandbox Partner key              | Sandbox Partner key                        | Klucz Partnera Sandbox             |
| Partner Secret                   | Partner Secret                             | Sekret Partnera                    |
| Sandbox Partner Secret           | Sandbox Partner Secret                     | Sekret Partnera Sandbox            |
| Return URL                       | Return URL                                 | URL Powrotu                        |
| Notification URL                 | Notification URL                           | URL Powiadomień                    |
| Cancel URL                       | Cancel URL                                 | URL Anulowania                     |
| Log cart request                 | Log cart request                           | Loguj żądanie koszyka              |

## Koszyk
Metoda płatności Pragma - odroczone płatności dla firm się w podsumowaniu zamówienia, gdy zostaną spełnione następujące warunki:
* kwota minimalna w koszyku osiągnie wartość ustawioną w kodzie metody płatności, czyli `\Pragma\PragmaPayCore\Api\PragmaPayCartConfigProviderInterface::MINIMUM_AMOUNT`
* kwota maksymalna w koszyku osiągnie wartość ustawioną w kodzie metody płatności, czyli `\Pragma\PragmaPayCore\Api\PragmaPayCartConfigProviderInterface::MAXIMUM_AMOUNT`
* klient wprowadzi identyfikator NIP

#### Ustawienie widoczności identyfikatora NIP w checkout Magento 2
1. Przejdź do panelu administracynjego Magento 2.
2. Przejdź do  **Stores** -> **Configuration**.
3. Następnie **Customers** -> **Customer configuration** -> **Create New Account Options**.
4. **Show VAT Number on Storefront** = **yes**
5. Aby zapisać wprowadzone zmiany należy wcisnąć przycisk **Save config**.


<!--external links:-->
[external-link-1]: 
