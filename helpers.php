<?php

/**
 * Request to be affiliate for post, product or any other post type based on post ID
 *
 * @param int $post_id
 *
 * @return void
 */
function request_to_be_affiliate_for( $post_id ) {
	$request = new Yogasukmap\AffiliateWPApproval\Request();
	$request->create( $post_id );
}


/**
 * Invite affiliate to promote the post
 *
 * @param int $affiliate_id
 * @param int $post_id of url/products/posts that want to be affiliated
 *
 * @return void
 */
function invite_affiliate( $affiliate_id, $post_id ) {
	$invitation = new Yogasukmap\AffiliateWPApproval\Invitation();
	$invitation->create( [
		"affiliate_id" => $affiliate_id,
		"post_id"      => $post_id
	] );
}

/**
 * Update status of request
 * it should called by vendor/owner
 * it will check if the current login is the owner of post_id
 *
 * @param int $id of request
 * @param string $status of new status that applied to this request
 *
 * @return void
 */
function update_request_status( $id, $status ) {
	$request = new Yogasukmap\AffiliateWPApproval\Request();
	if ( $status == "approve" ) {
		$request->approve( $id );
	}

	if ( $status == "reject" ) {
		$request->reject( $id );
	}

	if ( $status == "revoked" ) {
		$request->revoke( $id );
	}
}

/**
 * Check if current user has sent request for this post or not
 *
 * @param int $post_id
 *
 * @return bool
 */
function is_referring_request_sent_for( $post_id ) {
	$affiliateWPApproval = new Yogasukmap\AffiliateWPApproval\Core();

	return $affiliateWPApproval->is_requested_before( get_current_user_id(), $post_id );
}

/**
 * Getting list of request with status "waiting" for product who owned by $user_id
 * if $user_id = null, then $user id is current active login
 *
 * @param int|null $user_id
 *
 * @return null|array
 */
function get_waiting_request( $user_id = null ) {
	$request = new Yogasukmap\AffiliateWPApproval\Request();

	if ( is_null( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return $request->waiting_approval( $user_id );
}

/**
 * Getting list of request with status "invited" for affiliate_id
 *
 * @param int|null $user_id
 *
 * @return null|array
 */
function get_invited_request( $user_id = null ) {
	$invitation = new Yogasukmap\AffiliateWPApproval\Invitation();

	if ( is_null( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return $invitation->invited( $user_id );
}

/**
 * Getting list of request with status "approved" for product who owned by $user_id
 * if $user_id = null, then $user id is current active login
 *
 * @param int|null $user_id
 *
 * @return null|array
 */
function get_approved_request( $user_id = null ) {
	$request = new Yogasukmap\AffiliateWPApproval\Request();

	if ( is_null( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return $request->approved( $user_id );
}

/**
 * Get list of all registered and active affiliates
 *
 * @return null|array
 */
function get_active_affiliates() {
	$affiliates = new \Yogasukmap\AffiliateWPApproval\Affiliate();

	return $affiliates->withStatus( "active" )->get();
}

