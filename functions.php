<?php
/**
 * Secluded functions and definitions
 *
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run slate_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'slate_setup' );

if ( ! function_exists( 'slate_setup' ) ):

function slate_setup() {

	add_editor_style();

	add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );

	add_theme_support( 'automatic-feed-links' );

	load_theme_textdomain( 'slate', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'slate' ),
	) );

	add_theme_support( 'custom-background' );

}
endif;

add_action( 'wp_enqueue_scripts', 'font_stylesheets' );

function font_stylesheets() {
        wp_register_style( 'oswald-font', 'http://fonts.googleapis.com/css?family=Oswald' );
	wp_enqueue_style( 'oswald-font' );
	wp_register_style( 'grace-font', 'http://fonts.googleapis.com/css?family=Covered+By+Your+Grace' );
	wp_enqueue_style( 'grace-font' );
}

function theme_scripts() {
	wp_enqueue_script( 'jquery' );
	
	wp_enqueue_script( 'menu', get_template_directory_uri() .'/js/menu.js');
	
	echo <<<END
		<script type="text/javascript">
			<!--
			onload=function() {

				var divh = document.getElementById('header').offsetHeight;
				
				divh += 58;
				
				document.getElementById('wrapper').style.cssText= 'padding-top:'+divh+'px';

			}
			//-->
		</script>
END;
	
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
	
	if ( is_404() ) {
	echo <<<END
			<script type="text/javascript">
				// focus on search field after it has loaded
				function searchFocus() {
					document.getElementById('s') && document.getElementById('s').focus();
				}
				window.onload = searchFocus;
			</script>
END;
	}
}

add_action('wp_enqueue_scripts', 'theme_scripts');

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 */
function slate_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'slate_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 */
function slate_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'slate' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and slate_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 */
function slate_auto_excerpt_more( $more ) {
	return ' &hellip;' . slate_continue_reading_link();
}
add_filter( 'excerpt_more', 'slate_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 */
function slate_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= slate_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'slate_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in style.css. This just
 * tells WordPress to not use the default styles.
 *
 */
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Deprecated way to remove inline styles printed when the gallery shortcode is used.
 *
 * This function is no longer needed or used. Use the use_default_gallery_style
 * filter instead, as seen above.
 */
function slate_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
// Backwards compatibility with WordPress 3.0.
if ( version_compare( $GLOBALS['wp_version'], '3.1', '<' ) )
	add_filter( 'gallery_style', 'slate_remove_gallery_css' );

if ( ! function_exists( 'slate_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own slate_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function slate_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'slate' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'slate' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'slate' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'slate' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'slate' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'slate' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override slate_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 */
function slate_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'slate' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'slate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'slate' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'slate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

}
/** Register sidebars by running slate_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'slate_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * This function uses a filter (show_recent_comments_widget_style) new in WordPress 3.1
 * to remove the default style.
 *
 */
function slate_remove_recent_comments_style() {
	add_filter( 'show_recent_comments_widget_style', '__return_false' );
}
add_action( 'widgets_init', 'slate_remove_recent_comments_style' );

if ( ! function_exists( 'slate_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 */
function slate_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s', 'slate' ),
		'post-meta',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="eu-entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		)
	);
}
endif;

if ( ! function_exists( 'slate_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 */
function slate_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'slate' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'slate' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'slate' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;
