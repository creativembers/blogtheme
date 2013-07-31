<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package creativembers
 */
?>
	<div class></div>
		<aside id="search" class="widget widget_search_custom">
			<?php get_search_form(); ?>
		</aside>

	<div id="secondary" class="widget-area" role="complementary">
		<?php do_action( 'before_sidebar' ); ?>
		<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>



			<aside id="archives" class="widget">
				<h1 class="widget-title"><?php _e( 'Archives', 'creativembers' ); ?></h1>
				<ul>
					<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
				</ul>
			</aside>

			<aside id="meta" class="widget">
				<h1 class="widget-title"><?php _e( 'Meta', 'creativembers' ); ?></h1>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</aside>

		<?php endif; // end sidebar widget area ?>
	</div><!-- #secondary -->
