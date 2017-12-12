<?php

namespace Yogasukmap\AffiliateWPApproval;

class Core {
	protected $post_type = "referring-request";

	public function create_post_type() {
		register_post_type( $this->post_type, [
			'label'       => "Reffering Request",
			'description' => "List of referring request",
			'public'      => true
		] );
	}

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

	public function get_user_request( $user_id = null ) {
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		return get_posts( [
			"post_type"   => $this->post_type,
			"post_author" => $user_id,
		] );
	}

	public function update_status( $post_id, $status ) {
		wp_update_post( [
			"ID"          => $post_id,
			"post_status" => $status
		] );
	}

	public function is_requested_before( $user_id, $post_id ) {
		$request = get_posts( [
			"author"      => $user_id,
			"post_parent" => $post_id,
			"post_type"   => $this->post_type,
			"post_status" => [ "waiting", "approved", "rejected" ]
		] );

		return ! empty( $request ) ? true : false;
	}
}