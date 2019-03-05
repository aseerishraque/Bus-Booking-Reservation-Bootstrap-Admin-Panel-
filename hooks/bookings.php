<?php
	// For help on using hooks, please refer to https://bigprof.com/appgini/help/working-with-generated-web-database-application/hooks

	function bookings_init(&$options, $memberInfo, &$args){

		return TRUE;
	}

	function bookings_header($contentType, $memberInfo, &$args){
		$header='';

		switch($contentType){
			case 'tableview':
				$header='';
				break;

			case 'detailview':
				$header='';
				break;

			case 'tableview+detailview':
				$header='';
				break;

			case 'print-tableview':
				$header='';
				break;

			case 'print-detailview':
				$header='';
				break;

			case 'filters':
				$header='';
				break;
		}

		return $header;
	}

	function bookings_footer($contentType, $memberInfo, &$args){
		$footer='';

		switch($contentType){
			case 'tableview':
				$footer='';
				break;

			case 'detailview':
				$footer='';
				break;

			case 'tableview+detailview':
				$footer='';
				break;

			case 'print-tableview':
				$footer='';
				break;

			case 'print-detailview':
				$footer='';
				break;

			case 'filters':
				$footer='';
				break;
		}

		return $footer;
	}

	function bookings_before_insert(&$data, $memberInfo, &$args){
		$currentuser=($memberInfo['username']);//logged in member
		if ($currentuser<>"admin") {
			# code...execute the following code if user is not admin
			#code..check if the member has submited correct personal details
		$MIM=sqlValue("SELECT pkValue FROM membership_userrecords WHERE tableName='customers' AND memberID='$currentuser'");//get members original id in members table.
		$getactualidno=sqlValue("SELECT id_number FROM customers WHERE id='$MIM'");//get members true id_no using their unique id
		$chosenidno=($data['id_number']);//get the selected id_no
		$actualchosenidno=sqlValue("SELECT id_number FROM customers WHERE id='$chosenidno'");//get actual chosen id_no from customers table
		if ($actualchosenidno==$getactualidno) {
			# code...if submitted id number is correct do a second check
			$busid=($data['bus']);
			$seatid=($data['seat']);
			#check if the selected seat in the bus is already booked or not
			$countquery=sqlValue("SELECT COUNT(*) FROM bookings WHERE bus='$busid' AND seat='$seatid' ORDER BY id");
		if ($countquery>0) {
			# code...if seat is booked display error message
			$_SESSION['custom_err_msg']="<b>Sorry,it seems the seat you have selected has alredy been booked in that bus.Please Select another seat</b>";
			return FALSE;
		}
		#code..check for double booking for same customer/bus/date/time
		$customerid=($data['id_number']);
		$busid=($data['bus']);
		$checkvalues=sqlValue("SELECT COUNT(*) FROM bookings WHERE id_number='$customerid' AND bus='$busid'");
		if ($checkvalues>0) {
			# code...
			# code...if there is double bookinh show error
			$_SESSION['custom_err_msg']="<b>Sorry,it seems you are doing double booking for the same customer,date,bus and departure time,please check and try again</b>";
			return FALSE;
		}
		else{
			#if seat is not booked then save the record
			return TRUE;}
		}
		else{
			#code show error message if the submitted details are conflicting
			$_SESSION['custom_err_msg']="<b>Sorry!! The personal information you have entered seems to belong to someone else,Please check and try again</b>";
			return FALSE;
		}
		}
		else{
			#return true for admin user
			return TRUE;
		}
		
	}

	function bookings_after_insert($data, $memberInfo, &$args){

		return TRUE;
	}

	function bookings_before_update(&$data, $memberInfo, &$args){

		return TRUE;
	}

	function bookings_after_update($data, $memberInfo, &$args){

		return TRUE;
	}

	function bookings_before_delete($selectedID, &$skipChecks, $memberInfo, &$args){

		return TRUE;
	}

	function bookings_after_delete($selectedID, $memberInfo, &$args){

	}

	function bookings_dv($selectedID, $memberInfo, &$html, &$args){

	}

	function bookings_csv($query, $memberInfo, &$args){

		return $query;
	}
	function bookings_batch_actions(&$args){

		return array();
	}
