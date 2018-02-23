<?php
/**
 * The loop that displays a page.
 *
 */
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( is_front_page() ) { ?>
						<h2 class="entry-title"><?php the_title(); ?></h2>
					<?php } else { ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php } ?>

					<div class="entry-content">
						<?php the_content(); ?>
						
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'slate' ), 'after' => '</div>' ) ); ?>
						
						<?php edit_post_link( __( 'Edit', 'slate' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-content -->
				</div><!-- #post-## -->

				<?php
				if (get_comments_number()==0) 
				{
    					// post has no comments
				} 
				else 
				{
					comments_template( '', true );
				} ?>

<?php endwhile; ?>
