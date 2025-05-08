# Moduł Pragma_PragmaPayCore

## Przegląd

Moduł `Pragma_PragmaPayCore` integruje bramkę płatności PragmaPay z Magento 2. Zapewnia funkcjonalność przetwarzania
płatności, zarządzania transakcjami i obsługi zwrotów.

## Szczegóły instalacji

Aby uzyskać informacje na temat instalacji modułu w Magento 2,
zobacz [Włączanie lub wyłączanie modułów](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-subcommands-enable.html).

## Funkcje

- Integracja z bramką płatności PragmaPay.
- Obsługa przechwytywania, zwrotów i anulowania płatności.
- Zarządzanie historią transakcji.
- Konfigurowalne ustawienia metody płatności.

   ---

## Korzystanie z podejścia bramki płatności

Moduł `Pragma_PragmaPayCore` wykorzystuje framework bramki płatności Magento do integracji z API PragmaPay. Takie
podejście zapewnia bezpieczną i modułową komunikację z dostawcą płatności.

### Kluczowe komponenty:

1. **Konfiguracja metody płatności**:
    - Zdefiniowana w plikach `etc/config.xml` i `etc/adminhtml/system.xml`.
    - Umożliwia włączanie/wyłączanie metody płatności, ustawianie danych uwierzytelniających API i konfigurowanie innych
      opcji.

2. **Komunikacja z API**:
    - Wykorzystuje klienta HTTP Magento do wysyłania żądań do API PragmaPay.
    - Obsługuje operacje takie jak autoryzacja, przechwytywanie, zwrot i anulowanie.

3. **Pula komend**:
    - Komendy są używane do wykonywania określonych akcji płatniczych (np. autoryzacja, przechwytywanie).
    - Każda komenda jest zaimplementowana jako usługa i skonfigurowana w pliku `di.xml`.

4. **Budowniki żądań**:
    - Tworzą ładunki żądań do komunikacji z API.
    - Przykłady: `AuthorizationHeaderBuilder`, `OrderInfoBuilder`.

5. **Obsługa odpowiedzi**:
    - Przetwarza odpowiedzi z bramki płatności.
    - Przykłady: `PragmaPayCreatePayment`, `PragmaPayRefundPayment`.

   ---

## Rozszerzalność

Moduł został zaprojektowany z myślą o rozszerzalności, umożliwiając deweloperom dostosowywanie lub rozszerzanie jego
funkcjonalności.

### Rozszerzanie bramki płatności:

1. **Nadpisywanie komend**:
    - Użyj mechanizmu dependency injection (DI), aby zastąpić domyślne komendy własnymi implementacjami.
    - Przykład: Zastąpienie komendy `authorize` w pliku `di.xml`.

2. **Dodawanie niestandardowych budowników żądań**:
    - Dodaj nowe budowniki żądań, aby modyfikować lub rozszerzać ładunek API.

3. **Dostosowywanie obsługi odpowiedzi**:
    - Dodaj lub nadpisz obsługę odpowiedzi, aby przetwarzać dodatkowe dane z API.

4. **Modyfikowanie interfejsu użytkownika w kasie**:
    - Rozszerz komponenty JavaScript lub szablony dla metody płatności.

5. **Używanie pluginów i obserwatorów**:
    - Dodaj niestandardową logikę za pomocą pluginów lub obserwatorów dla określonych zdarzeń (np. składanie zamówienia,
      zwrot).

   ---

## Dostępne komendy bramki płatności

Moduł `Pragma_PragmaPayCore` definiuje następujące komendy bramki płatności:

1. **Komenda autoryzacji**:
    - **Nazwa**: `authorize`
    - **Cel**: Autoryzuje płatność za pomocą API PragmaPay.
    - **Budowniki żądań**: `PragmaSubmitRequest`
    - **Obsługa odpowiedzi**: `PragmaResponseHandlerComposite`
    - **Walidator**: `PragmaPayCreatePayment`

2. **Komenda przechwytywania**:
    - **Nazwa**: `capture`
    - **Cel**: Przechwytuje autoryzowaną płatność.
    - **Budowniki żądań**: `PragmaPayGetPaymentStatusRequest`
    - **Walidator**: `PragmaPayGetPaymentStatus`

3. **Komenda zwrotu**:
    - **Nazwa**: `refund`
    - **Cel**: Przetwarza zwrot płatności.
    - **Budowniki żądań**: `PragmaPayRefundPaymentRequest`
    - **Obsługa odpowiedzi**: `PragmaPayRefundResponse`
    - **Walidator**: `PragmaPayRefundPayment`

4. **Komenda anulowania**:
    - **Nazwa**: `cancel`
    - **Cel**: Anuluje płatność.
    - **Budowniki żądań**: `PragmaPayCancelPaymentRequest`

   ---

## Uwagi dla deweloperów

- Moduł wykorzystuje mechanizm dependency injection Magento do rozszerzalności.
- Dostosowania można dokonywać poprzez nadpisywanie plików układu, szablonów lub za pomocą pluginów i obserwatorów.
- Upewnij się, że plik `pl_PL.csv` zawiera wszystkie niezbędne tłumaczenia dla ciągów i18n modułu.
- Przetestuj wszystkie dostosowania dokładnie w środowisku deweloperskim przed wdrożeniem na produkcję.
