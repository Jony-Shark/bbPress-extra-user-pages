<?php
/**
 * Add extra user subpages to Bbp user pages.
 */

class BbpExtraUserPages
{

    /**
     * Array of extra user pages 'slug' and 'title'.
     *
     * @var array<array>
     */
    private $userPages;

    /**
     * Array of current subpage 'slug' and 'title'.
     *
     * @var array<string>
     */
    private $currentUserPage;

    /**
     * Get pages array
     *
     * @param array $pages
     */
    public function __construct( $pages = [] )
    {
        if ( \is_admin() || ! \is_user_logged_in() || ! $this->bbpActive() ) {
            return;
        }
        $this->userPages     = \apply_filters( 'beup_extra_account_pages', $pages );
        $this->hooks();
    }

    /**
     * Add Hooks.
     */
    private function hooks()
    {
        \add_action( 'init', [ $this, 'setRewrites' ], 0, 0 );
        \add_action( 'beup_add_extra_user_pages', [ $this, 'getSubpageTemplate' ], 10, 0  );
        \add_action( 'beup_extra_user_page_menuitems', [ $this, 'addMenuItems' ], 10, 0  );
        \add_action( 'bbp_template_before_user_details_menu_items', [ $this, 'getCurrentSubpage' ], 10, 0  );
    }

    /**
     * Check bbp plugin activation.
     */
    private function bbpActive()
    {
        return in_array( 'bbPress/bbpress.php', (array) \get_option( 'active_plugins', array() ), true );
    }

    /**
     * Set rewrite rules and tags.
     */
    public function setRewrites()
    {
        $userPages = get_transient( 'beup_extra_user_pages' );
        if ( false !== $userPages && $userPages === $this->userPages )
        {
            return;
        }

        \set_transient( 'beup_extra_user_pages', $this->userPages );
        \flush_rewrite_rules( false );
        \array_map(
            function( $page )
                {
                    if ( ! isset( $page['slug'] ) )
                    {
                        return;
                    }
                    \add_rewrite_tag( '%' . $page['slug'] . '%', '([1]{1,})' );
                    \add_rewrite_rule( sprintf( '%s/([^/]+)/%s/?$', \bbp_get_user_slug(), $page['slug'] ), sprintf( 'index.php?%s=$matches[1]', \bbp_get_user_rewrite_id() ), 'top' );
                },
            $this->userPages
        );
    }

    /**
     * Get subpage template.
     */
    public function getSubpageTemplate()
    {
        \get_template_part( 'bbpress/user/user', $this->currentUserPage['slug'] );
    }

    /**
     * Add menu items to Bbp user page
     */
    public function addMenuItems()
    {
        \array_map(
            function( $page )
            {
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
            },
            $this->userPages
        );
    }

    /**
     * Get current subpage slug.
     */
    public function getCurrentSubpage()
    {
        global $wp;

        \array_map(
            function( $page ) use ( $wp )
            {
                if ( ! mb_strpos( $wp->request, $page['slug'] ) )
                {
                    return;
                }
                $this->currentUserPage = $page;
                \add_filter( 'bbp_is_single_user_profile', function() { return false; } );
            },
            $this->userPages
        );
    }

}
