<?php

namespace Yogasukmap\AffiliateWPApproval;

class Affiliate {
	public function my_affiliate_id() {
		return $this->get_affiliate_id( get_current_user_id() );
	}

	public function get_affiliate_id( $user_id ) {
		if ( function_exists( "affwp_get_affiliate_id" ) ) {
			return affwp_get_affiliate_id( $user_id );
		}

		return false;
	}
}