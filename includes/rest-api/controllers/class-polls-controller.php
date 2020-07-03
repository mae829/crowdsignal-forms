<?php
/**
 * Contains the Polls Controller Class
 *
 * @since 1.0.0
 * @package Crowdsignal_Forms\Rest_Api
 **/

namespace Crowdsignal_Forms\Rest_Api\Controllers;

use Crowdsignal_Forms\Crowdsignal_Forms;
use Crowdsignal_Forms\Models\Poll;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Polls Controller Class
 *
 * @since 1.0.0
 **/
class Polls_Controller {
	/**
	 * The namespace.
	 *
	 * @var string
	 **/
	protected $namespace = 'crowdsignal-forms/v1';

	/**
	 * The rest api base.
	 *
	 * @var string
	 **/
	protected $rest_base = 'polls';

	/**
	 * Register the routes for manipulating polls
	 *
	 * @since 1.0.0
	 **/
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_polls' ),
					'permission_callback' => array( $this, 'get_polls_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_poll' ),
					'permission_callback' => array( $this, 'create_or_update_poll_permissions_check' ),
				),
			)
		);

		// GET polls/:poll_id route.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<poll_id>\d+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_poll' ),
					'permission_callback' => array( $this, 'get_poll_permissions_check' ),
					'args'                => $this->get_poll_fetch_params(),
				),
			)
		);

		// GET polls/:poll_id/results route.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<poll_id>\d+)/results',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_poll_results' ),
					'permission_callback' => array( $this, 'get_poll_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<poll_id>\d+)',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_poll' ),
					'permission_callback' => array( $this, 'create_or_update_poll_permissions_check' ),
					'args'                => $this->get_poll_fetch_params(),
				),
			)
		);

		/**
		 * Archives a poll
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<poll_id>\d+)/archive',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'archive_poll' ),
					'permission_callback' => array( $this, 'create_or_update_poll_permissions_check' ),
				),
			)
		);

		/**
		 * Un-archives a poll, moving it to the last used user folder
		 */
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<poll_id>\d+)/unarchive',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'unarchive_poll' ),
					'permission_callback' => array( $this, 'create_or_update_poll_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Create a new poll.
	 *
	 * @param \WP_REST_Request $request The API Request.
	 * @return \WP_REST_Response|\WP_Error
	 * @since 1.0.0
	 */
	public function create_poll( \WP_REST_Request $request ) {
		$data              = $request->get_json_params();
		$poll              = Poll::from_array( $data );
		$valid_or_wp_error = $poll->validate();
		if ( is_wp_error( $valid_or_wp_error ) ) {
			return $valid_or_wp_error;
		}

		$resulting_poll = Crowdsignal_Forms::instance()->get_api_gateway()->create_poll( $poll );
		if ( is_wp_error( $resulting_poll ) ) {
			return $resulting_poll;
		}

		return rest_ensure_response( $resulting_poll->to_array() );
	}

	/**
	 * Update a poll.
	 *
	 * @param \WP_REST_Request $request The API Request.
	 * @return \WP_REST_Response|\WP_Error
	 * @since 1.0.0
	 */
	public function update_poll( \WP_REST_Request $request ) {
		$data              = $request->get_json_params();
		$poll              = Poll::from_array( $data );
		$valid_or_wp_error = $poll->validate();
		if ( is_wp_error( $valid_or_wp_error ) ) {
			return $valid_or_wp_error;
		}

		$resulting_poll = Crowdsignal_Forms::instance()->get_api_gateway()->update_poll( $poll );
		if ( is_wp_error( $resulting_poll ) ) {
			return $resulting_poll;
		}

		return rest_ensure_response( $resulting_poll->to_array() );
	}

	/**
	 * Archive a poll (Moves poll to the archive folder, does not delete).
	 *
	 * @param \WP_REST_Request $request The API Request.
	 * @return \WP_REST_Response|\WP_Error
	 * @since 1.0.0
	 */
	public function archive_poll( \WP_REST_Request $request ) {
		$poll_id = $request->get_param( 'poll_id' );
		if ( ! isset( $poll_id ) ) {
			return new \WP_Error(
				'no-poll-id',
				__( 'No Poll ID was provided.', 'crowdsignal-forms' ),
				array( 'status' => 400 )
			);
		}

		$resulting_poll = Crowdsignal_Forms::instance()->get_api_gateway()->archive_poll( $poll_id );
		if ( is_wp_error( $resulting_poll ) ) {
			return $resulting_poll;
		}

		return rest_ensure_response( $resulting_poll->to_array() );
	}

	/**
	 * Un-archive a poll (Moves poll to the most recently used user folder).
	 *
	 * @param \WP_REST_Request $request The API Request.
	 * @return \WP_REST_Response|\WP_Error
	 * @since 1.0.0
	 */
	public function unarchive_poll( \WP_REST_Request $request ) {
		$poll_id = $request->get_param( 'poll_id' );
		if ( ! isset( $poll_id ) ) {
			return new \WP_Error(
				'no-poll-id',
				__( 'No Poll ID was provided.', 'crowdsignal-forms' ),
				array( 'status' => 400 )
			);
		}

		$resulting_poll = Crowdsignal_Forms::instance()->get_api_gateway()->unarchive_poll( $poll_id );
		if ( is_wp_error( $resulting_poll ) ) {
			return $resulting_poll;
		}

		return rest_ensure_response( $resulting_poll->to_array() );
	}

	/**
	 * The permission check for creating a new poll.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 **/
	public function create_or_update_poll_permissions_check() {
		return current_user_can( 'publish_posts' );
	}

	/**
	 * Get the polls.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response
	 **/
	public function get_polls() {
		return rest_ensure_response( Crowdsignal_Forms::instance()->get_api_gateway()->get_polls() );
	}

	/**
	 * The permission check.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 **/
	public function get_polls_permissions_check() {
		return true;
	}

	/**
	 * Get a poll by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response|\WP_Error
	 **/
	public function get_poll( $request ) {
		$poll_id = $request->get_param( 'poll_id' );
		if ( null === $poll_id || ! is_numeric( $poll_id ) ) {
			return new \WP_Error(
				'invalid-poll-id',
				__( 'Invalid poll ID', 'crowdsignal-forms' ),
				array( 'status' => 400 )
			);
		}
		$poll = Crowdsignal_Forms::instance()->get_api_gateway()->get_poll( $poll_id );
		if ( is_wp_error( $poll ) ) {
			return rest_ensure_response( $poll );
		}
		return rest_ensure_response( $poll->to_array() );
	}

	/**
	 * Get poll results by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 **/
	public function get_poll_results( $request ) {
		$poll_id = $request->get_param( 'poll_id' );
		return rest_ensure_response( Crowdsignal_Forms::instance()->get_api_gateway()->get_poll_results( $poll_id ) );
	}

	/**
	 * The get-a-poll by ID permission check.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 **/
	public function get_poll_permissions_check() {
		return true;
	}

	/**
	 * Gets the collection params.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_collection_params() {
		return array();
	}

	/**
	 * Returns a validator array for the get-a-poll by ID params.
	 *
	 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_poll_fetch_params() {
		return array(
			'poll_id' => array(
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
		);
	}
}
