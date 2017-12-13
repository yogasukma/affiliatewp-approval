<?php

namespace Yogasukmap\AffiliateWPApproval;

class RequestPostType {
	public $post_type = "referring-request";

	/**
	 * Create custom status for post especially the referring-request post type
	 * waiting => request has been created, but still waiting approval by post/product/urls owners
	 * approved => request has been created, and approved by owners, referral will be saved
	 * rejected => request has been created, and rejected by owners, so referral will be skipped
	 * revoked => request has been created, and revoked by owners, so referral will be skipped
	 */
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
	 * Create custom metabox to (currently) change request's status
	 *
	 * @return void
	 */
	public function create_metabox() {
		add_meta_box(
			$this->post_type . "_metabox",
			"Set Request Status",
			[ $this, "render_metabox" ],
			$this->post_type,
			"side",
			"high"
		);
	}

	/**
	 * Rendering the metabox
	 *
	 * @return void
	 */
	public function render_metabox() {
		global $post;
		$current_status = get_post_meta( $post->ID, "_request-status", true );

		wp_nonce_field( 'update-affiliate-request-status', "update-affiliate-request-nonce" );

		echo "<select name='affiliate-request-link-status'>";

		foreach ( $this->statuses as $status ) {
			$selected = $status == $current_status ? "selected=selected" : "";
			echo "<option value='" . $status . "' " . $selected . ">" . ucfirst( $status ) . "</option>";
		}

		echo "</select>";
	}

	/**
	 * Saving the request's status.
	 * Pay attention, the request's status will be saved at post meta as _request-status
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function save_metabox_data( $post_id ) {
		if ( get_post_type( $post_id ) != $this->post_type ) {
			return false;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( ! isset( $_POST['update-affiliate-request-nonce'] ) || ! wp_verify_nonce( $_POST['update-affiliate-request-nonce'], 'update-affiliate-request-status' ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post' ) ) {
			return false;
		}

		if ( isset( $_POST["affiliate-request-link-status"] ) ) {
			( new Core() )->update_status( $post_id, $_POST["affiliate-request-link-status"] );
		}

		return true;
	}
}