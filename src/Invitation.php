<?php

namespace Yogasukmap\AffiliateWPApproval;

use Yogasukmap\AffiliateWPApproval\Database\AffiliateWPRequestLogTable;

class Invitation {

	protected $db;
	protected $affiliate_id;

	public function __construct() {
		$this->db = new AffiliateWPRequestLogTable();
	}

	public function create( $data ) {
		if ( ! isset( $data["affiliate_id"] ) || ! isset( $data["post_id"] ) ) {
			return false;
		}

		$data["status"]       = "invited";
		$data["type"]         = "invitation";
		$data["requested_by"] = isset( $data["user_id"] ) ? $data["user_id"] : get_current_user_id();
		$data["requested_at"] = date( "Y-m-d H:i:s" );

		unset( $data["user_id"] );

		if ( ! $this->db->by_affiliate( $data["affiliate_id"] )->for_post( $data["post_id"] )->is_exists() ) {
			$this->db->insert( $data );
		}
	}

	public function invited( $user_id ) {
		$affiliate_id = ( new Affiliate() )->get_affiliate_id( $user_id );

		return $this->db->by_affiliate( $affiliate_id )->with_status( "invited" )->get();
	}
}