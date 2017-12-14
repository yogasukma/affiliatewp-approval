<?php

namespace Yogasukmap\AffiliateWPApproval\Database;

use Yogasukmap\AffiliateWPApproval\Supports\TableCreator;
use Yogasukmap\AffiliateWPApproval\Supports\BasicCrud;

class AffiliateWPRequestLogTable {

	use TableCreator;
	use BasicCrud;

	protected $wpdb;
	protected $table_name = "affiliate_wp_approval_logs";
	protected $table_name_with_prefix;
	protected $user_id;
	protected $post_author;
	protected $post_id;
	protected $affiliate_id;
	protected $status;

	public function __construct() {
		global $wpdb;

		$this->wpdb                   = $wpdb;
		$this->table_name_with_prefix = $this->wpdb->prefix . $this->table_name;
	}

	public function create_table_sql() {
		$charset_collate = $this->wpdb->get_charset_collate();

		$sql = "CREATE TABLE " . $this->table_name_with_prefix . "(
			id INT NOT NULL AUTO_INCREMENT,
			post_id INT NOT NULL,
			affiliate_id INT NOT NULL,
			status VARCHAR(20),
			type VARCHAR(20),
			requested_by INT,
			requested_at DATETIME,
			approved_at DATETIME,
			rejected_at DATETIME,
			revoked_at DATETIME,
			deleted_at DATETIME,
			PRIMARY KEY(id)
			) $charset_collate 
		";

		return $sql;
	}
}