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
    class All_in_One_SEO_Pack_BuddyPress extends All_in_One_SEO_Pack_Compatible {
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
            add_filter( 'aioseop_description', array( &$this, 'filter_description' ), 1, 2 );
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
                // Get activity
                $activity = new BP_Activity_Activity( $bp->current_action );
                // Reset description
                $description = $activity->action . ( $activity->content ? ': ' . $activity->content : '' );
            }
            return $description;
        }
    }
}
