<?php
	/* Include Requeried files */
	define("PREPEND_PATH", "../");
	$hooks_dir = dirname(__FILE__);
	include("{$hooks_dir}/../defaultLang.php");
	include("{$hooks_dir}/../language.php");
	include("{$hooks_dir}/../lib.php");
	include("{$hooks_dir}/SummaryReport.php");
	
	$x = new StdClass;
	$x->TableTitle = "Availability Report";
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
		1 => 'bus',
		2 => 'route',
		3 => 'amount',
		4 => 'date',
		5 => 'time',
		6 => 'status',
	);	$groups_array = array (
		0 => 'Admins',
	);
	$memberInfo = getMemberInfo();
	if(!in_array(strtolower($memberInfo["group"]), array_map("strtolower", $groups_array))){
		header("Location: ../index.php");
		exit;
	}



	$from = makeSafe($_REQUEST["from"]);
	$to = makeSafe($_REQUEST["to"]);
	
	/* if period 1 not set while 2 is set, shift 2 to 1 */
	if(!isset($_REQUEST["period-one-from"]) && !isset($_REQUEST["period-one-to"])) {
		if(isset($_REQUEST["period-two-from"]))
			$_REQUEST["period-one-from"] = $_REQUEST["period-two-from"];
		if(isset($_REQUEST["period-two-to"]))
			$_REQUEST["period-one-to"] = $_REQUEST["period-two-to"];
		unset($_REQUEST["period-two-from"]);
		unset($_REQUEST["period-two-to"]);
		unset($_REQUEST["comparison-period-2"]);
		$_REQUEST["comparison-period-1"] = 1;
	}

	if(isset($_REQUEST["period-one-from"])){
		$period_one_from = makeSafe($_REQUEST["period-one-from"]);
	}
	if(isset($_REQUEST["period-one-to"])){
		$period_one_to = makeSafe($_REQUEST["period-one-to"]);
	}
	
	if(isset($_REQUEST["period-two-from"])){
		$period_two_from = makeSafe($_REQUEST["period-two-from"]);
	}
	if(isset($_REQUEST["period-two-to"])){
		$period_two_to = makeSafe($_REQUEST["period-two-to"]);
	}
	
	if(!isset($_REQUEST["apply"])){
		$from = date("m-d-Y", strtotime("first day of this month"));
		$to = date("m-d-Y", strtotime("this day"));
		$period_one_from = date("m-d-Y", strtotime("first day of previous month"));
		$period_one_to = date("m-d-Y", strtotime("this day last month"));
		$period_two_from = date("m-d-Y", strtotime("first day of this month last year"));
		$period_two_to = date("m-d-Y", strtotime("this day last year"));
		$comparison_period_one = "";
		$_REQUEST["comparison-period-1"] = "";
		$_REQUEST["comparison-period-2"] = "";
	
	}
	
	$config_array = array(
		'title' => 'Availability Report',
		'table' => 'availability',
		'label' => 'bus',
		'group_function' => 'count',
		'caption1' => 'Bus',
		'caption2' => 'Count of Availability',
		'date_format' => 'm-d-Y',
		'date_separator' => '/',
		'order_by' => $order_by,
		'sorting_order' => $sorting_order,
		'label_field_index' =>'2',
		'filterable_fields' =>$filterable_fields,
		'date_field' =>'date',
		'start_date' =>$from,
		'end_date' =>$to,
		'period-one-from'=>$period_one_from,
		'period-one-to'=>$period_one_to,
		'period-two-from'=>$period_two_from,
		'period-two-to'=>$period_two_to,
		'date_field_index' =>'5',
		'look_up_table' =>'buses',
		'look_up_value' =>'number',
		'parent_caption_field' =>'number',
		'parent_caption_field2' =>'',
		'parent_caption_separator' =>''
	);
	$new_report = new SummaryReport($config_array);
	$new_report->render_report_title();
	$new_report->add_report_configuration("mm-dd-yyyy");
	$new_report->render_report("m-d-Y");

	include_once("{$hooks_dir}/../footer.php");
