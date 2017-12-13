<?php

namespace Yogasukmap\AffiliateWPApproval;

class RequestPostType {
	public $post_type = "referring-request";
	public $statuses = [ "waiting", "approved", "rejected", "revoked" ];

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

	/**
	 * Create custom status for post especially the referring-request post type
	 * waiting => request has been created, but still waiting approval by post/product/urls owners
	 * approved => request has been created, and approved by owners, referral will be saved
	 * rejected => request has been created, and rejected by owners, so referral will be skipped
	 * revoked => request has been created, and revoked by owners, so referral will be skipped
	 *
	 * @return void
	 */
	public function create_custom_status() {
		foreach ( $this->statuses as $status ) {
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
}