<?php sk_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<div class="container-fluid">

		<div class="single-post__row">

			<aside class="sk-sidebar single-post__sidebar">

				<a href="#post-content" class="focus-only"><?php _e('Hoppa över sidomeny', 'sk_tivoli'); ?></a>

				<?php do_action('sk_page_helpmenu'); ?>

			</aside>

			<div class="single-post__content" id="post-content">

				<?php do_action('sk_before_page_title'); ?>

				<h1 class="single-post__title"><?php _e('Detaljerad fångststatistik', 'sk_tivoli');?></h1>

				<table class="table table-sm table-striped">
					<tbody>
					<tr>
						<th scope="row"><?php _e('Fiskare', 'sk_tivoli');?></th>
						<td><?php echo !empty( get_field('cr-name', get_the_ID() ) ) ? get_field('cr-name', get_the_ID() ) : __('Uppgift saknas', 'sk_tivoli') ;?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Datum', 'sk_tivoli');?></th>
						<td><?php echo get_field('cr-dateofcatch', get_the_ID() );?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Art', 'sk_tivoli');?></th>
						<td><?php echo Sk_Fishingsundsvall_Public::get_term_name( get_field( 'cr-species', get_the_ID() ) ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Vikt', 'sk_tivoli');?></th>
						<td><?php echo get_field('cr-weight', get_the_ID() );?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Längd', 'sk_tivoli');?></th>
						<td><?php echo get_field('cr-length', get_the_ID() );?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Återutsatt', 'sk_tivoli');?></th>
						<td><?php echo get_field('cr-released', get_the_ID() );?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Plats', 'sk_tivoli');?></th>
						<td><?php echo Sk_Fishingsundsvall_Public::get_term_name( get_field( 'cr-place', get_the_ID() ) ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Fenklippt', 'sk_tivoli');?></th>
						<td><?php echo get_field('cr-cutted', get_the_ID() );?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Redskap', 'sk_tivoli');?></th>
						<td><?php echo get_field('cr-catchmethod', get_the_ID() );?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Kommentar', 'sk_tivoli');?></th>
						<td><?php echo get_field('cr-comment', get_the_ID() );?></td>
					</tr>
					</tbody>
				</table>

				<?php do_action('sk_after_page_title'); ?>

				<?php do_action('sk_before_page_content'); ?>

				<?php the_content(); ?>

				<div class="clearfix"></div>

				<?php do_action('sk_after_page_content'); ?>

			</div>

		</div> <?php //.row ?>

	</div> <?php //.container-fluid ?>

<?php endwhile; ?>

<?php get_footer(); ?>
