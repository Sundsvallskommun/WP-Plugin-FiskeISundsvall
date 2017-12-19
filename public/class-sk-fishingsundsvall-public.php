<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Fishingsundsvall
 * @subpackage Sk_Fishingsundsvall/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sk_Fishingsundsvall
 * @subpackage Sk_Fishingsundsvall/public
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Fishingsundsvall_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	public static $filter = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;


	}

	/**
	 * Start session if it not exists.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function session_start() {
		if ( ! session_id() ) {
			session_start();
		}
	}


	/**
	 * Register the short code.
	 *
	 * @since 1.0.0
	 *
	 */
	public function add_shortcode() {
		global $post;
		add_shortcode( 'catchreport-form', array( $this, 'html_render_form' ) );

		// add acf_form_head function before get_header if form is activated.
		if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'catchreport-form' ) ) {
			add_action( 'get_header', function () {
				acf_form_head();
			} );
		}

	}


	/**
	 * Get terms for custom taxonomies.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param string $taxonomy
	 *
	 * @return array|int|WP_Error
	 */
	public static function get_catchreport_terms( $taxonomy = '' ) {
		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		) );


		return $terms;
	}

	/**
	 * Get filtered reports by a given art.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param string $species_id
	 *
	 * @return array
	 */
	public static function get_reports_by_species( $species_id = '' ) {
		global $wpdb;
		$places = self::get_catchreport_terms( 'catchreport-place' );
		$between_dates = $_SESSION['filter-reports'][0]['value'][0] . ' AND ' . $_SESSION['filter-reports'][0]['value'][1];

		if ( ! empty( $places ) ) {

			foreach ( $places as $place ) {

				$query = "
				SELECT COUNT(*) as total, 
					meta1.meta_value AS species,
					meta2.meta_value AS place,
					SUM(meta3.meta_value) AS weight,
					MAX( CAST( meta3.meta_value AS UNSIGNED ) ) AS maxweight,
					AVG(meta3.meta_value) AS average
				FROM wp_posts AS post
					LEFT JOIN wp_postmeta AS meta1 ON ( post.ID = meta1.post_id AND meta1.meta_key = 'cr-species' ) 
					LEFT JOIN wp_postmeta AS meta2 ON ( post.ID = meta2.post_id AND meta2.meta_key = 'cr-place' )  
					LEFT JOIN wp_postmeta AS meta3 ON ( post.ID = meta3.post_id AND meta3.meta_key = 'cr-weight' )  
					LEFT JOIN wp_postmeta AS meta4 ON ( post.ID = meta4.post_id AND meta4.meta_key = 'cr-dateofcatch' )
				WHERE 1=1 
					AND meta1.meta_value = '" . $species_id . "'
					AND meta2.meta_value = '" . $place->term_id . "'
					AND CAST(meta4.meta_value AS DATE) BETWEEN " . $between_dates . "
					AND post.post_type = 'catchreport' 
					AND post.post_status = 'publish'
				GROUP BY meta2.meta_value
		        ";

				$result = $wpdb->get_results( $query, ARRAY_A );
				if ( ! empty( $result ) ) {
					$results[] = $result;
				}


			}

			return $results;

		}

	}

	/**
	 * Form filter.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param string $species
	 *
	 * @return WP_Query
	 */
	public static function get_reports( $species = '' ) {

		$meta_query[] =
			array(
				'key'     => 'cr-dateofcatch',
				'value'   => array( date( 'Ymd', strtotime( '-1 year' ) ), date( 'Ymd', strtotime( date( 'Ymd' ) ) ) ),
				'type'    => 'date',
				'compare' => 'BETWEEN'
			);

		$orderby = 'cr-dateofcatch';
		$order   = 'DESC';

		if ( isset( $_POST['archive-filter'] ) && is_post_type_archive( 'catchreport' ) ) {
			$meta_query = array();
			unset( $_SESSION['filter-reports'] );

			if ( isset( $_POST['cr_date_from'] ) && isset( $_POST['cr_date_to'] ) ) {

				$meta_query[] =
					array(
						'key'     => 'cr-dateofcatch',
						'value'   => array(
							date( 'Ymd', strtotime( $_POST['cr_date_from'] ) ),
							date( 'Ymd', strtotime( $_POST['cr_date_to'] ) )
						),
						'type'    => 'date',
						'compare' => 'BETWEEN'
					);

			}

			if ( isset( $_POST['cr_species'] ) && ! empty( $_POST['cr_species'] ) ) {

				$meta_query[] =
					array(
						'key'     => 'cr-species',
						'value'   => intval( $_POST['cr_species'] ),
						'compare' => '='
					);
			}

			if ( isset( $_POST['cr_place'] ) && ! empty( $_POST['cr_place'] ) ) {

				$meta_query[] =
					array(
						'key'     => 'cr-place',
						'value'   => intval( $_POST['cr_place'] ),
						'compare' => '='
					);
			}

			if ( isset( $_POST['cr_orderby'] ) && ! empty( $_POST['cr_orderby'] ) ) {
				$orderby = $_POST['cr_orderby'];
			}

			if ( isset( $_POST['cr_order'] ) && ! empty( $_POST['cr_order'] ) ) {
				$order = $_POST['cr_order'];
			}


			$_SESSION['filter-reports'] = $meta_query;
			$_SESSION['filter-orderby'] = $orderby;
			$_SESSION['filter-order']   = $order;

		}

		if ( empty( $_SESSION['filter-reports'] ) ) {
			$_SESSION['filter-reports'] = $meta_query;
		}

		foreach ( $_SESSION['filter-reports'] as $item ) {
			self::$filter[ $item['key'] ] = $item['value'];
		}

		self::$filter['cr-orderby'] = isset( $_SESSION['filter-orderby'] ) ? $_SESSION['filter-orderby'] : 'cr-dateofcatch';
		self::$filter['cr-order'] = isset( $_SESSION['filter-order'] ) ? $_SESSION['filter-order'] : 'DESC';

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$args  = array(
			'posts_per_page' => 10,
			'post_type'      => 'catchreport',
			'paged'          => $paged,
			'meta_query'     => $meta_query,
			'orderby'        => 'meta_value',
			'meta_key'       => $orderby,
			'order'          => $order

		);

		$reports = new WP_Query( $args );

		return $reports;

	}

	/**
	 * Alter the query for archive.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $query
	 */
	function catchreport_archive( $query ) {

		if ( $query->is_archive() && is_post_type_archive( 'catchreport' ) && ! is_admin() ) {

			$post_per_page = 10;
			if( isset( $_POST['cr_show_all'] ) ){
				$post_per_page = -1;
			}
			$query->set( 'posts_per_page', $post_per_page );
			$query->set( 'meta_query', isset( $_SESSION['filter-reports'] ) ? $_SESSION['filter-reports'] : null );
			$query->set( 'orderby', isset( $_SESSION['filter-orderby'] ) ? $_SESSION['filter-orderby'] : null );
			$query->set( 'order', isset( $_SESSION['filter-order'] ) ? $_SESSION['filter-order'] : null );
		}

	}

	/**
	 * Fields to be able to be ordered.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return array
	 */
	public static function get_orderby() {

		$orderby = array(
			array(
				'name'  => 'Datum',
				'field' => 'cr-dateofcatch'
			),
			array(
				'name'  => 'Fiskart',
				'field' => 'cr-species'
			),
		);

		return $orderby;

	}


	/**
	 * Get term name by term id.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $term_id
	 *
	 * @return string
	 */
	public static function get_term_name( $term_id ) {

		$term = get_term( $term_id );
		return $term->name;

	}


	/**
	 * Render the html form.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return string
	 */
	public function html_render_form(){
		//start buffering
		ob_start();

		acf_form(array(
			'post_id'		=> 'new_post',
			'new_post'		=> array(
				'post_type'		=> 'catchreport',
				'post_status'		=> 'publish'
			),
			'html_before_fields' => '<div class="card"><div class="card-block"><p>'.__('Fält markerade med asterix (*) är obligatoriska.','sk_tivoli').'</p>',
			'html_after_fields' => '<div class="clearfix"></div></div></div>',
			'submit_value'		=> __('Skicka in fångstrapport', 'sk_tivoli' ),
			'updated_message' => __('Tack för fångstrapporten!', 'sk_tivoli' ),
		));

		?>
		<script type="text/javascript">
			(function($) {
				$('#acf-form').find('.acf-field').addClass('col-md-6');
				$('#acf-form').find('.acf-field-textarea').removeClass('col-md-6').addClass('col-md-12');
				$('#acf-form').find('input, select, textarea').addClass('form-control');
				$('#acf-form').find('.acf-radio-list input, .acf-checkbox-list input').removeClass('form-control');
				$('#acf-form').find('.acf-button').addClass('btn btn-secondary').removeClass('form-control');

			})(jQuery);
		</script>
<?php

		$output = ob_get_contents();
		ob_get_clean();

		return $output;
	}


	/**
	 * Adding single template for post type.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $single_template
	 *
	 * @return string
	 */
	public function single_template( $single_template ) {

		// check for post type
		if ( is_singular( 'catchreport' ) ) {
			$single_template = plugin_dir_path( __DIR__ ) . 'templates/single-catchreport.php';
		}

		return $single_template;

	}

	/**
	 * Adding archive template.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $archive_template
	 *
	 * @return string
	 */
	public function archive_template( $archive_template ) {
		if ( is_post_type_archive( 'catchreport' ) ) {
			$archive_template = plugin_dir_path( __DIR__ ) . 'templates/archive-catchreport.php';
		}

		return $archive_template;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sk_Fishingsundsvall_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sk_Fishingsundsvall_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sk-fishingsundsvall-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-datepicker', plugin_dir_url( __FILE__ ) . 'css/datepicker/datepicker.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sk_Fishingsundsvall_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sk_Fishingsundsvall_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sk-fishingsundsvall-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-datepicker', plugin_dir_url( __FILE__ ) . 'js/datepicker/bootstrap-datepicker.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-datepicker-locale', plugin_dir_url( __FILE__ ) . 'js/datepicker/locales/bootstrap-datepicker.sv.js', array( 'jquery' ), $this->version, false );

	}

}
