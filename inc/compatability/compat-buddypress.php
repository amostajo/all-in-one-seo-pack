<?php
if ( ! class_exists( 'All_in_One_SEO_Pack_BuddyPress' ) ) {
    /**
     * Compatibility with BuddyPress
     * - Implements same methods found in All_in_One_SEO_Pack_Compatible.
     *
     * @link https://codex.buddypress.org/
     * @package All-in-One-SEO-Pack
     * @author Alejandro Mostajo
     * @copyright Semperfi Web Design <https://semperplugins.com/>
     * @version 2.3.14
     */
    class All_in_One_SEO_Pack_BuddyPress extends All_in_One_SEO_Pack_Module {

        /**
         * Prefix.
         *
         * @since 2.3.14
         *
         * @var string
         */
        protected $prefix = 'aiosp_buddypress_';

        /**
         * Returns flag indicating if BuddyPress is present.
         *
         * @since 2.3.14
         *
         * @return bool
         */
        public function exists() {
            return function_exists( 'bp_is_active' );
        }

        /**
         * Declares compatibility hooks.
         *
         * @since 2.3.14
         */
        public function hooks() {
            add_filter( 'aioseop_title', array( &$this, 'filter_title' ), 10 );
            add_filter( 'aioseop_description', array( &$this, 'filter_description' ), 1, 2 );
            add_action( 'admin_init', array( &$this, 'admin_init' ) );
        }

        /**
         * Filters meta titles.
         * action:aioseop_title
         *
         * @since 2.3.14
         *
         * @global object $bp BuddyPress global object.
         *
         * @param string $title Current title.
         *
         * @return string
         */
        public function filter_title( $title ) {
            global $bp;
            /*
             * Check if we are displaying an individual activity.
             * - Activity display
             * - User activity
             */
            if ( is_page()
                && $bp->current_component === 'activity'
                && isset( $bp->displayed_user->id )
            ) {
                $activity = new BP_Activity_Activity( $bp->current_action );
                $action = strip_tags( preg_replace( '#<a.*?>([^>]*)</a>#i', '$1', $activity->action ) );
                $replacement = preg_match( '/group\\s[a-zA-Z]+/', $action, $matches )
                    ? sprintf(
                        __( 'Activity by %s in the %s', 'all-in-one-seo-pack' ),
                        $bp->displayed_user->userdata->display_name,
                        $matches[0]
                    )
                    : sprintf(
                        __( 'Activity by %s', 'all-in-one-seo-pack' ),
                        $bp->displayed_user->userdata->display_name
                    );
                $title = preg_replace( '/[Aa]ctivity/', $replacement, $title );
                // Replace [Part] postfix
                $title = preg_replace( '/\s\-\s[Pp]art\s[0-9]+/', '', $title );
            }
            return $title;
        }

        /**
         * Filters meta descriptions.
         * action:aioseop_description
         *
         * @see https://stackoverflow.com/questions/8914476/facebook-open-graph-meta-tags-maximum-content-length
         *
         * @since 2.3.14
         *
         * @global object $bp              BuddyPress global object.
         * @global array  $aioseop_options AIOSEOP saved settings.
         *
         * @param string $description Current description.
         * @param string $prefix      AIOSEOP module prefix.
         *
         * @return string
         */
        public function filter_description( $description, $prefix = null ) {
            global $bp, $aioseop_options;
            /*
             * Check if we are displaying an individual activity.
             * - Activity display
             * - User activity
             * - Either is for social descriptions or generate setting is on.
             */
            if ( is_page()
                && $bp->current_component === 'activity'
                && isset( $bp->displayed_user->id )
                && ($prefix === 'aiosp_opengraph_'
                    || $aioseop_options['aiosp_generate_descriptions'] === 'on'
                )
            ) {
                $activity = new BP_Activity_Activity( $bp->current_action );
                $description = $activity->action . ( $activity->content ? ': ' . $activity->content : '' );
            }
            return $description;
        }
        /**
         * Admin init.
         * Adds SEO metaboxes.
         *
         * @since 2.3.14
         */
        public function admin_init()
        {
            /**
             * @see [plugins]/buddypress/bp-activity/bp-activity-admin.php > bp_activity_admin_index()
             */
            add_meta_box(
                'all-in-one-seo-pack',
                __( 'All in One SEO Pack', 'all-in-one-seo-pack' ),
                array( &$this, 'display_seo_metabox' ),
                'toplevel_page_bp-activity', // Activity edit
                'normal',
                'low'
            );
        }
        /**
         * Displays SEO metabox.
         *
         * @since 2.3.14
         */
        public function display_seo_metabox()
        {
            ob_start();
            ?>
            <div class="aioseop_tabs">
                <!--TODO tabs-->
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}
