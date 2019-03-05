<?php 

define('NOLABEL', '{%%NOLABEL%%}'); // a special placeholder for label fields having no value

class SummaryReport{
	protected $precision = 0;
	
	public function __construct($config) {
		$this->title = $config['title'];
		$this->table = $config['table'];
		$this->label = $config['label'];
		$this->group_function = $config['group_function'];
		$this->caption1 = $config['caption1'];
		$this->caption2 = $config['caption2'];
		$this->date_separator = $config['date_separator'];
		$this->formated_date = $config['date_format'];
		$this->configured_date_format = str_replace("-", $this->date_separator, $config['date_format']);
		$this -> filterable_fields = $config['filterable_fields'] ;
		if(isset($config['group_function_field'])) $this->group_function_field = $config['group_function_field'];
		
		if(isset($config['parent_table'])){
			$this->parent_table = $config['parent_table'];
			$this->join_statment = $config['join_statment'];	 
		}else{
			$this->label_field_index = $config['label_field_index'];
		}
		
		if(isset($config['date_field'])&&!isset($config['parent_table'])) $this->date_field_index = $config['date_field_index'];
		if(isset($config['date_field'])){	
			$this->date_field=$config['date_field'];
			if($this->validate_date($config['start_date'])>$this->validate_date($config['end_date'])){
				$temp=$config['start_date'];
				$config['start_date']=$config['end_date'];
				$config['end_date']=$temp;
			}
			if($this->validate_date($config['start_date'],'start')) $this->ts_start_date =$this->validate_date($config['start_date'],'start');
			if($this->validate_date($config['end_date'],'end')) $this->ts_end_date =$this->validate_date($config['end_date'],'end');
		}
		
		$this->show_period_one=false;
		if(isset($config['period-one-from'])&&isset($config['period-one-to'])){
			if($this->validate_date($config['period-one-from'],'period_one_from')) $this->ts_period_one_from =$this->validate_date($config['period-one-from'],'period_one_from');	
			if($this->validate_date($config['period-one-to'],'period_one_to')) $this->ts_period_one_to =$this->validate_date($config['period-one-to'],'period_one_to');			
			$this->show_period_one=true;
		}
			
		$this->show_period_two=false;
		if(isset($config['period-two-from'])&&isset($config['period-two-to'])){	
			if($this->validate_date($config['period-two-from'],'period_two_from')) $this->ts_period_two_from =$this->validate_date($config['period-two-from'],'period_two_from');		
			if($this->validate_date($config['period-two-to'],'period_two_to')) $this->ts_period_two_to =$this->validate_date($config['period-two-to'],'period_two_to');			
			$this->show_period_two=true;
		}
		
		if($config['parent_caption_field'])  $this->parent_caption_field = $config['parent_caption_field'];
		if($config['parent_caption_field2'])	$this->parent_caption_field2 = $config['parent_caption_field2'];
		if($config['parent_caption_separator']) $this->parent_caption_separator = makeSafe( $config['parent_caption_separator'] );
		$this->configured_period_one_from = date($this->configured_date_format,$this->ts_period_one_from);
		$this->configured_period_one_to = date($this->configured_date_format,$this->ts_period_one_to);
		$this->configured_period_two_from = date($this->configured_date_format,$this->ts_period_two_from);
		$this->configured_period_two_to = date($this->configured_date_format,$this->ts_period_two_to);
	 
		if(isset($config['order_by'])) $this->order_by=$config['order_by'];	
		if(isset($config['sorting_order'])) $this->sorting_order=$config['sorting_order'];	
		if(isset($config['look_up_table']))	$this->look_up_table=$config['look_up_table'];	 
		if(isset($config['look_up_value']))	$this->look_up_value=$config['look_up_value'];
		
			
		$this->from = date($this->formated_date,$this->ts_start_date);
		$this->to = date($this->formated_date,$this->ts_end_date);
		
		$this->period_one_from = date($this->formated_date,$this->ts_period_one_from);
		if(!isset($this->ts_period_one_from)) $this->period_one_from 
			= date($this->formated_date, strtotime('first day of previous month'));
			
		$this->period_one_to = date($this->formated_date,$this->ts_period_one_to);
		if(!isset($this->ts_period_one_to)) $this->period_one_to
			= date($this->formated_date, strtotime('this day last month'));
			
		$this->period_two_from = date($this->formated_date,$this->ts_period_two_from);
		if(!isset($this->ts_period_two_from))  $this->period_two_from 
			= date($this->formated_date, strtotime('first day of this month last year'));	
		
		$this->period_two_to = date($this->formated_date,$this->ts_period_two_to);	
		if(!isset($this->ts_period_two_to)) $this->period_two_to 
			= date($this->formated_date, strtotime('this day last year'));
 
		$this->set_precision();
    }
	
	/* get the precision of the aggregate field to use for rounding the aggregate function */
	function set_precision() {
		if(!isset($this->group_function_field) || !isset($this->table)) return;
		$this->precision = sqlValue("SELECT NUMERIC_SCALE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='{$this->table}' AND COLUMN_NAME='{$this->group_function_field}'");
	}
	
	function add_filter($tableName, $filterNo, $filterFieldNo, $filterOperator, $filterValue){
		$filter = urlencode("FilterField[{$filterNo}]") . '=' . urlencode($filterFieldNo) . '&' .
			urlencode("FilterOperator[{$filterNo}]") . '=' . urlencode($filterOperator) . '&' .
			urlencode("FilterValue[{$filterNo}]") . '=' . urlencode($filterValue);

		if(isset($tableName)) return "{$tableName}_view.php?{$filter}"; 

		return $filter;
	}

	function get_report() {
		$ids = $items = array();
		$table_key = getPKFieldName($this->table);
	 	
		/* Get Dates */
		$comparison_periods = array();
		if(isset($this->date_field)){
			$comparison_periods[] = array(
				date('Y-m-d', $this->ts_start_date),
				date('Y-m-d', $this->ts_end_date)
			);
			
			if(isset($this->ts_period_one_from) && isset($this->ts_period_one_to)){
				$comparison_periods[] = array(
					date('Y-m-d',$this->ts_period_one_from),
					date('Y-m-d',$this->ts_period_one_to)
				);
			}
			
			if(isset($this->ts_period_two_from) && isset($this->ts_period_two_to)){
				$comparison_periods[] = array(
					date('Y-m-d',$this->ts_period_two_from),
					date('Y-m-d',$this->ts_period_two_to)
				);
			}
		}else{
			$comparison_periods[] = ''; // just any dummy value to enter the loop below
		}
		
		/* Getting the parent key */
		$parent_table_pk = '';
		if(isset($this->parent_table)) $parent_table_pk = getPKFieldName($this->parent_table);
	
		/* Execute query over dates and merge results */
		$i = 0;

		foreach($comparison_periods as $period){
			$i++;
			$aggregated_field = "`{$this->table}`.`{$this->group_function_field}`";
			$sorting_order = 'asc';
			$date_conditon = '1=1';
			
			if(isset($this->sorting_order)) $sorting_order = $this->sorting_order;
			if($this->group_function == 'count') $aggregated_field = '1';		
			if(isset($this->date_field)) $date_conditon = $this->date_field . " between '{$period[0]}' and '{$period[1]}'";
			
			if($this->parent_table){
				$query = "select 
							`{$this->parent_table}`.`{$parent_table_pk}` as id,
							`{$this->parent_table}`.`{$this->label}` as label,
							round({$this->group_function}({$aggregated_field}), {$this->precision}) as value 
						from {$this->join_statment}
						and {$date_conditon} 
						group by label 
						order by {$this->order_by} {$sorting_order}";
			}else{
				$query = "select 
							{$table_key} as id,
							{$this->label} as label,
							round({$this->group_function}({$aggregated_field}), {$this->precision}) as value
						from {$this->table} 
						where {$date_conditon}
						group by label 
						order by {$this->order_by} {$sorting_order}" ;
			} 

			$res = sql($query, $r);
			$num_rows = db_num_rows($res);
			while($row = db_fetch_assoc($res)){
				$identifier=$row['label'];
				if(!array_key_exists($identifier, $ids)){
					$items[] = array('label' => $row['label'], 'value' . $i => $row['value']);	
					$ids[$identifier] = count($items) - 1;
				}else{					
					/* get index of the array */
				 	$array_index = $ids[$identifier];
					
					/* push in specific array */
					$items[$array_index]['value' . $i] = $row['value'];
				} 	
			} 
 
		}	
 
		if($this->look_up_table && count($items)){
			$order_look_up_values = '';
			$array_of_keys = array();
			foreach($items as $item){
				$array_of_keys[] = "'" . makeSafe($item['label']) . "'"; 
			}
			$look_up_table_pk = getPKFieldName($this->look_up_table);
			$string_of_keys=implode(",",$array_of_keys);
			
			$parent_caption_field = "''";
			if( $this->parent_caption_field ) $parent_caption_field = $this->parent_caption_field;
			
			$parent_caption_separator = "''";
			if( $this->parent_caption_field2 ) $parent_caption_separator = "'" .$this->parent_caption_separator . "'";
	 		
			$parent_caption_field2 = "''";
			if( $this->parent_caption_field2 ) $parent_caption_field2 = $this->parent_caption_field2;
			
			$query="select {$look_up_table_pk} as id ,concat({$parent_caption_field}, {$parent_caption_separator}, {$parent_caption_field2}) 
				as parentCaption from {$this->look_up_table} where {$look_up_table_pk} 
				in (" . implode(',', $array_of_keys) . ") order by FIELD({$look_up_table_pk}, {$string_of_keys}) ";
 
			$res = sql($query, $r);
			$num_rows = db_num_rows($res);
			while($row = db_fetch_assoc($res)){
				for($i = 0; $i < count($items); $i++){	
					if ( gettype ( $items[$i]["label"] ) == "string" ){
						if (strcasecmp($items[$i]["label"], $row["id"]) == 0) {
							$items[$i]["label"] = $row["parentCaption"];
							break;
						}
					}else{
						if( $items[$i]["label"] == $row["id"] ){
							$items[$i]["label"] = $row["parentCaption"];
							break;
						}
					}
				}	
			}
		}
		
		//handling empty labels
		for ( $i=0 ;$i < count( $items ) ; $i++ ){
			if( empty( $items[$i]["label"] ) ){
				$items[$i]["label"] = NOLABEL ;
			}

		}
 
		//sort items by value1
		if($this->order_by == 'value'){
			foreach($items as $key => $item){
				$value1[$key]=$item['value1'];
			}
			$sorting_order=SORT_ASC;
			if( $this->sorting_order == 'desc' ) $sorting_order = SORT_DESC;
			array_multisort($value1,$sorting_order,$items);
		}
		
		
		if($this->order_by == 'label'){
			$label = array();
			foreach($items as $key => $item){
				$label[$key] = $item['label'];
			}
			$sorting_order=SORT_ASC;
			if( $this->sorting_order == 'desc' ) $sorting_order = SORT_DESC;
			array_multisort($label, $sorting_order, $items);
		}

		return $items;
	}
	
	function render_report_title(){
 
		if(isset($this->date_field)) $from_to_title='from '.date($this->configured_date_format,$this->ts_start_date).' to '.date($this->configured_date_format,$this->ts_end_date);
		?>		
	 
		<h1> <?php echo $this->title.' '.$from_to_title ?> 
			<div class="form-group pull-right hidden-print"> 
				<a href="summary-reports.php" class="btn btn-default btn-lg" > <i class="glyphicon glyphicon-chevron-left"></i> Back </a> 
				<button class="btn btn-primary btn-lg" type="button" id="sendToPrinter" onclick="window.print();"> <i class="glyphicon glyphicon-print"></i> Print Report </button>	
			</div>	
			<div class="clearfix"></div>			
		</h1>
		<?php
	}
	
	
	function render_report($title_date_format) {
		$to = date($this->configured_date_format, $this->ts_end_date);
		$from_to_title = '';	
		$items = $this->get_report();
		
		$label_align = "text-{$_REQUEST['label_align']}";
		$value_align = "text-{$_REQUEST['value_align']}";
		
		?>
		
		<div class="pull-right text-bold vspacer-lg"><?php echo count($items); ?> records</div>
		<div class="clearfix"></div>
		
		<div class="table-responsive">
		  <table class="table table-striped table-bordered table-hover">
			<thead>
				<?php $rowspan = (isset($this->date_field) ? 'rowspan="2"' : ''); ?>
			  <tr>
				<?php if($_REQUEST['row_numbers'] == 1){ ?>
					<th <?php echo $rowspan; ?> class="text-center label-row-numbers">#</th>
				<?php } ?>
				<th <?php echo $rowspan; ?> class="text-center label-group-by"><?php echo $this->caption1; ?></th>
				<th <?php echo $rowspan; ?> class="text-center label-summarize"><?php echo $this->caption2; ?></th>	
				<?php if(isset($this->date_field)){ ?>
					<?php if($this->show_period_one){ ?>
						<th colspan="2" class="text-center label-comparison-period-1">
							Period from 
							<?php echo $this->configured_period_one_from; ?>
							to
							<?php echo $this->configured_period_one_to; ?>
						</th>
					<?php } ?> 
					
					<?php if($this->show_period_two){ ?>
						<th colspan="2" class="text-center label-comparison-period-2">
							Period from
							<?php echo $this->configured_period_two_from; ?>
							to
							<?php echo $this->configured_period_two_to; ?>
						</th>
					<?php } ?>
					</tr>
					<tr>
					<?php if($this->show_period_one){ ?>
						<th class="text-center" align="center" valign="middle" ><?php echo $this->caption2 ?></th>
						<th class="text-center" align="center" valign="middle" >Change %</th>
					<?php } ?>

					<?php if($this->show_period_two){ ?>
						<th class="text-center" align="center" valign="middle"><?php echo $this->caption2 ?></th>
						<th class="text-center" align="center" valign="middle">Change %</th>
					<?php } ?>
				<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php
					if(!is_null($items)) foreach($items as $item){
						// set empty comparison values to 0 (formatted to comparison field precision)
						for($i = 1; $i <= 3; $i++) {
							$key = "value{$i}";
							if(!isset($item[$key]))
								$item[$key] = number_format(0, $this->precision);
							${$key} = $item[$key];
						}
						
						if( !isset($this->parent_table) && in_array ($this->label,$this->filterable_fields)){
							
							$filterFieldNo = array_search ( $this->label , $this->filterable_fields)+1;
							$filterValue = $item['label'];
							$filter_flag = true;
						}	
						if( isset($filter_flag) && !isset($this->date_field) && $filterValue != NOLABEL) {
							$value1_filter = '../' . $this->add_filter($this->table, 1, $filterFieldNo, 'equal-to', $filterValue);
							$value1 = "<a href=\"{$value1_filter}\">{$value1}</a>";
						}
						if( isset($filter_flag) && !isset($this->date_field) && $filterValue == NOLABEL){
							$value1_filter = '../' . $this->add_filter($this->table, 1, $filterFieldNo, 'is-empty', "");
							$value1 = "<a href=\"{$value1_filter}\">{$value1}</a>";
						}
						if(isset($filter_flag) && isset($this->date_field)){
			
							$date_field_no=$this->date_field_index;
							$second_filter_value=date($this->configured_date_format,$this->ts_start_date);
							$third_filter_value=date($this->configured_date_format,$this->ts_end_date);
							$label_filter = '../' . $this->add_filter($this->table, 1, $filterFieldNo, 'equal-to', $filterValue);
							
							$value1_filter = $label_filter . '&' . 
								urlencode('FilterAnd[2]') . '=and&' .
								$this->add_filter(null, 2, $date_field_no, 'greater-than-or-equal-to', $second_filter_value) . '&' .
								urlencode('FilterAnd[3]') . '=and&' . 
								$this->add_filter(null, 3, $date_field_no, 'less-than-or-equal-to', $third_filter_value);
						
							$value2_filter = $label_filter . '&' . 
								urlencode('FilterAnd[2]') . '=and&' .
								$this->add_filter(null, 2, $date_field_no, 'greater-than-or-equal-to', $this->configured_period_one_from) . '&' .
								urlencode('FilterAnd[3]') . '=and&' . 
								$this->add_filter(null, 3, $date_field_no, 'less-than-or-equal-to', $this->configured_period_one_to);
							
							$value3_filter = $label_filter . '&' .
								urlencode('FilterAnd[2]') . '=and&' .
								$this->add_filter(null, 2, $date_field_no, 'greater-than-or-equal-to', $this->configured_period_two_from) . '&' .
								urlencode('FilterAnd[3]') . '=and&' . 
								$this->add_filter(null, 3, $date_field_no, 'less-than-or-equal-to', $this->configured_period_two_to);
							
							$value1 = "<a href=\"{$value1_filter}\">{$value1}</a>";
							$value2 = "<a href=\"{$value2_filter}\">{$value2}</a>";
							$value3 = "<a href=\"{$value3_filter}\">{$value3}</a>";
						}
						// handling NOLABEL labels
						if($item["label"] == NOLABEL) $item["label"] = "NONE";
	
						/* Change % for comparison perdiod 1 */
						if($item['value1'] > 0 && $item['value2'] == 0){
							$first_percentage = "&#8734"; /* infinity */
						}elseif($item['value1'] == $item['value2']){
							$first_percentage = "-"; /* no change */
						}else{
							$first_percentage = round((($item['value1'] - $item['value2']) / $item['value2']) * 100, 1) . '%';
						}
						
						/* Change % for comparison perdiod 1 */
						if($item['value1'] > 0 && $item['value3'] == 0){
							$second_percentage = "&#8734"; /* infinity */
						}elseif($item['value1'] == $item['value3']){
							$second_percentage = "-"; /* no change */
						}else{
							$second_percentage = round((($item['value1'] - $item['value3']) / $item['value3']) * 100, 1) . '%';
						}
					?>
					  <tr>

						<?php if($_REQUEST['row_numbers'] == 1){ ?>
							<td class="<?php echo $value_align; ?>"><?php echo ++$row_number; ?></th>
						<?php } ?>
					  
						<td class="<?php echo $label_align; ?>" valign="middle"><?php echo $item['label'] ?></td>
						<td class="<?php echo $value_align; ?>" valign="middle">
							<?php echo  $value1 ?>
						</td>
						<?php if(isset($this->date_field)){
							
						if($this->show_period_one){ ?>
							<td class="<?php echo $value_align; ?>" valign="middle">
								<?php echo  $value2 ?>
							</td>
							<td class="percentage <?php echo $value_align; ?>" valign="middle" ><?php echo $first_percentage?></td>
						<?php }
						if($this->show_period_two){
							?>
							<td class="<?php echo $value_align; ?>" valign="middle">
								<?php echo  $value3 ?>
							</td>
							<td class="percentage <?php echo $value_align; ?>" valign="middle" ><?php echo $second_percentage?></td>
						<?php } ?>
					  </tr>
						<?php }
						
					}?> 
			</tbody>
		  </table>
		</div>
		
		<div class="pull-right text-bold"><?php echo count($items); ?> records</div>
		<div class="clearfix"></div>

	 
		<script>
			$j( function() {	
					for(var i=0 ; i<$j('.percentage').length;i++){
						 var percentage=parseFloat($j('.percentage')[i].innerText);
						if(percentage<0){	
							$j('.percentage')[i].innerText=parseFloat($j('.percentage')[i].innerText)+'%';
							$j('.percentage').eq(i).addClass('text-danger danger');	
						}else if(isNaN(percentage)){
							$j('.percentage').eq(i).css("font-size","1.4em");
							$j('.percentage').eq(i).addClass('text-success success');
							
						}else{
							$j('.percentage').eq(i).addClass('text-success success');
						}
					}
			});
		</script>
		
		<style>
			.label-row-numbers,
			.label-comparison-period-1,
			.label-comparison-period-2,
			.label-group-by,
			.label-summarize {
				vertical-align: middle !important;
			}
		</style>

	<?php	
    }
	
	function add_report_configuration($date_format){
		?>
		<style>
			#from, #to,#period-one-from,#period-one-to,#period-two-from,#period-two-to{ 
				display: inline !important; 
				direction: rtl !important;
			}
			#from, #to,#period-one-from,#period-one-to,#period-two-from,#period-two-to:hover{
				cursor: pointer;
			}
			.datepicker {
			   direction: rtl;
			}
			.datepicker.dropdown-menu {
			   right: initial;
			}
			@media print {
				@page {
				  margin: 15mm;
				}
			}
			.top-space{
				margin-top: 25px;
			}
		</style>
		
		<link rel="stylesheet" href="../resources/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
		<script src="../resources/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
		
		<div class="panel-group hidden-print" id="accordion">
			<div class="panel panel-primary">
			  <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse1" style="cursor: pointer;">
				<h4 class="panel-title">
					<span class="glyphicon glyphicon-cog"></span> Report Configuration
				</h4>
			  </div>  
			  <div id="collapse1" class="panel-collapse collapse">
				<div class="panel-body">
					
					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="get" class="form-inline">
						<?php if($this->date_field){ ?>
							<h4><label><i class="glyphicon glyphicon-calendar text-info"></i> Report Period</label></h4>
							
							<table class="table table-bordered" style="width: calc(100% - 8em); margin: 0 4em;">
								<thead>
									<tr class="active">
										<th class="text-center">
											<div class="btn-group vspacer-md">
												<button type="button" class="btn btn-default" id="current-month">Current month</button>
												<button type="button" class="btn btn-default" id="last-month">Last month</button>
											</div>
											<div class="btn-group vspacer-md">
												<button type="button" class="btn btn-default" id="current-quarter">Current quarter</button>
											</div>
											<div class="btn-group vspacer-md">
												<button type="button" class="btn btn-default" id="current-year">Current year</button>
											</div>
										</th>
										<th class="text-center" style="vertical-align: middle;">
											<div class="checkbox">
												<label>
													<input class="form-check-input" type="checkbox" value="1" id="comparison-period-1" name="comparison-period-1" <?php if(isset($_REQUEST['comparison-period-1'])) echo "checked"; ?> >
													<strong>Comparison Period 1</strong>
												</label>
											</div>
										</th>
										<th class="text-center" style="vertical-align: middle;">
											<div class="checkbox">
												<label>
													<input class="form-check-input" type="checkbox" value="1" id="comparison-period-2" name="comparison-period-2" <?php if(isset($_REQUEST['comparison-period-2'])) echo "checked"; ?> >
													<strong>Comparison Period 2</strong>
												</label>
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td id="report-period-selector" class="text-center">
											<div class="form-group">
												<label for="from" class="vspacer-lg"> From </label>
												<input type="text" class="form-control vspacer-lg" name="from" id="from" value="<?php echo htmlspecialchars($this->from); ?>" size="10">
												<span class="hspacer-lg"></span>
												<label for="to"> To</label>
												<input type="text" class="form-control" name="to" id="to" value="<?php echo htmlspecialchars($this->to) ?>" size="10">
											</div>
										</td>
										
										<td id="period-1-selector" class="text-center">
											<div class="form-group">
												<label for="period-one-from" class="vspacer-lg" id="period-one-from-label"> From </label>
												<input type="text"  class="form-control vspacer-lg" name="period-one-from" id="period-one-from" value="<?php echo htmlspecialchars($this->period_one_from); ?>" size="10">
												<span class="hspacer-lg"></span>
												<label for="period-one-to" id="period-one-to-label"> To</label>
												<input type="text" class="form-control" name="period-one-to" id="period-one-to" value="<?php echo htmlspecialchars($this->period_one_to); ?>" size="10">
											</div>
										</td>
										
										<td id="period-2-selector" class="text-center">
											<div class="form-group">
												<label for="period-two-from" class="vspacer-lg" id="period-two-from-label"> From </label>
												<input type="text"  class="form-control vspacer-lg" name="period-two-from" id="period-two-from" value="<?php echo htmlspecialchars($this->period_two_from); ?>" size="10">
												<span class="hspacer-lg"></span>
												<label for="period-two-to" id="period-two-to-label"> To</label>
												<input type="text" class="form-control" name="period-two-to" id="period-two-to" value="<?php echo htmlspecialchars($this->period_two_to); ?>" size="10">
											</div>
										</td>
									</tr>
								</tbody>
							</table>

						<?php } /* end of period selection */ ?>
						
						<h4><label for="order-by"><i class="glyphicon glyphicon-sort text-info"></i> Order by</label></h4>
						<div class="form-check" style="margin: 0 4em;">
							<div class="radio-inline">
								<label>
									<input type="radio" name="order-by" value="label" <?php echo $this->order_by == 'label' ? 'checked' : ''; ?>>
									<?php echo $this->caption1; ?>
								</label>
							</div>
							<div class="radio-inline">
								<label>
									<input type="radio" name="order-by" value="value" <?php echo $this->order_by == 'value' ? 'checked' : ''; ?>>
									<?php echo $this->caption2; ?>
								</label>
							</div>
							<div class="clearfix"></div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="sorting-order" id="sorting-order" value="desc" <?php echo isset($_REQUEST['sorting-order']) ? 'checked' : ''; ?>>
									<strong class="hspacer-md">Descending</strong>
								</label>
							</div>
						</div>
						
						<h4><label><i class="glyphicon glyphicon-picture text-info"></i> Appearance</label></h4>
						<div style="margin: 0 4em;">
							<table>
								<tbody>
									<tr>
										<th></th>
										<td>
											<div class="checkbox hspacer-lg">
												<label>
													<input type="checkbox" value="1" name="row_numbers" <?php echo $_REQUEST['row_numbers'] == 1 ? 'checked' : ''; ?>>
													<b>Show row numbers</b>
												</label>
											</div>
										</td>
									</tr>
									<tr>
										<th class="text-right">Alignment of <i><?php echo $this->caption1; ?></i> column</th>
										<td>
											<div class="radio-inline hspacer-lg">
												<label title="Left">
													<input type="radio" name="label_align" value="left" <?php echo $_REQUEST['label_align'] == 'left' ? 'checked' : ''; ?>>
													<i class="glyphicon glyphicon-align-left"></i> 
												</label>
											</div>
											<div class="radio-inline hspacer-lg">
												<label title="Center">
													<input type="radio" name="label_align" value="center" <?php echo $_REQUEST['label_align'] == 'center' ? 'checked' : ''; ?>>
													<i class="glyphicon glyphicon-align-center"></i> 
												</label>
											</div>
											<div class="radio-inline hspacer-lg">
												<label title="Right">
													<input type="radio" name="label_align" value="right" <?php echo $_REQUEST['label_align'] == 'right' ? 'checked' : ''; ?>>
													<i class="glyphicon glyphicon-align-right"></i> 
												</label>
											</div>
										</td>
									</tr>
									<tr>
										<th class="text-right">Alignment of other columns</th>
										<td>
											<div class="radio-inline hspacer-lg">
												<label title="Left">
													<input type="radio" name="value_align" value="left" <?php echo $_REQUEST['value_align'] == 'left' ? 'checked' : ''; ?>>
													<i class="glyphicon glyphicon-align-left"></i> 
												</label>
											</div>
											<div class="radio-inline hspacer-lg">
												<label title="Center">
													<input type="radio" name="value_align" value="center" <?php echo $_REQUEST['value_align'] == 'center' ? 'checked' : ''; ?>>
													<i class="glyphicon glyphicon-align-center"></i> 
												</label>
											</div>
											<div class="radio-inline hspacer-lg">
												<label title="Right">
													<input type="radio" name="value_align" value="right" <?php echo $_REQUEST['value_align'] == 'right' ? 'checked' : ''; ?>>
													<i class="glyphicon glyphicon-align-right"></i> 
												</label>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<hr>
						<div class="form-group">
							<button name="apply" class="btn btn-primary vspacer-lg btn-lg" id="apply-filter" > <span class="glyphicon glyphicon-ok"></span> Apply </button>
						</div>
					</form>
				</div>
			  </div>
			</div>
		</div>
		
		
		 <script>
			$j(function(){
				var date_format =<?php echo json_encode($date_format); ?>;
				var date_field=<?php echo json_encode($this->date_field); ?>;
				var start = $j('#from').val();
				
				function setting_date_picker(date_format){
					$j('#from, #to,#period-one-from,#period-one-to,#period-two-from,#period-two-to').datepicker({
					autoclose: true,
					format: date_format,
					todayHighlight: true,
					orientation: 'auto left'
					});
				}
				
				function get_last_day_of_month(year,month){
					var date = new Date(year,month, 0);
					return date.getDate();
				}
				
				function validate_date(day,month,year){
					var minus_date=false;
					var valid_date=[];
					if(month==0){
						month=12;
						minus_date=true;
					}
					if(month==-1){
						month=11;
						minus_date=true;
					}
					if(minus_date&&year==(new Date()).getFullYear()) year=year-1;
					if(month<10) month='0'+month;
					if(day<10)day='0'+day;
					valid_date['day'] = day;
					valid_date['month'] = month;
					valid_date['year'] = year;
					
					return valid_date;
					
				}
				
				function format_date (date_format,day,month,year){
					
					var date=date_format;
					if(day=="last_day") day=get_last_day_of_month(year,month);
					var valid_date=validate_date(day,month,year);
					date=date.replace("dd",valid_date['day']);
					date=date.replace("mm",valid_date['month']);
					date=date.replace("yyyy",valid_date['year']);
 
					return date;
				}
				
				function set_date_fields_values(date_fields_values){
					var date_fields_ids=["from","to","period-one-from","period-one-to","period-two-from","period-two-to"];
					for(var i=0;i<date_fields_ids.length;i++){
						$j("#"+date_fields_ids[i]).val(date_fields_values[i]);
					}
				}
				
				function get_quarter(month,quarter_number){
					var quarter = [];
					if( ( month >= 1 && month <= 3 )|| quarter_number == 1 ){
						quarter["number"]=1;
						quarter[ "start" ] = 1;
						quarter[ "end" ] = 3;
					}
					else if( ( month >= 4 && month <= 6 ) || quarter_number == 2 ){
						quarter["number"]=2;
						quarter[ "start" ] = 4;
						quarter[ "end" ] = 6;
					}
					else if( ( month >= 7 && month <= 9 ) || quarter_number == 3 ){
						quarter[ "number" ]=3;
						quarter[ "start" ] = 7;
						quarter[ "end" ] = 9;
					} 
					else if( ( month >= 10 && month <= 12 ) || quarter_number == 4 ){
						quarter[ "number" ]=4;
						quarter[ "start" ] = 10;
						quarter[ "end" ] = 12;
					} 
					return quarter;
				}
				
				var start_date = new Date(start);
				
				$j( "#current-month" ).click(function(){
					var today = new Date();
					var date_fields_values = [];
					var valid_today_last_month=validate_date(today.getDate(),today.getMonth(),today.getFullYear()); 
					var day_of_today_last_month=today.getDate();
					var day_of_today_last_year=today.getDate();
					var last_day_of_previous_month=get_last_day_of_month(valid_today_last_month['year'],valid_today_last_month['month']);
					var last_day_current_month_last_year=get_last_day_of_month(today.getFullYear()-1,today.getMonth()+1);
					if(today>last_day_of_previous_month){
						day_of_today_last_month=last_day_of_previous_month;
					} 
					if(today>last_day_current_month_last_year){
						day_of_today_last_year=last_day_current_month_last_year;
					} 
					date_fields_values[0] = format_date (date_format,"1",today.getMonth()+1,today.getFullYear());
					date_fields_values[1] = format_date (date_format,today.getDate(),today.getMonth()+1,today.getFullYear());	
					date_fields_values[2] = format_date (date_format,"1",today.getMonth(),today.getFullYear());
					date_fields_values[3] = format_date (date_format,day_of_today_last_month,today.getMonth(),today.getFullYear()),
					date_fields_values[4] = format_date (date_format,"1",today.getMonth()+1,today.getFullYear()-1),
					date_fields_values[5] = format_date (date_format,day_of_today_last_year,today.getMonth()+1,today.getFullYear()-1);
					set_date_fields_values( date_fields_values );
					setting_date_picker( date_format );
					
				});
			
				$j( "#last-month" ).click(function(){
					var today = new Date();
					var date_fields_values = [];
					date_fields_values[0] = format_date (date_format,"1",today.getMonth(),today.getFullYear());
					date_fields_values[1] = format_date (date_format,"last_day",today.getMonth(),today.getFullYear());
					date_fields_values[2] = format_date (date_format,"1",today.getMonth()-1,today.getFullYear());
					date_fields_values[3] = format_date (date_format,"last_day",today.getMonth()-1,today.getFullYear()),
					date_fields_values[4] = format_date (date_format,"1",today.getMonth(),today.getFullYear()-1),
					date_fields_values[5] = format_date (date_format,"last_day",today.getMonth(),today.getFullYear()-1);
					set_date_fields_values( date_fields_values );
					setting_date_picker( date_format );
					
				});
				
				$j( "#current-quarter" ).click(function(){
					var today = new Date();
					var month = today.getMonth();
					var year = today.getFullYear();
					var current_quarter = get_quarter( today.getMonth()+1 );
					var last_quarter_number=current_quarter["number"]-1;
					if(last_quarter_number==0){
						last_quarter_number = 4;
						year = year-1;
					} 
					var last_quarter = get_quarter( "" ,last_quarter_number );		
					var date_fields_values = [];
					date_fields_values[0] = format_date (date_format,"1",current_quarter["start"],today.getFullYear());
					date_fields_values[1] = format_date (date_format,"last_day",current_quarter["end"],today.getFullYear());
					date_fields_values[2] = format_date (date_format,"1",last_quarter["start"],year);
					date_fields_values[3] = format_date (date_format,"last_day",last_quarter["end"],year),
					date_fields_values[4] = format_date (date_format,"1",current_quarter["start"],today.getFullYear()-1),
					date_fields_values[5] = format_date (date_format,"last_day",current_quarter["end"],today.getFullYear()-1);
					set_date_fields_values( date_fields_values );
					setting_date_picker( date_format );
					
				});
	
				$j( "#current-year" ).click(function(){
					var today = new Date();	
					var date_fields_values = [];
					var two_month_ago = "";
					date_fields_values[0] = format_date (date_format,"1","1",today.getFullYear());
					date_fields_values[1] = format_date (date_format,today.getDate(),today.getMonth()+1,today.getFullYear());
					date_fields_values[2] = format_date (date_format,"1","1",today.getFullYear()-1);
					date_fields_values[3] = format_date (date_format,"last_day","12",today.getFullYear()-1),
					date_fields_values[4] = format_date (date_format,"1","1",today.getFullYear()-2),
					date_fields_values[5] = format_date (date_format,"last_day","12",today.getFullYear()-2);
					set_date_fields_values( date_fields_values );
					setting_date_picker( date_format );
					
				});
				
				$j('#comparison-period-1').on('change',function(){
					if($j( this ).attr('checked')){
						$j( this ).attr( 'checked' , false );
						$j( "#period-one-from" ).prop( "disabled", true );
						$j( "#period-one-to" ).prop( "disabled", true );
						$j( "#period-one-from-label" ).addClass( "text-muted" );
						$j( "#period-one-to-label" ).addClass( "text-muted" );
					}else{
						$j( this ).attr( 'checked', true );
						$j( "#period-one-from" ).prop( "disabled", false );
						$j( "#period-one-to" ).prop( "disabled", false );
						$j( "#period-one-from-label" ).removeClass( "text-muted" );
						$j( "#period-one-to-label" ).removeClass( "text-muted" );	
					}	
				});
				
				 
				if(!$j( '#comparison-period-1' ).attr( 'checked' )){

					$j( "#period-one-from" ).prop( "disabled", true );
					$j( "#period-one-to" ).prop( "disabled", true );
					$j( "#period-one-from-label" ).addClass( "text-muted" );
					$j( "#period-one-to-label" ).addClass( "text-muted" );
				}
					 
				if(!$j( '#comparison-period-2' ).attr( 'checked' )){

					$j( "#period-two-from" ).prop( "disabled", true );
					$j( "#period-two-to" ).prop( "disabled", true );
					$j( "#period-two-from-label" ).addClass( "text-muted" );
					$j( "#period-two-to-label" ).addClass( "text-muted" );
				}
 
				$j( '#comparison-period-2' ).on( 'change' ,function(){
				
					if($j( '#comparison-period-2' ).attr( 'checked' )){
						$j( this ).attr( 'checked', false );
						$j( "#period-two-from" ).prop( "disabled", true );
						$j( "#period-two-to" ).prop( "disabled", true );
						$j( "#period-two-from-label" ).addClass( "text-muted" );
						$j( "#period-two-to-label" ).addClass( "text-muted" );
					}else{
						$j( this ).attr( 'checked', true );
						$j( "#period-two-from" ).prop( "disabled", false );
						$j( "#period-two-to" ).prop( "disabled", false );
						$j( "#period-two-from-label" ).removeClass( "text-muted" );
						$j( "#period-two-to-label" ).removeClass( "text-muted" );
					}
				});
				
				$j( '#sorting-order' ).on( 'change' , function(){
					
					if($j( this ).attr( 'checked' )){
						$j( this ).attr( 'checked', false );
					}else{
						$j( this ).attr( 'checked', true );
					}
				})
				
			
 
 				setting_date_picker(date_format);
			}); 
		</script>		
		
	<?php	
		
	}
 	
	function validate_date($date,$type='date'){
		if(!$date)exit(error_message('Not found '. $type .' Date','',false));
		$listed_date_format=str_replace("-","",$this->formated_date);
		$my_sql_date = toMySQLDate($date,'-',$listed_date_format);
		if(strtotime($my_sql_date)<=0) {exit(error_message('Invalid '. $type .' Date','',false));}
		return strtotime($my_sql_date);
	}
 
}