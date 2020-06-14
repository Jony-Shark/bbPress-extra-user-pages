# bbPress-extra-user-pages
A little class for extend bbPress user subpages with template files

# Usage
 - Add bbpress folder to your theme root.
 - Add this file for your template `includes` folder.
 - In your `functions.php` add a require line: ```require_once '/{your-includes-folder}/class-bbpextrauserpages.php';``` or use `PSR-4 autoload`
 - Add an array of page 'slugs' and 'titles':
```php
$pages = [
    [ 'slug' => {$page-slug}, 'title' => {$page-title} ]
];
```
 - Add class construct to your code:
```php 
new BbpExtraUserPages( $pages );
```
 - Add template files to `/bbpress/user/` folder in the following type: ```user-{$page-slug}.php```

### You can also specify a list of subpages to create with a filter:
```php 
add_filter( 'beup_extra_account_pages', function() { return [[ 'slug' => {$page-slug}, 'title' => {$page-title} ]] } );
```
