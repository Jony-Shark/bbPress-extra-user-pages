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
     * Get pages array
     *
     * @param array $pages
     */
    public function __construct( $pages = [] )
    {
        if ( \is_admin() || ! \is_user_logged_in() || ! in_array( 'bbPress/bbpress.php', (array) \get_option( 'active_plugins', array() ), true ) ) {
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
        \add_action( 'init', [ $this, 'set_rewrites' ], 0, 0 );
        \add_action( 'beup_add_extra_user_pages', [ $this, 'select_page_template' ], 10, 0  );
        \add_action( 'beup_extra_user_page_menuitems', [ $this, 'add_menu_items' ], 10, 0  );
    }

    /**
     * Set rewrite rules and tags.
     */
    public function set_rewrites()
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
     * Select page template.
     */
    public function select_page_template()
    {
        \array_map(
            function( $page )
            {
                if ( $this->is_extra_user_page( $page['slug'] ) )
                {
                    $this->get_extra_user_page_template( $page['slug'] );
                }
            },
            $this->userPages
        );
    }

    /**
     * Add menu items to Bbp user page
     */
    public function add_menu_items()
    {
        \array_map(
            function( $page )
            {
            ?>
                <li class="<?php if ( $this->is_extra_user_page( $page['slug'] ) ) : ?>current<?php endif; ?>">
                    <span class="bbp-user-edit-link">
                        <a href="<?php \printf( '%s%s/', \bbp_user_profile_url(), $page['slug'] ); ?>" title="<?php echo \esc_html( $page['title'] ); ?>"><?php echo \esc_html( $page['title'] ); ?></a>
                    </span>
                </li>
            <?php
            },
            $this->userPages
        );
    }

    /**
     * Get template-part from theme's 'bbpress/user/' folder
     */
    private function get_extra_user_page_template( $page_slug )
    {
        \add_filter( 'bbp_is_single_user_profile', function() { return false; } );
        \get_template_part( 'bbpress/user/user', $page_slug );
    }

    /**
     * Check request for page slugs.
     */
    private function is_extra_user_page( $page_slug )
    {
        global $wp;
        return mb_strpos( $wp->request, $page_slug );
    }
}
