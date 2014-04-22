<?php
class bStat
{
	public  $id_base = 'bstat';
	public  $version = 5;

	public function __construct()
	{
		add_action( 'init', array( $this, 'init' ), 1 );
	} // END __construct

	public function init()
	{
		add_action( 'template_redirect', array( $this, 'template_redirect' ), 15 );

		wp_register_script( $this->id_base, plugins_url( plugin_basename( __DIR__ ) ) . '/js/bstat.js', array( 'jquery' ), $this->version, TRUE );
		wp_enqueue_script( $this->id_base );
	} // END init

	public function options()
	{
		if ( ! $this->options )
		{
			$this->options = (object) apply_filters(
				'go_config',
				array(
					'endpoint' => admin_url( '/admin-ajax.php?action=' . $this->id_base ),
					'secret' => $this->version,
					'session_cookie' => (object) array(
						'domain' => COOKIE_DOMAIN, // a WP-provided constant
						'path' => '/',
						'duration'=> 1800, // 30 minutes in seconds
					),
				),
				$this->id_base
			);
		}

		return $this->options;
	} // END options

	public function template_redirect()
	{
		wp_localize_script( $this->id_base, $this->id_base, $this->wp_localize_script() );
	} // END template_redirect

	public function wp_localize_script()
	{
		global $wpdb;
		$details = array(
			'post'       => is_singular() ? get_queried_object_id() : FALSE, // this is either an int or BOOL
			'blog'       => (int) $this->get_blog(),
			'endpoint'   => esc_js( $this->options()->endpoint ),
		);
		$details['signature'] = $this->get_signature( $details );

		return $details;
	}

	public function get_signature( $details )
	{
		return md5( (int) $details['post'] . (int) $details['blog'] . (string) $this->options()->secret );
	}

	public function get_blog()
	{
		global $wpdb;
		return isset( $wpdb->blogid ) ? $wpdb->blogid : 1;
	}

	public function get_session()
	{
		// get or start a session
		if ( isset( $_COOKIE[ $this->id_base ]['session'] ) )
		{
			$session = $_COOKIE[ $this->id_base ]['session'];
		}
		else
		{
			$session = md5( microtime() . $this->options()->secret );
		}

		// set or update the cookie to expire in 30 minutes or so (configurable)
		setcookie(
			$this->id_base . '[session]',
			$session,
			time() + $this->options()->session_cookie->duration,
			$this->options()->session_cookie->path,
			$this->options()->session_cookie->domain
		);

		return $session;
	}

	public function initial_setup()
	{
		$this->db()->initial_setup();
	}

}

function bstat()
{
	global $bstat;

	if ( ! $bstat )
	{
		$bstat = new bStat;
	}

	return $bstat;
} // end bstat