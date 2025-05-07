# Moduł Pragma_PragmaWebUi

## Opis

Moduł `Pragma_PragmaWebUi` dostarcza API do obsługi powiadomień (webhooków) z PragmaPay Gateway. Umożliwia przetwarzanie aktualizacji statusów płatności oraz zarządzanie zamówieniami na podstawie otrzymanych powiadomień.

Aby uzyskać szczegółową dokumentację, zobacz [Dokumentację API PragmaGo](https://pragma-pay.readme.io/reference/powiadomienia-z-systemu-pragmago).

## Szczegóły instalacji

Aby uzyskać informacje na temat instalacji modułu w Magento 2, zobacz [Włączanie lub wyłączanie modułów](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-subcommands-enable.html).

## Rozszerzalność

Deweloperzy rozszerzeń mogą wchodzić w interakcję z modułem `Pragma_PragmaWebUi`. Aby uzyskać więcej informacji na temat mechanizmu rozszerzeń Magento, zobacz [Magento plug-ins](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/plugins.html).

[Mechanizm wstrzykiwania zależności Magento](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/depend-inj.html) umożliwia nadpisywanie funkcjonalności modułu `Pragma_PragmaWebUi`.

## Punkty końcowe API

Moduł `Pragma_PragmaWebUi` wprowadza następujące punkty końcowe API do obsługi powiadomień z PragmaPay Gateway:

### 1. `/rest/V1/pragma/calculate`

**Opis:**
Obsługuje żądania obliczania opcji płatności na podstawie danych produktu lub koszyka.

**Metoda:** `POST`

**Przykładowe dane żądania:**
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
