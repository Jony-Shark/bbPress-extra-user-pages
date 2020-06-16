<?php

/**
 * Add custom user subpages to bbPress user pages.
 */

namespace SzepeViktor\Bbpress;

use function SzepeViktor\Bbpress\tag;

class ExtraUserPages
{
    /**
     * @var array<array{slug: string, title: string}>
     */
    private $userPages;

    /**
     * @var array{slug: string, title: string}
     */
    private $currentUserPage;

    /**
     * @param array<array{slug: string, title: string}> $userPages
     */
    public function __construct($userPages = [])
    {
        $this->userPages = \apply_filters('beup/extra-user-pages', $userPages);

        \add_action('init', [$this, 'addRewriteRules'], 0, 0);
        \add_action('bbp_template_before_user_details_menu_items', [$this, 'setCurrentSubpageSlug'], 10, 0);

        \add_action('beup/extra-user-details-menu-items', [$this, 'printMenuItems'], 10, 0);
        \add_action('beup/extra-user-pages', [$this, 'printSubpage'], 10, 0);
    }

    /**
     * @return void
     */
    public function setCurrentSubpageSlug()
    {
        global $wp;

        foreach ($this->userPages as $page) {
            // FIXME More strict condition
            if (!mb_strpos($wp->request, $page['slug'])) {
                continue;
            }
            $this->currentUserPage = $page;
            \add_filter('bbp_is_single_user_profile', '__return_false');
        }
    }

    /**
     * @return void
     */
    public function addRewriteRules()
    {
        $userPages = \get_transient('beup_extra_user_pages');
        if ($userPages === $this->userPages) {
            return;
        }

        \set_transient('beup_extra_user_pages', $this->userPages);
        // TODO We hope Rewrite Rules will stay in place.
        \flush_rewrite_rules(false);

        foreach ($this->userPages as $page) {
            if (!isset($page['slug'])) {
                continue;
            }

            \add_rewrite_tag(sprintf('%%%s%%', $page['slug']), '([1]{1,})');
            \add_rewrite_rule(
                sprintf(
                    '%s/([^/]+)/%s/?$',
                    \bbp_get_user_slug(),
                    $page['slug']
                ),
                sprintf(
                    'index.php?%s=$matches[1]',
                    \bbp_get_user_rewrite_id()
                ),
                'top'
            );
        }
    }

    /**
     * @return void
     */
    public function printMenuItems()
    {
        foreach ($this->userPages as $page) {
            $link = tag(
                'a',
                [
                    'href' => \bbp_get_user_profile_url() . $page['slug'],
                    'title' => $page['title'],
                ],
                \esc_html($page['title'])
            );
            print tag(
                'li',
                ['class' => ($this->currentUserPage === $page) ? 'current' : ''],
                tag(
                    'span',
                    ['class' => sprintf('bbp-user-%s-link', $page['slug'])],
                    $link
                )
            );
        }
    }

    /**
     * @return void
     */
    public function printSubpage()
    {
        \get_template_part('bbpress/extra-user', $this->currentUserPage['slug']);
    }
}
