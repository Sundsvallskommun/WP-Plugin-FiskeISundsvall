<?php
get_header();
$reports = Sk_Fishingsundsvall_Public::get_reports();
?>
<div class="container-fluid archive">

	<h1 class="archive__title"><?php _e( 'Fångststatistik', 'sk_tivoli' ); ?></h1>

	<div class="card form-wrapper">
		<div class="card-block">
			<form method="post" action="<?php echo get_post_type_archive_link( 'catchreport' ); ?>">

				<div class="row">


					<div class="col-sm-6">

						<div class="form-group row">
							<label for="inputEmail3"
							       class="col-sm-4 col-form-label"><?php _e( 'Datum från: ', 'sk_tivoli' ); ?></label>
							<div class="col-sm-6">
								<input type="text" name="cr_date_from"
								       class="form-control datepicker"
								       data-date-format="yyyy-mm-dd"
								       placeholder="<?php _e( 'Publiceringsdatum' ); ?>"
								       value="<?php echo isset( Sk_Fishingsundsvall_Public::$filter['cr-dateofcatch'][0] ) ? date( 'Y-m-d', strtotime( Sk_Fishingsundsvall_Public::$filter['cr-dateofcatch'][0] ) ) : date( 'Y-m-d', strtotime( '-1 year' ) ); ?>">
							</div>
						</div><!-- .form-group -->

						<div class="form-group row">
							<label for="inputEmail3"
							       class="col-sm-4 col-form-label"><?php _e( 'Datum till: ', 'sk_tivoli' ); ?></label>
							<div class="col-sm-6">
								<input type="text" name="cr_date_to"
								       class="form-control datepicker"
								       data-date-format="yyyy-mm-dd"
								       placeholder="<?php _e( 'Publiceringsdatum' ); ?>"
								       value="<?php echo isset( Sk_Fishingsundsvall_Public::$filter['cr-dateofcatch'][1] ) ? date( 'Y-m-d', strtotime( Sk_Fishingsundsvall_Public::$filter['cr-dateofcatch'][1] ) ) : date( 'Y-m-d' ); ?>">
							</div>
						</div><!-- .form-group -->

					</div><!-- .col -->

					<div class="col-sm-6">

						<div class="form-group row">
							<label for="cr-species"
							       class="col-sm-4 col-form-label"><?php _e( 'Fiskart: ', 'sk_tivoli' ); ?></label>
							<div class="col-sm-6">
								<select name="cr_species" class="form-control" id="cr-species">
									<option value=""><?php _e( 'Alla arter', 'sk_tivoli' ); ?></option>
									<?php foreach ( Sk_Fishingsundsvall_Public::get_catchreport_terms( 'catchreport-fish' ) as $specie ) : ?>
										<option
											value="<?php echo $specie->term_id; ?>" <?php selected( $specie->term_id, isset( Sk_Fishingsundsvall_Public::$filter['cr-species'] ) ? Sk_Fishingsundsvall_Public::$filter['cr-species'] : null, true ); ?>><?php echo $specie->name; ?></option>
									<?php endforeach; ?>

								</select>
							</div>
						</div>

						<div class="form-group row">
							<label for="cr-place"
							       class="col-sm-4 col-form-label"><?php _e( 'Fångstplats: ', 'sk_tivoli' ); ?></label>
							<div class="col-sm-6">
								<select name="cr_place" class="form-control" id="cr-place">
									<option value=""><?php _e( 'Alla platser', 'sk_tivoli' ); ?></option>
									<?php foreach ( Sk_Fishingsundsvall_Public::get_catchreport_terms( 'catchreport-place' ) as $place ) : ?>
										<option
											value="<?php echo $place->term_id; ?>" <?php selected( $place->term_id, isset( Sk_Fishingsundsvall_Public::$filter['cr-place'] ) ? Sk_Fishingsundsvall_Public::$filter['cr-place'] : null, true ); ?>><?php echo $place->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>


					</div><!-- .col -->

				</div><!-- .row -->


				<div class="row">
					<div class="col-sm-12">
						<div class="form-check">
							<label class="form-check-label">
								<input type="checkbox" name="cr_show_all" value="on" class="form-check-input" <?php checked( 'on', isset( $_POST['cr_show_all'] ) ? 'on' : null, true ) ?>>
								<?php _e( 'Visa all information (sortering sker på datum)', 'sk_tivoli' ); ?>
							</label>
						</div>
					</div>
				</div>

				<hr>

				<div class="row">


					<div class="col-sm-6">

						<div class="form-group row">
							<label for="cr-orderby"
							       class="col-sm-4 col-form-label"><?php _e( 'Sortera på: ', 'sk_tivoli' ); ?></label>
							<div class="col-sm-6">

								<select name="cr_orderby" class="form-control" id="cr-orderby">
									<?php foreach ( Sk_Fishingsundsvall_Public::get_orderby() as $orderby ) : ?>

										<option
											value="<?php echo $orderby['field']; ?>" <?php selected( $orderby['field'], isset( Sk_Fishingsundsvall_Public::$filter['cr-orderby'] ) ? Sk_Fishingsundsvall_Public::$filter['cr-orderby'] : null, true ); ?>><?php echo $orderby['name']; ?></option>
									<?php endforeach; ?>

								</select>
							</div>
						</div>

					</div><!-- .col -->

					<div class="col-sm-6">

						<div class="form-check form-check-inline">
							<label class="form-check-label">
								<input class="form-check-input" type="radio" name="cr_order"
								       id="" <?php checked( 'ASC', isset( Sk_Fishingsundsvall_Public::$filter['cr-order'] ) ? Sk_Fishingsundsvall_Public::$filter['cr-order'] : null, true ) ?>
								       value="ASC"> <?php _e( 'Stigande', 'sk_tivoli' ); ?>
							</label>
							<label class="form-check-label">
								<input class="form-check-input" type="radio" name="cr_order"
								       id="" <?php checked( 'DESC', isset( Sk_Fishingsundsvall_Public::$filter['cr-order'] ) ? Sk_Fishingsundsvall_Public::$filter['cr-order'] : null, true ) ?>
								       value="DESC"> <?php _e( 'Fallande', 'sk_tivoli' ); ?>
							</label>
						</div>

					</div><!-- .col -->

				</div><!-- .row -->


				<div class="row">
					<div class="col-sm-12">
						<button type="submit"
						        class="btn btn-secondary"><?php _e( 'Visa fångster', 'sk_tivoli' ); ?></button>
					</div>
				</div>


				<input type="hidden" name="archive-filter" value="">
			</form>

		</div><!-- .card-block -->
	</div><!-- .card -->


	<div class="row posts">
		<div class="col-md-12">
			<div class="card">
				<div class="card-block">
					<table class="table table-striped table-sm<?php echo isset( $_POST['cr_show_all'] ) ? ' table-xs' : null; ?>">
						<thead>
						<tr class="table-info">
							<th><?php _e( 'Datum', 'sk_tivoli' ); ?></th>
							<th><?php _e( 'Fiskart', 'sk_tivoli' ); ?></th>
							<th><?php _e( 'Vikt', 'sk_tivoli' ); ?></th>
							<th><?php _e( 'Fiskare', 'sk_tivoli' ); ?></th>
							<th><?php _e( 'Plats', 'sk_tivoli' ); ?></th>
							<th><?php _e( 'Redskap', 'sk_tivoli' ); ?></th>
							<?php if(isset( $_POST['cr_show_all'])) : ?>
								<th><?php _e( 'Fenklippt', 'sk_tivoli' ); ?></th>
								<th><?php _e( 'Återutsatt', 'sk_tivoli' ); ?></th>
							<?php endif; ?>
							<th></th>
						</tr>
						</thead>
						<tbody>

						<?php if ( $reports->have_posts() ): while ( $reports->have_posts() ): $reports->the_post(); ?>
							<tr>
								<td scope="row"><?php echo get_field( 'cr-dateofcatch', get_the_ID() ); ?></td>
								<td><?php echo Sk_Fishingsundsvall_Public::get_term_name( get_field( 'cr-species', get_the_ID() ) ); ?></td>
								<td><?php echo get_field( 'cr-weight', get_the_ID() ); ?></td>
								<td><?php echo !empty( get_field('cr-name', get_the_ID() ) ) ? get_field('cr-name', get_the_ID() ) : __('Uppgift saknas', 'sk_tivoli') ;?></td>
								<td><?php echo Sk_Fishingsundsvall_Public::get_term_name( get_field( 'cr-place', get_the_ID() ) ); ?></td>
								<td><?php echo get_field( 'cr-catchmethod', get_the_ID() ); ?></td>
								<?php if(isset( $_POST['cr_show_all'])) : ?>
									<td><?php echo get_field( 'cr-cutted', get_the_ID() ); ?></td>
									<td><?php echo get_field( 'cr-released', get_the_ID() ); ?></td>
								<?php endif; ?>
								<td><a href="<?php echo get_permalink( get_the_ID() );?>" title="<?php _e( 'Läs mer', 'sk_tivoli' );?>"><?php _e( 'Läs mer', 'sk_tivoli' );?></a></td>
							</tr>
						<?php endwhile; endif; ?>
						</tbody>
					</table>
					</div>
				</div>
		</div><!-- .col -->
	</div><!-- .row -->

	<div class="sk-pagination mb-3">
		<?php


		$big        = 999999999; // need an unlikely integer
		$translated = __( 'Sida', 'sk_tivoli' ); // Supply translatable string

		echo paginate_links( array(
			'base'               => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'             => '?paged=%#%',
			'current'            => max( 1, get_query_var( 'paged' ) ),
			'total'              => $reports->max_num_pages,
			'before_page_number' => '<span class="sr-only">' . $translated . ' </span>'
		) );

		?>
	</div>
	<div class="row posts">
		<div class="col-md-12">

			<?php foreach ( Sk_Fishingsundsvall_Public::get_catchreport_terms( 'catchreport-fish' ) as $species ) : ?>
				<h3><?php echo $species->name; ?></h3>
			<div class="card mb-3">
				<div class="card-block">
				<table class="table table-striped table-sm">
					<thead>
					<tr class="table-info">
						<th><?php _e( 'Art', 'sk_tivoli' ); ?></th>
						<th><?php _e( 'Fiskeplats', 'sk_tivoli' ); ?></th>
						<th><?php _e( 'Medelvikt', 'sk_tivoli' ); ?></th>
						<th><?php _e( 'Total vikt', 'sk_tivoli' ); ?></th>
						<th><?php _e( 'Maxvikt', 'sk_tivoli' ); ?></th>
						<th><?php _e( 'Antal', 'sk_tivoli' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					$sum = array( 'total-weight' => 0, 'max' => 0, 'total' => 0 );
					foreach ( Sk_Fishingsundsvall_Public::get_reports_by_species( $species->term_id ) as $results ) : ?>
						<?php foreach ( $results as $item ) : ?>
							<tr>
								<td scope="row"><?php echo $species->name; ?></td>
								<td><?php echo Sk_Fishingsundsvall_Public::get_term_name( $item['place'] ); ?></td>
								<td><?php echo round( $item['average'] ); ?></td>
								<td><?php echo $item['weight']; ?></td>
								<td><?php echo $item['maxweight']; ?></td>
								<td><?php echo $item['total']; ?></td>
							</tr>
							<?php
							$sum['total-weight'] = $item['weight'] + $sum['total-weight'];
							$sum['total']        = $item['total'] + $sum['total'];
							$sum['max']          = $item['maxweight'] > $sum['max'] ? $item['maxweight'] : $sum['max'];
						endforeach; ?>

					<?php endforeach; ?>



						<tr class="table-info">
							<td><?php echo $species->name; ?></td>
							<td><?php _e( 'Alla platser', 'sk_tivoli' ); ?></td>
							<td><?php echo $sum['total-weight'] !== 0 ? round( $sum['total-weight'] / $sum['total'] ) : '0'; ?></td>
							<td><?php echo $sum['total-weight']; ?></td>
							<td><?php echo $sum['max']; ?></td>
							<td><?php echo $sum['total']; ?></td>
						</tr>
					</tbody>
				</table>
					</div>
				</div>
				<?php
				unset( $sum );
			endforeach; ?>
		</div><!-- .col -->
	</div><!-- .row -->
</div>

<?php get_footer(); ?>
