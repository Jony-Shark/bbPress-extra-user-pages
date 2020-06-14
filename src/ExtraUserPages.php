<?php

/**
 * Add custom user subpages to bbPress user pages.
 */

namespace SzepeViktor\Bbpress;

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
            ?>
            <li class="<?php if ( $this->currentUserPage === $page ) { echo 'current'; } ?>">
                <span class="bbp-user-<?php echo esc_attr( $page['slug'] ); ?>-link">
                    <a href="<?php \bbp_user_profile_url(); echo esc_attr( $page['slug'] ); ?>/"
                        title="<?php echo \esc_html( $page['title'] ); ?>">
                        <?php echo \esc_html( $page['title'] ); ?>
                    </a>
                </span>
            </li>
            <?php
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
