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
include "helpers.php";

$affiliateWPApprovalLogTable = new \Yogasukmap\AffiliateWPApproval\Database\AffiliateWPRequestLogTable();
$affiliateWPApprovalLogTable->install();

