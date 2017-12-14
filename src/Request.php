<?php

namespace Yogasukmap\AffiliateWPApproval;

use Yogasukmap\AffiliateWPApproval\Database\AffiliateWPRequestLogTable;

class Request {

	protected $db;
	protected $affiliate_id;

	public function __construct() {
		$this->db = new AffiliateWPRequestLogTable();
	}

	public function create( $data ) {
		if ( ! is_array( $data ) && is_numeric( $data ) ) {
			$data = [
				"post_id" => $data
			];
		}

		if ( ! isset( $data["post_id"] ) ) {
			return false;
		}

		if ( isset( $data["user_id"] ) ) {
			$data["affiliate_id"] = ( new Affiliate() )->get_affiliate_id( $data["user_id"] );
		}

		if ( ! isset( $data["affiliate_id"] ) ) {
			$data["affiliate_id"] = ( new Affiliate() )->my_affiliate_id();
		}

		$data["status"]       = "requested";
		$data["type"]         = "request";
		$data["requested_by"] = isset( $data["user_id"] ) ? $data["user_id"] : get_current_user_id();
		$data["requested_at"] = date( "Y-m-d H:i:s" );

		unset( $data["user_id"] );

		if ( ! $this->db->by_affiliate( $data["affiliate_id"] )->for_post( $data["post_id"] )->is_exists() ) {
			$this->db->insert( $data );
		}
	}

	public function approve( $id ) {
		$this->db->update( [
			"status"      => "approved",
			"approved_at" => date( "Y-m-d H:i:s" )
		], [
			"id" => $id
		] );
	}

	public function reject( $id ) {
		$this->db->update( [
			"status"      => "rejected",
			"rejected_at" => date( "Y-m-d H:i:s" )
		], [
			"id" => $id
		] );
	}

	public function revoke( $id ) {
		$this->db->update( [
			"status"     => "revoked",
			"revoked_at" => date( "Y-m-d H:i:s" )
		], [
			"id" => $id
		] );
	}

	public function waiting_approval( $user_id = null ) {
		return $this->db->with_status( "requested" )->for_post_authored_by( $user_id )->get();
	}

	public function approved( $user_id = null ) {
		return $this->db->with_status( "approved" )->for_post_authored_by( $user_id )->get();
	}
}