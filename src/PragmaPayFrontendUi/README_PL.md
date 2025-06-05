# Dokumentacja modułu Pragma_PragmaFrontendUi

## 🧾 Przegląd

Moduł `Pragma_PragmaFrontendUi` integruje metodę płatności **PragmaPay** z procesem checkout w Magento 2. Udostępnia niestandardową metodę płatności z określonymi wymaganiami, takimi jak zgoda użytkownika oraz obowiązkowy adres rozliczeniowy.

---

## ✨ Funkcje

- Dodaje metodę płatności **PragmaPay** do procesu składania zamówienia.
- Wyświetla checkbox ze zgodą użytkownika pod metodą płatności.
- Wymaga podania adresu rozliczeniowego do aktywacji metody płatności.
- Dynamicznie ładuje logo PragmaPay oraz treść zgody na podstawie konfiguracji sklepu.

---

## 💳 Szczegóły metody płatności

### ✅ Zgoda użytkownika (checkbox)

- Checkbox wyświetlany jest pod metodą płatności PragmaPay.
- Użytkownik musi zaznaczyć zgodę na warunki przed złożeniem zamówienia.
- Treść zgody jest generowana dynamicznie i zawiera nazwę sklepu.

### 🏠 Wymagany adres rozliczeniowy

- Metoda PragmaPay wymaga podania adresu rozliczeniowego w trakcie składania zamówienia.
- Jest to wymuszone przez konfigurację `isBillingAddressRequired` w pliku layoutu.

---

## ⚙️ Konfiguracja

### 📝 Treść zgody

Treść zgody jest generowana w klasie `PragmaPayCheckoutConfigProvider` i dynamicznie zawiera nazwę sklepu.  
Zostaje przekazana na frontend przez obiekt `checkoutConfig`.

### 🧾 Adres rozliczeniowy

W pliku layoutu `checkout_index_index.xml` znajduje się zapis wymuszający wymaganie adresu rozliczeniowego:

```xml
<item name="isBillingAddressRequired" xsi:type="boolean">true</item>
```

---

## 🧩 Komponenty UI

Moduł zawiera niestandardowy komponent UI dla metody płatności PragmaPay.  
Komponent jest zdefiniowany w pliku `checkout_index_index.xml` i zaimplementowany w szablonie Knockout `pragma_payment.html`.

---

## 🛠 Uwagi dodatkowe

- Upewnij się, że szablon `pragma_payment.html` jest poprawnie załadowany i zmapowany w pliku `requirejs-config.js`.
- Wyczyść cache Magento po dokonaniu zmian w module:

```bash
php bin/magento cache:clean
php bin/magento cache:flush
```

- Po więcej szczegółów zajrzyj do kodu źródłowego modułu oraz testów jednostkowych.
