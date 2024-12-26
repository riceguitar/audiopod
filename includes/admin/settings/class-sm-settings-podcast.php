<?php
/**
 * Podcast settings page.
 *
 * @package SM/Core/Admin/Settings
 */

defined( 'ABSPATH' ) or die;

/**
 * Initialize settings
 */
class SM_Settings_Podcast extends SM_Settings_Page {
	/**
	 * SM_Settings_Podcast constructor.
	 */
	public function __construct() {
		$this->id    = 'podcast';
		$this->label = __( 'Podcast', 'audiopod-wp' );
		add_action( 'sm_settings_podcast_settings_after', array( $this, 'after' ) );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'sm_podcast_settings', array(
			array(
				'title' => __( 'Podcast Settings', 'audiopod-wp' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'podcast_settings',
			),
			array(
				'title'       => __( 'Title', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'title',
				'placeholder' => get_bloginfo( 'name' ),
			),
			array(
				'title'       => __( 'Description', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'description',
				'placeholder' => get_bloginfo( 'description' ),
			),
			array(
				'title'       => __( 'Website Link', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'website_link',
				'placeholder' => home_url(),
			),
			array(
				'title'       => __( 'Language', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'language',
				'placeholder' => get_bloginfo( 'language' ),
			),
			array(
				'title'       => __( 'Copyright', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'copyright',
				// translators: %s: blog name.
				'placeholder' => wp_sprintf( __( 'Copyright &copy; %s', 'audiopod-wp' ), get_bloginfo( 'name' ) ),
				// translators: %s: copyright symbol HTML entitiy (&copy;).
				'desc'        => wp_sprintf( esc_html__( 'Tip: Use %s to generate a copyright symbol.', 'audiopod-wp' ), '<code>' . htmlspecialchars( '&copy;' ) . '</code>' ),
			),
			array(
				'title'       => __( 'Webmaster Name', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'webmaster_name',
				'placeholder' => __( 'e.g. Your Name', 'audiopod-wp' ),
			),
			array(
				'title'       => __( 'Webmaster Email', 'audiopod-wp' ),
				'type'        => 'email',
				'id'          => 'webmaster_email',
				'placeholder' => __( 'e.g. Your Email', 'audiopod-wp' ),
			),
			array(
				'title'       => __( 'Author', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'itunes_author',
				'placeholder' => __( 'e.g. Primary Speaker or Church Name', 'audiopod-wp' ),
				'desc'        => __( 'This will display at the &ldquo;Artist&rdquo; in the iTunes Store.', 'audiopod-wp' ),
			),
			array(
				'title'       => __( 'Subtitle', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'itunes_subtitle',
				// translators: %s: blog name.
				'placeholder' => wp_sprintf( __( 'e.g. Preaching and teaching audio from %s', 'audiopod-wp' ), get_bloginfo( 'name' ) ),
				'desc'        => __( 'Your subtitle should briefly tell the listener what they can expect to hear.', 'audiopod-wp' ),
			),
			array(
				'title'       => __( 'Summary', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'itunes_summary',
				// translators: %s: blog name.
				'placeholder' => wp_sprintf( __( 'e.g. Weekly teaching audio brought to you by %s in City, State.', 'audiopod-wp' ), get_bloginfo( 'name' ) ),
				'desc'        => __( 'Keep your Podcast Summary short, sweet and informative. Be sure to include a brief statement about your mission and in what region your audio content originates.', 'audiopod-wp' ),
			),
			array(
				'title'       => __( 'Owner Name', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'itunes_owner_name',
				'placeholder' => get_bloginfo( 'name' ),
				'desc'        => __( 'This should typically be the name of your Church.', 'audiopod-wp' ),
			),
			array(
				'title'       => __( 'Owner Email', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'itunes_owner_email',
				'placeholder' => __( 'e.g. Your Email', 'audiopod-wp' ),
				'desc'        => __( 'Use an email address that you don&rsquo;t mind being made public. If someone wants to contact you regarding your Podcast this is the address they will use.', 'audiopod-wp' ),
			),
			array(
				'title' => __( 'Cover Image', 'audiopod-wp' ),
				'type'  => 'image',
				'id'    => 'itunes_cover_image',
				'desc'  => __( 'This JPG will serve as the Podcast artwork in the iTunes Store. The image must be between 1,400px by 1,400px and 3,000px by 3,000px or else iTunes will not accept your feed.', 'audiopod-wp' ),
			),
			array(
				'title'   => __( 'Sub Category', 'audiopod-wp' ),
				'type'    => 'select',
				'id'      => 'itunes_sub_category',
				'options' => array(
					'0' => __( 'Sub Category', 'audiopod-wp' ),
					'1' => __( 'Buddhism', 'audiopod-wp' ),
					'2' => __( 'Christianity', 'audiopod-wp' ),
					'3' => __( 'Hinduism', 'audiopod-wp' ),
					'4' => __( 'Islam', 'audiopod-wp' ),
					'5' => __( 'Judaism', 'audiopod-wp' ),
					'6' => __( 'Other', 'audiopod-wp' ),
					'7' => __( 'Spirituality', 'audiopod-wp' ),
				),
			),
			array(
				'title'    => __( 'PodTrac Tracking', 'audiopod-wp' ),
				'type'     => 'checkbox',
				'id'       => 'podtrac',
				'desc'     => __( 'Enables PodTrac tracking.', 'audiopod-wp' ),
				// translators: %s <a href="http://podtrac.com">podtrac.com</a>.
				'desc_tip' => wp_sprintf( __( 'For more info on PodTrac or to sign up for an account, visit %s', 'audiopod-wp' ), '<a href="http://podtrac.com">podtrac.com</a>' ),
				'default'  => 'no',
			),
			array(
				'title'    => __( 'HTML in description', 'audiopod-wp' ),
				'type'     => 'checkbox',
				'id'       => 'enable_podcast_html_description',
				'desc'     => __( 'Enables showing of HTML in iTunes description field. Uncheck if description looks messy.', 'audiopod-wp' ),
				'desc_tip' => __( 'It is recommended to leave it unchecked. Uncheck if the feed does not validate.', 'audiopod-wp' ),
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Redirect', 'audiopod-wp' ),
				'type'     => 'checkbox',
				'id'       => 'enable_podcast_redirection',
				'desc'     => __( 'Enables redirection of podcast from old to new URL.', 'audiopod-wp' ),
				'desc_tip' => __( 'You can use relative or absolute URLs.', 'audiopod-wp' ),
			),
			array(
				'title' => __( 'Old URL', 'audiopod-wp' ),
				'type'  => 'text',
				'id'    => 'podcast_redirection_old_url',
			),
			array(
				'title' => __( 'New URL', 'audiopod-wp' ),
				'type'  => 'text',
				'id'    => 'podcast_redirection_new_url',
			),
			array(
				'title'       => __( 'Number of podcasts to show', 'audiopod-wp' ),
				'type'        => 'number',
				'id'          => 'podcasts_per_page',
				'placeholder' => get_option( 'posts_per_rss' ),
			),
			array(
				'title'       => __( 'iTunes Podcast URL', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'podcast_url_itunes',
				'placeholder' => 'pcast://itunes.apple.com/us/podcast/…/id…',
				'desc'        => 'URL to use for the iTunes link in the <code>[list_podcasts]</code> shortcode. Change “https” to “pcast” to make the link open directly into the Apple Podcasts app. Shortcode key to include/exclude: <code>itunes</code>.',
				'desc_tip'    => 'Leave empty to disable.',
			),
			array(
				'title'       => __( 'Android Podcast URL', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'podcast_url_android',
				'placeholder' => 'https://subscribeonandroid.com/' . str_replace( 'https://', '', get_site_url( null, '?feed=rss2&post_type=wpfc_sermon', 'https' ) ),
				'desc'        => 'URL to use for the Android link in the <code>[list_podcasts]</code> shortcode. Shortcode key to include/exclude: <code>android</code>.',
				'desc_tip'    => 'Leave empty to disable.',
			),
			array(
				'title'       => __( 'Overcast Podcast URL', 'audiopod-wp' ),
				'type'        => 'text',
				'id'          => 'podcast_url_overcast',
				'placeholder' => 'https://overcast.fm/…',
				'desc'        => 'URL to use for the Overcast link in the <code>[list_podcasts]</code> shortcode.  Shortcode key to include/exclude: <code>overcast</code>.',
				'desc_tip'    => 'Leave empty to disable.',
			),
			array(
				'title'    => __( 'Sermon Image', 'audiopod-wp' ),
				'type'     => 'checkbox',
				'id'       => 'podcast_sermon_image_series',
				'desc'     => __( 'Fallback to series image if sermon does not have its own image.', 'audiopod-wp' ),
				'desc_tip' => __( 'Default disabled.', 'audiopod-wp' ),
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Use "https://" in Enclosure URL', 'audiopod-wp' ),
				'type'     => 'checkbox',
				'id'       => 'podcast_enclosure_url',
				'desc'     => __( 'Podcast RSS feed Enclosure URL. Uncheck if description looks messy.' ),
				'desc_tip' => __( 'It is advisable to keep the checkbox unchecked, as selecting it will automatically replace all instances of "http://" with "https://" in the Enclosure URL of the podcast\'s RSS feed.', 'audiopod-wp' ),
				'default'  => 'no',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'podcast_settings',
			),
		) );

		return apply_filters( 'sm_get_settings_' . $this->id, $settings );
	}

	/**
	 * Additional HTML after the settings form.
	 */
	public function after() {
		?>
		<div>
			<p>
				<label for="feed_url"><?php echo __( 'Feed URL to Submit to iTunes', 'audiopod-wp' ); ?></label>
				<input type="text" disabled="disabled"
						value="<?php echo site_url( '/' ) . '?feed=rss2&post_type=wpfc_sermon'; ?>" id="feed_url">
			</p>
			<p>
				<?php
				// translators: %s Feed Validator link, see msgid "Feed Validator".
				echo wp_sprintf( esc_html__( 'Use the %s to diagnose and fix any problems before submitting your Podcast to iTunes.', 'audiopod-wp' ), '<a href="http://www.feedvalidator.org/check.cgi?url=' . site_url( '/' ) . SermonManager::getOption( 'archive_slug', 'sermons' ) . '/feed/" target="_blank">' . esc_html__( 'Feed Validator', 'audiopod-wp' ) . '</a>' );
				?>
			</p>
			<p>
				<?php
				// translators: %s see msgid "Submit Your Podcast".
				echo wp_sprintf( esc_html__( 'Once your Podcast Settings are complete and your Sermons are ready, it&rsquo;s time to %s to the iTunes Store!', 'audiopod-wp' ), '<a href="https://www.apple.com/itunes/podcasts/specs.html#submitting" target="_blank">' . esc_html__( 'Submit Your Podcast', 'audiopod-wp' ) . '</a>' );
				?>
			</p>
			<p>
				<?php
				// translators: %s see msgid "FeedBurner".
				echo wp_sprintf( esc_html__( 'Alternatively, if you want to track your Podcast subscribers, simply pass the Podcast Feed URL above through %s. FeedBurner will then give you a new URL to submit to iTunes instead.', 'audiopod-wp' ), '<a href="http://feedburner.google.com/" target="_blank">' . esc_html__( 'FeedBurner', 'audiopod-wp' ) . '</a>' );
				?>
			</p>
			<p>
				<?php
				// translators: %s see msgid "iTunes FAQ for Podcast Makers".
				echo wp_sprintf( esc_html__( 'Please read the %s for more information.', 'audiopod-wp' ), '<a href="https://www.apple.com/itunes/podcasts/creatorfaq.html" target="_blank">' . esc_html__( 'iTunes FAQ for Podcast Makers', 'audiopod-wp' ) . '</a>' );
				?>
			</p>
		</div>
		<?php
	}
}

return new SM_Settings_Podcast();
