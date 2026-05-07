# Changelog

All notable changes to `dashed-pages` will be documented in this file.

## v4.1.1 - 2026-05-07

### Fixed
- Migration toegevoegd die `order`-kolom aan `dashed__pages` toevoegt als die ontbreekt. De Sorteren-actie uit v4.1.0 schreef anders naar een niet-bestaande kolom op installaties waar de pages-tabel uit een tijd dateert vóór de gestandaardiseerde `createTableForVisitableModel` (die `order` standaard meelevert).

## v4.1.0 - 2026-05-07

### Added
- "Sorteren"-header-action op de Pages list-page via `Dashed\DashedCore\Filament\Concerns\HasNestableSortingAction`. Pages kunnen nu via slepen genested en geordend worden zonder per-item Edit. Vereist `dashed-core` v4.6.0+.

## v4.0.5 - 2026-04-27

- Bind het huidige visitable model voor popup-targeting zodat popup-rules kunnen filteren op de live page/model.
- Refactor: container-binding voor de current visitable losgelaten ten gunste van een Octane-veilige aanpak (geen lekkende state tussen requests).

## 1.0.0 - 202X-XX-XX

- initial release
