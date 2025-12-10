# Toast Component

Plně funkční toast notifikační systém kompatibilní s Flux UI stylem, ale bez závislosti na Flux.

## Instalace

### 1. Zkopíruj soubory

```
app/
├── Support/
│   └── Toast.php
└── View/
    └── Components/
        └── Toast/
            ├── Toast.php
            └── Group.php

resources/views/components/toast/
├── index.blade.php
├── toast.blade.php
└── group.blade.php
```

### 2. Registruj komponenty (volitelné)

Pokud chceš používat `<x-toast>` místo `<x-toast.index>`, přidej do `AppServiceProvider`:

```php
use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    Blade::component('toast', \App\View\Components\Toast\Toast::class);
    Blade::component('toast.group', \App\View\Components\Toast\Group::class);
}
```

Nebo použij anonymní komponenty (soubory v `resources/views/components/toast/`).

## Použití

### Základní použití

V layoutu přidej toast komponentu:

```blade
<body>
    <!-- ... -->
    <x-toast />
</body>
```

### S wire:navigate (persist)

```blade
<body>
    <!-- ... -->
    @persist('toast')
        <x-toast />
    @endpersist
</body>
```

### Toast Group (stacking)

```blade
<x-toast.group>
    <x-toast />
</x-toast.group>

<!-- Nebo jednodušeji -->
<x-toast.group />
```

S expanded stavem (vždy zobrazí všechny toasty):

```blade
<x-toast.group expanded />
```

## Triggery

### Z Livewire komponenty

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Support\Toast;

class EditPost extends Component
{
    public function save()
    {
        // Jednoduchý toast
        Toast::toast('Změny byly uloženy.');

        // S headingem
        Toast::toast(
            text: 'Můžete to kdykoliv změnit v nastavení.',
            heading: 'Změny uloženy',
        );

        // Varianty
        Toast::success('Operace proběhla úspěšně.');
        Toast::warning('Pozor na něco.');
        Toast::danger('Něco se pokazilo.');

        // S vlastní dobou trvání (v ms)
        Toast::toast('Rychlý toast', duration: 2000);

        // Permanentní (duration: 0)
        Toast::toast('Zůstane dokud nezavřete', duration: 0);
    }
}
```

### Alternativa - přímo přes dispatch

```php
$this->dispatch('toast', [
    'heading' => 'Úspěch',
    'text' => 'Váš soubor byl nahrán.',
    'variant' => 'success',
    'duration' => 5000,
]);
```

### Z Alpine.js

```blade
<button x-on:click="$toast('Změny byly uloženy.')">
    Uložit
</button>

<!-- S konfigurací -->
<button x-on:click="$toast({
    heading: 'Změny uloženy',
    text: 'Můžete to kdykoliv změnit.',
    variant: 'success',
})">
    Uložit
</button>
```

### Z JavaScriptu

```javascript
// Jednoduchá zpráva
Toast.toast('Změny byly uloženy.');

// S konfigurací
Toast.toast({
    heading: 'Změny uloženy',
    text: 'Můžete to kdykoliv změnit v nastavení.',
    variant: 'success',
    duration: 3000,
});

// Pomocné metody
Toast.success('Úspěšně uloženo.');
Toast.warning('Zkontrolujte vstup.');
Toast.danger('Operace selhala.');
```

## Pozice

```blade
<!-- Výchozí: bottom end -->
<x-toast position="bottom end" />

<!-- Další možnosti -->
<x-toast position="top start" />
<x-toast position="top center" />
<x-toast position="top end" />
<x-toast position="bottom start" />
<x-toast position="bottom center" />

<!-- S vlastním paddingem (např. pro navbar) -->
<x-toast position="top end" class="pt-24" />
```

## Varianty

| Varianta | Popis |
|----------|-------|
| (none) | Neutrální info toast |
| `success` | Zelená - úspěch |
| `warning` | Oranžová - varování |
| `danger` | Červená - chyba |

## Duration

- Výchozí: `5000` ms (5 sekund)
- `0` = permanentní (nezavře se automaticky)

## API Reference

### Toast::toast()

| Parametr | Typ | Výchozí | Popis |
|----------|-----|---------|-------|
| text | string | required | Text zprávy |
| heading | string\|null | null | Nadpis toastu |
| variant | string\|null | null | success, warning, danger |
| duration | int | 5000 | Doba zobrazení v ms |

### Blade komponenty

#### `<x-toast>`

| Prop | Typ | Výchozí | Popis |
|------|-----|---------|-------|
| position | string | 'bottom end' | Pozice na obrazovce |

#### `<x-toast.group>`

| Prop | Typ | Výchozí | Popis |
|------|-----|---------|-------|
| position | string | 'bottom end' | Pozice na obrazovce |
| expanded | bool | false | Vždy zobrazí všechny toasty |

## Požadavky

- Laravel 10+
- Livewire 3+
- Alpine.js 3+
- Tailwind CSS 3+

## Dark Mode

Komponenta automaticky podporuje dark mode pomocí Tailwind `dark:` variant.
