<?php

namespace Yogasukmap\AffiliateWPApproval;

class Affiliate {
	protected $status;

	public function my_affiliate_id() {
		return $this->get_affiliate_id( get_current_user_id() );
	}

	public function get_affiliate_id( $user_id ) {
		if ( function_exists( "affwp_get_affiliate_id" ) ) {
			return affwp_get_affiliate_id( $user_id );
		}

		return false;
	}

	public function withStatus( $status ) {
		$this->status = $status;

		return $this;
	}

	public function get_args() {
		$args = [];

		if ( ! is_null( $this->status ) ) {
			$args["status"] = $this->status;
		}

		return $args;
	}

	public function get() {
		$affiliateWP = new \Affiliate_WP_DB_Affiliates();

		return $affiliateWP->get_affiliates($this->get_args());
	}
}