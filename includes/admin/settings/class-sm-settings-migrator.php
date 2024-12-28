<?php
/**
 * Sermon Manager Migration Tool
 *
 * @package SM/Core/Admin/Settings
 */

defined('ABSPATH') or die;

/**
 * Class SM_Settings_Migrator
 */
class SM_Settings_Migrator extends SM_Settings_Page {
    /**
     * Constructor.
     */
    public function __construct() {
        $this->id    = 'migrator';
        $this->label = __('Migrator', 'sermon-manager-for-wordpress');

        // Add action to hide save button only on this tab
        add_action('sm_settings_' . $this->id, array($this, 'ap_hide_save_button'), 5);

        parent::__construct();
    }

    /**
     * Hide save button only on migrator tab
     */
    public function ap_hide_save_button() {
        $GLOBALS['hide_save_button'] = true;
    }

    /**
     * Get settings array
     *
     * @return array
     */
    public function get_settings() {
        $settings = array(
            array(
                'title' => __('Sermon Manager Migration Tool', 'sermon-manager-for-wordpress'),
                'type'  => 'title',
                'desc'  => __('This tool helps you migrate sermon descriptions to post content. This process will destroy your sermon_description upon completion.<br/><br/><strong>Warning! Backup your database before performing this action!</strong>', 'sermon-manager-for-wordpress'),
                'id'    => 'migrator_options',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'migrator_options',
            ),
        );

        return apply_filters('sm_get_settings_' . $this->id, $settings);
    }

    /**
     * Output the settings page
     */
    public function output() {
        // Process form submissions
        $message = '';
        $message_type = 'info';
        
        if (isset($_POST['preview_migration'])) {
            if (!check_admin_referer('sm-settings')) {
                $message = __('Security check failed. Please try again.', 'sermon-manager-for-wordpress');
                $message_type = 'error';
            } else {
                $sermons = $this->get_sermons_to_migrate();
            }
        }
        
        if (isset($_POST['start_migration'])) {
            if (!check_admin_referer('sm-settings')) {
                $message = __('Security check failed. Please try again.', 'sermon-manager-for-wordpress');
                $message_type = 'error';
            } else {
                $count = $this->migrate_all_sermon_descriptions();
                $message = sprintf(__('Successfully migrated %d sermons.', 'sermon-manager-for-wordpress'), $count);
                $message_type = 'success';
            }
        }

        // Output any messages
        if ($message) {
            ?>
            <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
                <p><?php echo esc_html($message); ?></p>
            </div>
            <?php
        }

        // Output settings fields
        $settings = $this->get_settings();
        SM_Admin_Settings::output_fields($settings);
        
        // Add migration interface
        ?>
        <form method="post">
            <?php wp_nonce_field('sm-settings'); ?>
            
            <p class="submit">
                <input type="submit" name="preview_migration" class="button button-secondary" 
                    value="<?php esc_attr_e('Preview Migration', 'sermon-manager-for-wordpress'); ?>" />
                    
                <input type="submit" name="start_migration" class="button button-primary" 
                    value="<?php esc_attr_e('Start Migration', 'sermon-manager-for-wordpress'); ?>"
                    onclick="return confirm('<?php esc_attr_e('Are you sure you want to start the migration? This process cannot be undone.', 'sermon-manager-for-wordpress'); ?>');" />
            </p>

            <?php if (isset($sermons)): ?>
                <div class="migration-preview">
                    <h3><?php esc_html_e('Migration Preview', 'sermon-manager-for-wordpress'); ?></h3>
                    <?php $this->render_preview_table($sermons); ?>
                </div>
            <?php endif; ?>
        </form>
        <?php
    }

    /**
     * Get sermons that need migration
     */
    private function get_sermons_to_migrate() {
        $sermons = get_posts(array(
            'post_type' => 'wpfc_sermon',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));

        $to_migrate = array();
        foreach ($sermons as $sermon) {
            $sermon_description = get_post_meta($sermon->ID, 'sermon_description', true);
            
            if (!empty($sermon_description) && empty($sermon->post_content)) {
                $to_migrate[] = array(
                    'ID' => $sermon->ID,
                    'title' => $sermon->post_title,
                    'status' => $sermon->post_status,
                    'description_length' => strlen($sermon_description),
                    'edit_link' => get_edit_post_link($sermon->ID)
                );
            }
        }

        return $to_migrate;
    }

    /**
     * Render preview table
     */
    private function render_preview_table($sermons) {
        if (empty($sermons)) {
            echo '<p>' . esc_html__('No sermons found that need migration.', 'sermon-manager-for-wordpress') . '</p>';
            return;
        }
        ?>
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID', 'sermon-manager-for-wordpress'); ?></th>
                    <th><?php esc_html_e('Title', 'sermon-manager-for-wordpress'); ?></th>
                    <th><?php esc_html_e('Status', 'sermon-manager-for-wordpress'); ?></th>
                    <th><?php esc_html_e('Description Length', 'sermon-manager-for-wordpress'); ?></th>
                    <th><?php esc_html_e('Actions', 'sermon-manager-for-wordpress'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sermons as $sermon): ?>
                    <tr>
                        <td><?php echo esc_html($sermon['ID']); ?></td>
                        <td><?php echo esc_html($sermon['title']); ?></td>
                        <td><?php echo esc_html($sermon['status']); ?></td>
                        <td><?php echo esc_html($sermon['description_length']); ?> <?php esc_html_e('characters', 'sermon-manager-for-wordpress'); ?></td>
                        <td>
                            <a href="<?php echo esc_url($sermon['edit_link']); ?>" class="button button-small" target="_blank">
                                <?php esc_html_e('View/Edit', 'sermon-manager-for-wordpress'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Perform the migration
     */
    private function migrate_all_sermon_descriptions() {
        $sermons = get_posts(array(
            'post_type' => 'wpfc_sermon',
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));

        $count = 0;
        foreach ($sermons as $sermon) {
            $sermon_description = get_post_meta($sermon->ID, 'sermon_description', true);
            
            if (!empty($sermon_description) && empty($sermon->post_content)) {
                wp_update_post(array(
                    'ID' => $sermon->ID,
                    'post_content' => $sermon_description
                ));
                
                delete_post_meta($sermon->ID, 'sermon_description');
                $count++;
            }
        }

        return $count;
    }
}

return new SM_Settings_Migrator();