---
name: Filament 3 Importer hooks and custom Dashboard override
description: How to hook into Filament 3 CSV imports and override the default Dashboard page with custom widgets
---

## Importer lifecycle hooks
`Filament\Actions\Imports\Importer` has no `beforeSave`/`afterSave` methods to override directly by name checking — it uses a generic `callHook(string $hook)` that calls the method only if it exists on the subclass. Valid hook names (in call order): `beforeValidate`, `afterValidate`, `beforeFill`, `afterFill`, `beforeSave`, `beforeCreate`/`beforeUpdate`, `afterSave`, `afterCreate`/`afterUpdate`.

**Why:** Grepping the base class for a `beforeSave` method definition finds nothing (it's dynamically dispatched), which can look like the hook doesn't exist. It does — just define `protected function beforeSave(): void {}` in your custom Importer subclass and it will be called.

**How to apply:** Use `beforeSave()` in a custom `Importer` subclass to force fields (e.g. status/approval flags) on imported records before they're persisted.

## Overriding the default Dashboard page
To replace Filament's built-in dashboard widgets, create `App\Filament\Pages\Dashboard extends Filament\Pages\Dashboard` (not the generic `Page` class — `make:filament-page --type=custom` scaffolds the wrong base class) and override `getWidgets(): array`. This fully replaces the widget list for that page (no merging with panel-level widgets).

**Why:** The base `Dashboard::getWidgets()` returns `Filament::getWidgets()` (panel-registered widgets); overriding it in a subclass swaps in your own set entirely.

**How to apply:** Register the custom page via `discoverPages()`/explicit `->pages([App\Filament\Pages\Dashboard::class])` in the Panel provider, and leave `->widgets([])` empty if all widgets are meant to live only on this custom Dashboard.
