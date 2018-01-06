<?php

  function SoccerGroupCalc1($num_teams, $group_size) {
	  $num_groups = $num_teams/$group_size;
	  $games_per_group = ($group_size*($group_size-1))/2;
	  $total_games = $num_groups * $games_per_group;  
		  
	  return $num_groups . ' Groups<br/>' . $games_per_group .' Games per Group<br/>' . $total_games . ' Games in Total';
	  
	  
  }
  
  error_reporting(E_ALL); ini_set('display_errors', '1');
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/user/user-shared.php';

  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
   
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Soccer Scheduling Assistant');

  echo '<h1>Soccer Scheduling Assistant</h1>';
  

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

	  CEWriteFormStart('Select Competition', 'select_comp', '/scheduling/soccer/');
      CEWriteFormAction('display');
	  CEWriteFormFieldDropDown('uid_comp_name', 'Competition', $uid_comp_name, $con, $sql, 'Select a Competition');

	  echo '  <input type="submit" value="Continue">';
	  echo '</fieldset>';
	  echo '</form>';   
	  CEWritePageEnd();
    
  elseif($action == 'display' OR $action=='update'):

	//update if commanded to do so
	if($action == 'update'):
		foreach(array_keys($_POST) as $key):
			if($key!='action' AND $key!='uid_comp_name'):
				$stmt = $con->prepare('UPDATE mentor_team SET pmt_amount = :pmt_amount, pmt_notes = :pmt_notes WHERE uid = :key');
				$stmt = $con->prepare('UPDATE mentor_team SET pmt_amount = :pmt_amount, pmt_notes = :pmt_notes, pmt_ok = :pmt_ok WHERE uid = :key');
				$stmt->bindParam(':key', $key);
				$stmt->bindParam(':pmt_amount', $_POST[$key]['pmt_amount']);
				$stmt->bindParam(':pmt_notes', $_POST[$key]['pmt_notes']);
				if(isset($_POST[$key]['pmt_ok'])):
					$a = 1;
					$stmt->bindParam(':pmt_ok', $a);
				else: 
					$a = 0;
					$stmt->bindParam(':pmt_ok', $a);
				endif;
				$stmt->execute();
			endif;
		endforeach;
	endif;

		$formname = 'Soccer Scheduling Assistant';
		$formaction = '/scheduling/soccer/';
		echo '<form name="'. $formname . '" action="' . $formaction . '" method="post">';
		CEWriteFormAction('update');
		CEWriteFormFieldHidden('uid_comp_name', $uid_comp_name);


		//table of counts of team by division
		$sql = 'SELECT DISTINCT(div_name) AS div_name, COUNT(uid) AS count FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Soccer%" GROUP BY div_name ORDER BY div_disp_order ASC';
		$soccer_divisions = $con->query($sql);
		
		echo '<p>';
		echo "<table border='1'>
	  		<tr>
			  <th>Division</th>
			  <th>Qty Teams</th>
			  <th>Groups of 2</th>
			  <th>Groups of 3</th>
			  <th>Groups of 4</th>
			  <th>Groups of 5</th>
			  <th>Groups of 6</th>
			  <th>Groups of 7</th>
			  <th>Groups of 8</th>
		  	</tr>";
		
		foreach($soccer_divisions as $soccer_division):
			echo '<tr>';
				echo '<td>' . $soccer_division['div_name'] . '</td>';
				echo '<td>' . $soccer_division['count'] . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 2) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 3) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 4) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 5) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 6) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 7) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 8) . '</td>';
			echo '</tr>';
		endforeach;
		echo '</table>';
		echo '</p>';

		//table to specify number of groups for automatic allocation
		$sql = 'SELECT DISTINCT(div_name) AS div_name, uid_division FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Soccer%" GROUP BY div_name ORDER BY div_disp_order ASC';
		$soccer_divisions = $con->query($sql);
		
		echo '<p>';
		echo "<table border='1'>
	  		<tr>
			  <th>Division</th>
			  <th>Enter Num Groups</th>
		  	</tr>";
		
		foreach($soccer_divisions as $soccer_division):
			$groups_in_div = $con->query('SELECT COUNT(soccer_group) FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name = "' . $soccer_division['div_name'] . '" GROUP BY div_name')->fetchColumn();
			echo '<tr>';
				echo '<td>' . $soccer_division['div_name'] . '</td>';
				echo '<td><input type="number" step="1" size="2" name="' . $soccer_division['uid_division'] . '" min="1" max="99" value="' . $groups_in_div . '"></td>'; 			
			echo '</tr>';
		endforeach;
		
		echo '<tr>';
			echo '<td colspan="2"><center><button type="submit" name="submit" value="auto_allocate">Auto Allocate</button></center></td>';
		echo '</tr>';

		echo '</table>';
		echo '</p>';


		//table of counts of team by group
		$sql = 'SELECT DISTINCT(IF(ISNULL(soccer_group), "Unallocated", soccer_group)) AS soccer_group, COUNT(uid) AS count, div_name FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Soccer%" GROUP BY soccer_group, div_name ORDER BY div_disp_order ASC, soccer_group ASC';
		//echo $sql;
		$soccer_divisions = $con->query($sql);
		
		echo '<p>';
		echo "<table border='1'>
	  		<tr>
			  <th>Soccer Group</th>
			  <th>Division</th>
			  <th>Qty Teams in Group</th>
		  	</tr>";
		
		foreach($soccer_divisions as $soccer_division):
			echo '<tr>';
				echo '<td>' . $soccer_division['soccer_group'] . '</td>';
				echo '<td>' . $soccer_division['div_name'] . '</td>';
				echo '<td>' . $soccer_division['count'] . '</td>';
			echo '</tr>';
		endforeach;		

		echo "</table>";
		echo '</p>';

		
		$soccer_teams = $con->query('SELECT team_name, div_name, organisation, concat(mentor_first_name, " ", mentor_last_name) AS mentor_name, mentor_email, IF(ISNULL(soccer_group), "Unallocated", soccer_group) AS soccer_group FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Soccer%" ORDER BY soccer_group ASC, div_disp_order ASC, team_name ASC');
		echo '<p>';
		echo "<table border='1'>
		  <tr>
			  <th>Team Name</th>
			  <th>Division</th>
			  <th>Organisation</th>
			  <th>Mentor Name</th>
			  <th>Mentor Email</th>
			  <th>Group</th>
			  <th>Update</th>
		  </tr>";

		foreach($soccer_teams as $soccer_team):
			echo '<tr>';
				echo '<td>' . $soccer_team['team_name'] . '</td>';
				echo '<td>' . $soccer_team['div_name'] . '</td>';
				echo '<td>' . $soccer_team['organisation'] . '</td>';
				echo '<td>' . $soccer_team['mentor_name'] . '</td>';
				echo '<td>' . $soccer_team['mentor_email'] . '</td>';
				echo '<td>' . $soccer_team['soccer_group'] . '</td>';
				echo '<td>Update All Allocations</td>';
			echo '</tr>';
		endforeach;
		
		echo "</table>";
		echo '</p>';
		echo '</form>'; 
	CEWritePageEnd();
 	endif;




	/*

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
		  <th>Invoice Num</th>
		  <th>Amount Paid</th>
		  <th>Check</th>
		  <th>Notes</th>
		  <th>TL</th>
	  </tr>";
      $sql = 'SELECT 
	  		   uid_mentor_team,
	    	   organisation, 
	           first_name, 
			   last_name, 
			   email, 
			   pmt_notes, 
			   pmt_amount, 
			   IF(ISNULL(invoice_number), "Unviewed", invoice_number) as invoice,
			   pmt_ok
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
   			    echo '<td><input type="checkbox" class="smallcheckbox" name="' . $row['uid_mentor_team'] . '[pmt_ok]"'; if (ceIntToBool($row['pmt_ok'])){echo ' checked="checked" ';} echo '></td>';
				//echo '<td>' . CEWriteFormFieldCheckbox($row['uid_mentor_team'] . ['pmt_ok'] , '', ceIntToBool($row['pmt_ok']));
				echo '<td><input type="text" size="40" name="' . $row['uid_mentor_team'] . '[pmt_notes]" value="' . $row['pmt_notes'] . '"></td>';
				echo'<td>'; 
					if($row['pmt_ok']==1): 
						echo '<span style="color: green">Paid</span>'; 
					else: 
						echo '<span style="color: red">Unpaid</span>'; 
					endif;
				echo '</td>';
			echo "</tr>";
	    }
    echo "</table>";  
*/
?>




