<?php

namespace Yogasukmap\AffiliateWPApproval;

class Core {
	protected $post_type = "referring-request";


	/**
	 * Registering new post type
	 *
	 * @return void
	 */
	public function create_post_type() {
		register_post_type( $this->post_type, [
			'label'       => "Reffering Request",
			'description' => "List of referring request",
			'public'      => true
		] );
	}

	public function create_custom_status() {
		$statuses = [ "waiting", "approved", "rejected" ];

		foreach ( $statuses as $status ) {
			register_post_status( $status, [
				'label'                     => _x( ucfirst( $status ), 'post' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( ucfirst( $status ) . ' <span class="count">(%s)</span>', ucfirst( $status ) . ' <span class="count">(%s)</span>' ),
			] );
		}
	}

	/**
	 * Create new referral request based on current user login
	 *
	 * @param $post_id
	 *
	 * @return bool|int|\WP_Error
	 */
	public function request( $post_id ) {
		$user = wp_get_current_user();
		$post = get_post( $post_id );

		if ( empty( $user ) || empty( $post ) ) {
			return false;
		}

		if ( $this->is_requested_before( $user->ID, $post->ID ) ) {
			return false;
		}

		return wp_insert_post( [
			"post_title"  => $user->display_name . " sent referring request to '" . $post->post_title . "'",
			"post_type"   => $this->post_type,
			"post_status" => "waiting",
			"post_parent" => $post->ID
		] );
	}

	/**
	 * Get list of user's request
	 *
	 * @param int|null $user_id who referring
	 * @param int|null $parent_id of product/post/urls
	 *
	 * @return \WP_Post|array
	 */
	public function get_user_request( $user_id = null, $parent_id = null ) {
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$args = [
			"post_type"   => $this->post_type,
			"author"      => $user_id,
			"post_status" => [ "waiting", "approved", "rejected" ]
		];

		if ( ! is_null( $parent_id ) ) {
			$args["parent"] = $parent_id;
		}

		$requests = get_posts( $args );

		return count( $requests ) == 1 ? $requests[0] : $requests;
	}

	/**
	 * Update status by site owner / vendor / seller
	 *
	 * @param int $post_id
	 * @param string $status
	 *
	 * @return void
	 */
	public function update_status( $post_id, $status ) {
		wp_update_post( [
			"ID"          => $post_id,
			"post_status" => $status
		] );
	}

	/**
	 * Check if there is any request created before for certain user and post
	 *
	 * @param $user_id ID of user that will checked
	 * @param $post_id ID of the post the will checked
	 *
	 * @return bool true if it was created before, or false if not found any request created for this user and post
	 */
	public function is_requested_before( $user_id, $post_id ) {
		$request = get_posts( [
			"author"      => $user_id,
			"post_parent" => $post_id,
			"post_type"   => $this->post_type,
			"post_status" => [ "waiting", "approved", "rejected" ]
		] );

		return ! empty( $request ) ? true : false;
	}

	/**
	 * Check if current referral should saved or not.
	 * If the post/product/urls has been requested before by the affiliate, but not approved yet, we should skip it.
	 *
	 * @param $skip
	 * @param $affiliate_id
	 * @param $is_valid
	 * @param $referrer url, but can't be used since it just showing as admin-ajax.php
	 * @param $tracker  \Affiliate_WP_Tracking class
	 *
	 * @return bool true if current referral should be skipped and not saved, false if it's ok to saved
	 */
	public function skipping_referral( $skip, $affiliate_id, $is_valid, $referrer, $tracker ) {
		if ( ! $is_valid ) {
			return true;
		}

		$post_id = url_to_postid( $_SERVER["HTTP_REFERER"] );
		$user_id = affwp_get_affiliate_user_id( $affiliate_id );
		$request = $this->get_user_request( $user_id, $post_id );

		if ( empty( $request ) || $request->post_status != "approved" ) {
			return true;
		}

		return false;
	}
}