<?php
/**
 * Plugin Name: AffiliateWP - Approval Process
 * Plugin URI: https://github.com/ysupr/affiliatewp-approval
 * Description: AffiliateWP add on to create approval process before referring some product
 * Version: 1.0.0
 * Author: Yoga Sukma
 * Author URI: https://yogasukma.web.id
 */

include "vendor/autoload.php";

$requestPostType = new Yogasukmap\AffiliateWPApproval\RequestPostType();
add_action( "init", [ $requestPostType, "create_post_type" ] );
add_action( "init", [ $requestPostType, "create_custom_status" ] );


$affiliateWPApproval = new Yogasukmap\AffiliateWPApproval\Core();
add_filter( "affwp_tracking_skip_track_visit", [ $affiliateWPApproval, "skipping_referral" ], 10, 5 );

/**
 * Request to be affiliate for post, product or any other post type based on post ID
 *
 * @param int $post_id
 *
 * @return void
 */
function request_to_be_affiliate_for( $post_id ) {
	$affiliateWPApproval = new Yogasukmap\AffiliateWPApproval\Core();
	$affiliateWPApproval->request( $post_id );
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
