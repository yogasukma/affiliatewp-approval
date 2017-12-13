<?php

namespace Yogasukmap\AffiliateWPApproval;

class Core {
	public $post_type;
	public $statuses;

	public function __construct() {
		$this->post_type = ( new RequestPostType() )->post_type;
		$this->statuses  = ( new RequestPostType() )->statuses;
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

		$request = wp_insert_post( [
			"post_title"  => $user->display_name . " sent referring request to '" . $post->post_title . "'",
			"post_type"   => $this->post_type,
			"post_status" => "publish",
			"post_parent" => $post->ID
		] );

		if ( $request ) {
			add_post_meta($request->ID, "_request-status", "waiting");
		}

		return $request;
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
			"author"    => $user_id,
			"post_type" => $this->post_type,
		];

		if ( ! is_null( $parent_id ) ) {
			$args["parent"] = $parent_id;
		}

		$requests = get_posts( $args );

		return count( $requests ) == 1 ? $requests[0] : $requests;
	}

	/**
	 * Update status by site owner / vendor / seller
	 * status will be saved as post meta as _request-status
	 *
	 * @param int $post_id
	 * @param string $status
	 *
	 * @return void
	 */
	public function update_status( $post_id, $status ) {
		update_post_meta( $post_id, "_request-status", $status );
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
		$request = $this->get_user_request( $user_id, $post_id );

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

		if ( empty( $request ) || get_post_meta( $request->ID, "_request-status", true ) != "approved" ) {
			return true;
		}

		return false;
	}
}