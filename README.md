# bbPress Extra User Pages

Add custom bbPress user subpages.

### Installation

1. Download the [plugin from GitHub](https://github.com/Jony-Shark/bbPress-extra-user-pages/archive/master.zip)
1. WordPress admin / Plugin / Add new / Upload plugin
1. Copy `/bbpress` folder from the plugin to your current theme
1. Add `add_filter` call below to your current theme's `functions.php`
1. Activate the plugin: WordPress admin / Plugin

```php
add_filter(
    'beup_extra_account_pages',
    function ($userPages) {
        return [
            // List of Slug and Title pairs.
            ['slug' => 'example', 'title' => 'Example Subpage'],
            // ['slug' => 'pelda', 'title' => 'Példa Aloldal'],
        ];
    }
);
```

### Adding custom subpages

1. Create a new template file with the desired content `/bbpress/extra-user-NEWSLUG.php`
1. Add a new array item to the `add_filter` call `['slug' => 'NEWSLUG', 'title' => 'New Subpage Title'],`

### Telepítés (HU)

1. Töltsd le a [bővítményt GitHub-ról](https://github.com/Jony-Shark/bbPress-extra-user-pages/archive/master.zip)
1. WordPress admin / Bővítmények / Új hozzáadása / Bővítmény feltöltése
1. Másold a bővítmény `/bbpress` mappáját az aktuális sablonodba
1. Add hozzá a fenti `add_filter` hívást az aktuális sablonod `functions.php` fájljához
1. Kapcsold be a bővítményt: WordPress admin / Bővítmények

### Új egyedi aloldal hozzáadása (HU)

1. Hozz létre egy új sablon fájlt a kívánt tartalommal `/bbpress/extra-user-UJALOLDAL.php`
1. Adj hozzá egy elemt a tömbhöz az `add_filter` hívásban `['slug' => 'UJALOLDAL', 'title' => 'Új Aloldal Címe'],`
