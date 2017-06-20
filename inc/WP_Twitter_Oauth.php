<?php


class WP_Twitter_Oauth {
	public $consumer_key;
	protected $consumer_secret;
	public $request;
	public $params = array();
	public $cache;
	public $display_media;

	public function __construct(
		$Request = 'users/show',
		$Params = array(),
		$Cache = 900
	) {

		$twitter_oauth_var = get_option( 'welt_twitter_oauth_var' );

		if ( $twitter_oauth_var && isset( $twitter_oauth_var['consumer_key'] ) && isset( $twitter_oauth_var['consumer_secret'] ) ) {

			$this->consumer_key    = (string) $twitter_oauth_var['consumer_key'];
			$this->consumer_secret = (string) $twitter_oauth_var['consumer_secret'];
			$this->request         = (string) $Request;
			$this->params          = (array) $Params;
			$this->cache           = (int) $Cache;

		} else {

			return __( 'The class is not set properly!', 'widget-embed-lastest-tweets' );

		}

	}

	/*
	* Get token from Twitter API 1.1
	* returns $access_token
	*/
	protected function get_access_token() {
		$credentials = $this->consumer_key . ':' . $this->consumer_secret;
		$auth        = base64_encode( $credentials );
		$args        = array(
			'httpversion' => '1.1',
			'headers'     => array(
				'Authorization' => 'Basic ' . $auth,
				'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',

				// !important

			),
			'body'        => array(
				'grant_type' => 'client_credentials',
			),
		);

		$call = wp_remote_post( 'https://api.twitter.com/oauth2/token', $args );

		// need to know what's going on before proceeding
		if ( 200 == wp_remote_retrieve_response_code( $call ) ) {
			$keys = json_decode( wp_remote_retrieve_body( $call ) );
			update_option( md5( $this->consumer_key . $this->consumer_secret ) . '_twitter_access_token', $keys->access_token );

			return __( 'Access granted ^^ !', 'widget-embed-lastest-tweets' );
		} else {

			return $this->check_http_code( wp_remote_retrieve_response_code( $call ) );

		}

	}


	/*
	* Full check
	* returns $error
	*/
	protected function check_http_code( $http_code ) {

		switch ( $http_code ) {

			case '400':
			case '401':
			case '403':
			case '404':
			case '406':
				$error = '<div class="error">' . __( 'Your credentials might be unset or incorrect or username is wrong. In any case this error is not due to Twitter API.', 'widget-embed-lastest-tweets' ) . '</div>';
				break;

			case '429':
				$error = '<div class="error">' . __( 'Rate limits are exceed!', 'widget-embed-lastest-tweets' ) . '</div>';
				break;

			case '500':
			case '502':
			case '503':
				$error = '<div class="error">' . __( 'Twitter is overwhelmed or something bad happened with its API.', 'widget-embed-lastest-tweets' ) . '</div>';
				break;

			default:
				$error = '<div class="error">' . __( 'Something is wrong or missing. ', 'widget-embed-lastest-tweets' ) . '</div>';

		}

		return $error;

	}

	/*
	* Get object from Twitter API 1.1 with the $access_token
	* returns $obj from Twitter
	*/
	protected function get_obj() {
		$this->get_access_token();
		$access_token = get_option( md5( $this->consumer_key . $this->consumer_secret ) . '_twitter_access_token' );

		$args = array(
			'httpversion' => '1.1',
			'timeout'     => 120,
			'headers'     => array(
				'Authorization' => "Bearer {$access_token}",
			)
		);

		$defaults = array(
			'count' => 1,
		);

		$q     = "https://api.twitter.com/1.1/{$this->request}.json";
		$sets  = wp_parse_args( $this->params, $defaults );
		$query = add_query_arg( $sets, $q );

		$call = wp_remote_get( $query, $args );

		if ( 200 == wp_remote_retrieve_response_code( $call ) ) {
			$obj = json_decode( wp_remote_retrieve_body( $call ) );
		} else {
			$this->delete_cache();
			$obj = $this->check_http_code( wp_remote_retrieve_response_code( $call ) );
		}

		return apply_filters( 'the_twitter_object', $obj );
	}


	/*
	* Get infos but make sure there's some cache
	* returns (object) $infos from Twitter
	*/
	public function get_infos() {

		$set_cache = isset( $this->params ) ? implode( ',', $this->params ) . $this->request : $this->request;

		$cached = unserialize( base64_decode( get_site_transient( md5( $set_cache ) ) ) );// tips with base64_decode props to raherian

		if ( FALSE === $cached ) {
			$cached = $this->get_obj();
			set_site_transient( md5( $set_cache ), base64_encode( serialize( $cached ) ), $this->cache );//900 by default because Twitter says every 15 minutes in its doc
		}

		return $cached;
	}


	/*
	* Delete cache
	* In case you need to delete transient
	*/
	protected function delete_cache() {
		$set_cache = isset( $this->params ) ? implode( ',', $this->params ) . $this->request : $this->request;
		delete_site_transient( md5( $set_cache ) );
	}

}