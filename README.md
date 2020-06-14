# bbPress-extra-user-pages
A little class for extend bbPress user subpages with template files

# Usage
1. Add bbpress folder to your theme root.
1. Add this file for your template `includes` folder.
1. In your `functions.php` add a require line: ```require_once '/{your-includes-folder}/class-bbpextrauserpages.php';``` or use `PSR-4 autoload`
1. Add an array of page 'slugs' and 'titles':  
```php
    $pages =
    [
        [ 'slug' => {$page-slug}, 'title' => {$page-title} ]
    ];
```
1. Add class construct to your code:
```php 
    new BbpExtraUserPages( $pages );
```
1. Add template files to `/bbpress/user/` folder in the following type: ```user-{$page-slug}.php```

### You can also specify a list of subpages to create with a filter:
```php 
    add_filter( 'beup_extra_account_pages', function() { return [[ 'slug' => {$page-slug}, 'title' => {$page-title} ]] } );
```
