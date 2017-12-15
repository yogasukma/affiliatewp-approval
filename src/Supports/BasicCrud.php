<?php

namespace Yogasukmap\AffiliateWPApproval\Supports;

trait BasicCrud {
	public function by_affiliate( $affiliate_id ) {
		$this->affiliate_id = $affiliate_id;

		return $this;
	}

	public function for_post( $post_id ) {
		$this->post_id = $post_id;

		return $this;
	}

	public function with_status( $status ) {
		$this->status = $status;

		return $this;
	}

	public function for_post_authored_by( $user_id ) {
		$this->post_author = $user_id;

		return $this;
	}

	public function get() {
		return $this->wpdb->get_results( "SELECT * FROM " . $this->table_name_with_prefix . $this->get_where_condition() );
	}

	public function get_where_condition() {
		$where = "";

		if ( ! is_null( $this->affiliate_id ) ) {
			$where .= empty( $where ) ? " WHERE " : " AND ";
			$where .= " affiliate_id = " . $this->affiliate_id;
		}

		if ( ! is_null( $this->post_id ) ) {
			$where .= empty( $where ) ? " WHERE " : " AND ";
			$where .= " post_id = " . $this->post_id;
		}

		if ( ! is_null( $this->status ) ) {
			$where .= empty( $where ) ? " WHERE " : " AND ";
			$where .= " status = '" . $this->status . "'";
		}

		if ( ! is_null( $this->post_author ) ) {
			$where .= empty( $where ) ? " WHERE " : " AND ";
			$where .= " post_id in(SELECT ID FROM " . $this->wpdb->posts . " WHERE post_author = " . $this->post_author . ")";
		}

		return $where;
	}

	public function insert( $data ) {
		$this->wpdb->insert( $this->table_name_with_prefix, $data );
	}

	public function update( $data, $where ) {
		$this->wpdb->update( $this->table_name_with_prefix, $data, $where );
	}

	public function is_exists() {
		$row = $this->wpdb->get_row( "SELECT * FROM " . $this->table_name_with_prefix . $this->get_where_condition() );

		return is_null( $row ) ? false : true;
	}
}