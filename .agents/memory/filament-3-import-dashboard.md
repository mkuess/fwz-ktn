---
name: Filament 3 Importer hooks, custom Dashboard override, and localization
description: How to hook into Filament 3 CSV imports, override the default Dashboard page with custom widgets, and localize the panel to a non-English locale
---

## Importer lifecycle hooks
`Filament\Actions\Imports\Importer` has no `beforeSave`/`afterSave` methods to override directly by name checking — it uses a generic `callHook(string $hook)` that calls the method only if it exists on the subclass. Valid hook names (in call order): `beforeValidate`, `afterValidate`, `beforeFill`, `afterFill`, `beforeSave`, `beforeCreate`/`beforeUpdate`, `afterSave`, `afterCreate`/`afterUpdate`.

**Why:** Grepping the base class for a `beforeSave` method definition finds nothing (it's dynamically dispatched), which can look like the hook doesn't exist. It does — just define `protected function beforeSave(): void {}` in your custom Importer subclass and it will be called.

**How to apply:** Use `beforeSave()` in a custom `Importer` subclass to force fields (e.g. status/approval flags) on imported records before they're persisted.

## Overriding the default Dashboard page
To replace Filament's built-in dashboard widgets, create `App\Filament\Pages\Dashboard extends Filament\Pages\Dashboard` (not the generic `Page` class — `make:filament-page --type=custom` scaffolds the wrong base class) and override `getWidgets(): array`. This fully replaces the widget list for that page (no merging with panel-level widgets).

**Why:** The base `Dashboard::getWidgets()` returns `Filament::getWidgets()` (panel-registered widgets); overriding it in a subclass swaps in your own set entirely.

**How to apply:** Register the custom page via `discoverPages()`/explicit `->pages([App\Filament\Pages\Dashboard::class])` in the Panel provider, and leave `->widgets([])` empty if all widgets are meant to live only on this custom Dashboard.

## Localizing a Filament 3 panel (e.g. to German)
`Filament\Panel` has no `->locale()` builder method in Filament 3 — calling it throws `BadMethodCallException`. Filament core packages (support/forms/tables/actions/notifications/infolists/filament) already ship translated `resources/lang/<locale>` files for many locales (e.g. `de`), so all built-in UI strings translate automatically once the app-wide locale is set.

**Why:** Localization in Filament 3 panels is driven entirely by Laravel's app locale (`config('app.locale')` / `APP_LOCALE` env var), not a per-panel setting.

**How to apply:** Set `APP_LOCALE`/`APP_FALLBACK_LOCALE` in `.env` (and matching defaults in `config/app.php`) to the target locale; add a `resources/lang/<locale>.json` file for app-specific string overrides (e.g. "Are you sure?" → "Sind Sie sicher?"); translate custom Resource/Widget labels manually via `->label()`, `$modelLabel`, `$pluralModelLabel`, since those are app-specific text Filament can't auto-translate.
