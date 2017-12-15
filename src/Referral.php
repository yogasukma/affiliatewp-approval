<?php


namespace Yogasukmap\AffiliateWPApproval;


use Yogasukmap\AffiliateWPApproval\Database\AffiliateWPRequestLogTable;

class Referral {

	protected $db;

	public function __construct() {
		$this->db = new AffiliateWPRequestLogTable();
	}

	public function skipping_referral( $skip, $affiliate_id, $is_valid, $referrer, $tracker ) {
		$post_id = url_to_postid( $_SERVER["HTTP_REFERER"] );
		$request = $this->db->by_affiliate( $affiliate_id )->for_post( $post_id )->with_status( "approved" )->get();
		if ( empty( $request ) ) {
			return true;
		}

		return false;
	}

}