<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Fishingsundsvall
 * @subpackage Sk_Fishingsundsvall/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sk_Fishingsundsvall
 * @subpackage Sk_Fishingsundsvall/admin
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Fishingsundsvall_Admin {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;


	}

	/**
	 * Init
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function init() {
		if ( isset( $_GET['import'] ) && $_GET['import'] === 'xz' ) {
			$this->importer();
		}
	}


	/**
	 * Import old data method.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function importer() {

		$file = plugin_dir_path( __DIR__ ) . 'temp.csv';
		echo $file;
		$lines = file( $file );

		foreach ( $lines as $line ) {
			$items[] = explode( ';', $line );
		}

		$temp = array();
		set_time_limit( 360 );
		foreach ( $items as $key => $item ) {
			foreach ( $item as $index => $value ) {
				if ( $index === 0 ) {
					$term                   = get_term_by( 'name', trim( $value ), 'catchreport-fish' );
					$temp[ $key ][ $index ] = $term->term_id;
				} elseif ( $index === 4 ) {
					$term                   = get_term_by( 'name', trim( $value ), 'catchreport-place' );
					$temp[ $key ][ $index ] = $term->term_id;
				} else {
					$temp[ $key ][ $index ] = trim( $value );
				}
			}
		}

		unset( $items );

		$items = $temp;

		$i = 0;
		foreach ( $items as $item ) {

			$post_data = array(
				'post_author'   => '1',
				'post_date'     => $item[1] . ' 00:00:00',
				'post_date_gmt' => $item[1] . ' 00:00:00',
				'post_title'    => $item[1],
				'post_status'   => 'publish',
				'post_type'     => 'catchreport',
			);

			$post_id = wp_insert_post( $post_data );

			// add id as post_name for correct permalink
			wp_update_post( array( 'ID' => $post_id, 'post_name' => $post_id, 'post_title' => $item[1] . ', ' . $post_id ) );


			$post_meta = array(
				'cr-name'        => $item[3],
				'cr-species'     => $item[0], //hämta
				'cr-weight'      => $item[2],
				'cr-place'       => $item[4], //hämta
				'cr-released'    => $item[7],
				'cr-cutted'      => $item[6],
				'cr-catchmethod' => $item[5],
				'cr-dateofcatch' => $item[1]
			);

			foreach ( $post_meta as $meta_key => $meta_value ) {
				update_post_meta( $post_id, $meta_key, $meta_value );
			}

			$i++;
		}
		die( 'Importerat ' . $i );
	}


	/**
	 * Register post type for reports
	 *
	 * @since    1.0.0
	 *
	 */
	public function register_post_type() {

		register_post_type( 'catchreport',
			array(
				'labels'          => array(
					'name'          => __( 'Fångstrapport', 'sk_tivoli' ),
					'singular_name' => __( 'Fångstrapport', 'sk_tivoli' ),
					'add_new'       => __( 'Ny rapport', 'sk_tivoli' ),
					'add_new_item'  => __( 'Skapa ny rapport', 'sk_tivoli' ),
					'edit_item'     => __( 'Redigera rapport', 'sk_tivoli' ),
				),
				'public'          => true,
				'show_ui'         => true,
				'menu_position'   => 6,
				'menu_icon'       => 'dashicons-list-view',
				'capability_type' => 'post',
				'has_archive'     => true,
				'hierarchical'    => false,
				'rewrite'         => array( 'slug' => 'fangstrapport', 'with_front' => false ),
				'supports'        => false
			)
		);
	}


	/**
	 * Register taxonomies for place and species.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function register_taxonomy() {

		register_taxonomy(
			'catchreport-place',
			'catchreport',
			array(
				'label'        => __( 'Fångstplats', 'sk_tivoli' ),
				'public'       => true,
				'show_ui'      => true,
				'hierarchical' => true,
			)
		);

		register_taxonomy(
			'catchreport-fish',
			'catchreport',
			array(
				'label'        => __( 'Fiskart', 'sk_tivoli' ),
				'public'       => true,
				'show_ui'      => true,
				'hierarchical' => true,
			)
		);

	}


	/**
	 * Remove the meta boxes for place and species.
	 * We dont want this to be editable when editing a post.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'catchreport-placediv', 'catchreport', 'side' );
		remove_meta_box( 'catchreport-fishdiv', 'catchreport', 'side' );
	}


	/**
	 * Save post hook
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function save_post( $post_id ) {

		$post_type = get_post_type( $post_id );
		if ( $post_type !== 'catchreport' ) {
			return false;
		}

		//set title to catcher name and post_name to id
		$values = array(
			'ID'         => $post_id,
			'post_title' => get_field( 'cr-dateofcatch', $post_id ) . ', ' . Sk_Fishingsundsvall_Public::get_term_name( get_field( 'cr-place', $post_id ) ) . ', ' . Sk_Fishingsundsvall_Public::get_term_name( get_field( 'cr-species', $post_id ) ),
			'post_name'  => $post_id
		);

		// prevent inifity loop by remove hook.
		remove_action( 'save_post_catchreport', array( $this, 'save_post' ) );
		// update the post.
		wp_update_post( $values );
		// adding hook back.
		add_action( 'save_post_catchreport', array( $this, 'save_post' ) );


	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sk-fishingsundsvall-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sk-fishingsundsvall-admin.js', array( 'jquery' ), $this->version, false );

	}

}
