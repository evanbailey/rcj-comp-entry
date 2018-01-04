<?php
  error_reporting(E_ALL); ini_set('display_errors', '1');
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
   
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Record Invoice Payments');

  echo '<h1>Record Invoice Payments</h1>';
  

 $action                = postFieldDefault('action'); 
 $uid_comp_name         = postFieldDefault('uid_comp_name');


  if($action == ''):
    $sql = 'SELECT 
	          uid_comp_name AS uid, 
			  concat(year, " - ", state, " - ", comp_name) AS display 
	        FROM 
			  v_comp_name 
			WHERE 
			  year = YEAR(NOW()) 
			  OR 
			  year = YEAR(NOW())-1 
			ORDER BY 
			  year DESC, 
			  state ASC, 
			  event_date ASC';

	  CEWriteFormStart('Select Competition', 'select_comp', '/payment-records/');
      CEWriteFormAction('display');
	  CEWriteFormFieldDropDown('uid_comp_name', 'Competition', $uid_comp_name, $con, $sql, 'Select a Competition');

	  echo '  <input type="submit" value="View Payments">';
	  echo '</fieldset>';
	  echo '</form>';   
	  CEWritePageEnd();
    
  elseif($action == 'display' OR $action=='update'):
	//update if commanded to do so
	if($action == 'update'):
		foreach(array_keys($_POST) as $key):
			if($key!='action' AND $key!='uid_comp_name'):
				$stmt = $con->prepare('UPDATE mentor_team SET pmt_amount = :pmt_amount, pmt_notes = :pmt_notes WHERE uid = :key');
				$stmt->bindParam(':key', $key);
				$stmt->bindParam(':pmt_amount', $_POST[$key]['pmt_amount']);
				$stmt->bindParam(':pmt_notes', $_POST[$key]['pmt_notes']);
				$stmt->execute();
			endif;
		endforeach;
	endif;

	//then show form
    $formname = 'Update Payment Information';
    $formaction = '/payment-records/';
    echo '<form name="'. $formname . '" action="' . $formaction . '" method="post">';
    CEWriteFormAction('update');
    CEWriteFormFieldHidden('uid_comp_name', $uid_comp_name);

	$comp_name = $con->query('SELECT concat(year, " - ", state, " - ", comp_name) AS comp_name FROM v_comp_name WHERE uid_comp_name = "' . $uid_comp_name . '"')->fetchColumn();
    $fee = $con->query('SELECT entry_fee FROM comp_name WHERE uid = "' . $uid_comp_name . '"')->fetchColumn();
    $total_teams = $con->query('SELECT count(uid) AS count_teams FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '"')->fetchColumn();
    
    $total_received = $con->query('SELECT sum(pmt_amount) FROM v_invoice_payment WHERE uid_comp_name = "' . $uid_comp_name . '"')->fetchColumn();
    $total_due = $total_teams * $fee;
    $total_owing = $total_due - $total_received;

    echo 'Displaying payment information for ' . $comp_name . '<br/>';
    echo 'Number of Teams: ' . $total_teams . '<br/>';
    echo 'Total Amount Due: ' . number_format($total_due, 2) . '<br/>';
    echo 'Total Amount Paid: ' . number_format($total_received, 2) . '<br/>'; 
    echo 'Total Amount Unpaid: ' . number_format($total_owing, 2) . '<br/><br/>';
	echo '  <input type="submit" value="Record Payments Entered">';
    echo '<br/>';echo '<br/>';
    //get fee per team for comp


    echo "<table border='1'>
	  <tr>
		  <th>Organisation</th>
		  <th>Name</th>
		  <th>Email</th>
		  <th>Qty Teams</th>
		  <th>Amount Due</th>
		  <th>Viewed Invoice?</th>
		  <th>Amount Paid</th>
		  <th>Notes</th>
	  </tr>";
      $sql = 'SELECT 
	  		   uid_mentor_team,
	    	   organisation, 
	           first_name, 
			   last_name, 
			   email, 
			   pmt_notes, 
			   pmt_amount, 
			   IF(ISNULL(invoice_number), "Unviewed", "Viewed") as invoice 
			 FROM 
			   v_invoice_payment 
			 WHERE 
			   uid_comp_name = "' . $uid_comp_name . '" 
			 ORDER BY
			   organisation ASC,
			   last_name ASC,
			   first_name ASC';
      foreach($con->query($sql) as $row)
      	{
			$num_teams = $con->query('SELECT COUNT(uid) AS num_teams FROM team WHERE uid_mentor_team ="' . $row['uid_mentor_team'] . '"')->fetchColumn();
			
			if($num_teams<1):
				continue;
			endif;
			
			$amt_due = $fee * $num_teams;
			
			echo '<tr>';
				echo '<td>' . htmlspecialchars($row['organisation']) . '</td>';
				echo '<td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
				echo '<td>' . htmlspecialchars($row['email']) . '</td>';  
				echo '<td>' . htmlspecialchars($num_teams) . '</td>';
				echo '<td>' . number_format(htmlspecialchars($amt_due), 2) . '</td>';
				echo '<td>' . htmlspecialchars($row['invoice']) . '</td>';
				echo '<td><input type="number" step="0.01" size="9" name="' . $row['uid_mentor_team'] . '[pmt_amount]" min="0" max="99999" value="' . $row['pmt_amount'] . '"></td>'; 
				echo '<td><input type="text" size="40" name="' . $row['uid_mentor_team'] . '[pmt_notes]" value="' . $row['pmt_notes'] . '"></td>'; 
			echo "</tr>";
	    }
    echo "</table>";
	echo '</form>';   
	CEWritePageEnd();

  endif;

?>




