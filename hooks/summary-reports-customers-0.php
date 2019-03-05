<?php
	/* Include Requeried files */
	define("PREPEND_PATH", "../");
	$hooks_dir = dirname(__FILE__);
	include("{$hooks_dir}/../defaultLang.php");
	include("{$hooks_dir}/../language.php");
	include("{$hooks_dir}/../lib.php");
	include("{$hooks_dir}/SummaryReport.php");
	
	$x = new StdClass;
	$x->TableTitle = "Customers Report";
	include_once("{$hooks_dir}/../header.php");
	
	if($_REQUEST["row_numbers"] != 1) $_REQUEST["row_numbers"] = 0;
	if(!in_array($_REQUEST["label_align"], array("center", "left"))) $_REQUEST["label_align"] = "right";
	if(!in_array($_REQUEST["value_align"], array("center", "left"))) $_REQUEST["value_align"] = "right";

	$order_by = "label";
	if(isset($_REQUEST["order-by"])){
		$order_by = makeSafe($_REQUEST["order-by"]);
	}
	if(isset($_REQUEST["sorting-order"])){
		$sorting_order = makeSafe($_REQUEST["sorting-order"]);
	}

$filterable_fields = array (
		0 => 'id',
		1 => 'fullname',
		2 => 'phone',
		3 => 'id_number',
	);	$groups_array = array (
		0 => 'Admins',
	);
	$memberInfo = getMemberInfo();
	if(!in_array(strtolower($memberInfo["group"]), array_map("strtolower", $groups_array))){
		header("Location: ../index.php");
		exit;
	}


	$config_array = array(
		'title' => 'Customers Report',
		'table' => 'customers',
		'label' => 'fullname',
		'group_function' => 'count',
		'caption1' => 'Fullname',
		'caption2' => 'Count of Customers',
		'date_format' => 'm-d-Y',
		'date_separator' => '/',
		'order_by' => $order_by,
		'sorting_order' => $sorting_order,
		'label_field_index' =>'2',
		'filterable_fields' =>$filterable_fields
	);
	$new_report = new SummaryReport($config_array);
	$new_report->render_report_title();
	$new_report->add_report_configuration("mm-dd-yyyy");
	$new_report->render_report("m-d-Y");

	include_once("{$hooks_dir}/../footer.php");
