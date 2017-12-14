<?php

namespace Yogasukmap\AffiliateWPApproval\Supports;

trait TableCreator {
	public function install() {
		if ( is_null( $this->table_name ) || $this->is_table_exists( $this->table_name_with_prefix ) ) {
			return false;
		}

		require_once( ABSPATH . "wp-admin/includes/upgrade.php" );
		dbDelta( $this->create_table_sql() );
	}

	public function is_table_exists( $table = null ) {
		if ( is_null( $table ) ) {
			$table = $this->table_name_with_prefix;
		}

		if ( $this->wpdb->get_var( "SHOW TABLES LIKE'" . $table . "'" ) != $table ) {
			return false;
		}

		return true;
	}
}