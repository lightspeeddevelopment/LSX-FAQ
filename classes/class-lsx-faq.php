<?php
namespace lsx;
/**
 * Main Class
 *
 * @package   LSX FAQ
 * @author    LightSpeed
 * @license   GPL-3.0+
 * @link
 * @copyright 2017 LightSpeedDevelopment
 */
class lsx_faq {
	/**
	 * Holds the edit class
	 * @var array
	 */
	var $edit = false;
	/**
	 * Holds the edit class
	 * @var array
	 */
	var $frontend = false;
	/**
	 * Holds instance of the class
	 */
	private static $instance;
	/**
	 * Constructor.
	 */
	public function __construct() {
		require_once( WC_PDD_PATH . 'classes/class-lsx-faq.php' );
		$this->setup();
	}
	/**
	 * Return an instance of this class.
	 *
	 * @return  object
	 */
	public static function init() {
		// If the single instance hasn't been set, set it now.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Setup hooks and text load domain
	 */
	public function setup() {
		$this->edit = new Lsx_Faq_Edit();
	}
}