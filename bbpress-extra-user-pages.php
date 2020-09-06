<?php

/**
 * bbPress Extra User Pages
 *
 * @wordpress-plugin
 * Plugin Name: bbPress Extra User Pages
 * Plugin URI:  https://acme.com/demo-plugin
 * Description: Add custom bbPress user subpages.
 * Version:     1.0.0
 * Author:      Viktor Szépe
 * Author URI:  https://github.com/szepeviktor
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

use SzepeViktor\Bbpress\ExtraUserPages;

if (!defined('ABSPATH')) {
    die;
}

function beup_init()
{
        if (\is_admin()
            || !\is_user_logged_in()
            || !\function_exists('is_bbpress')
        ) {
            return;
        }

        require_once __DIR__ . '/src/ExtraUserPages.php';
        require_once __DIR__ . '/src/helpers.php';

        new ExtraUserPages();
}

\add_action('init', __NAMESPACE__ . '\\beup_init');
