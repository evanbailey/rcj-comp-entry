<?php

//  error_reporting(E_ALL); ini_set('display_errors', '1');
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/user/user-shared.php';

  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
   
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Dance Scheduling Assistant');

  echo '<h1>Dance Scheduling Assistant</h1>';
  

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

	  CEWriteFormStart('Select Competition', 'select_comp', '/scheduling/dance/');
      CEWriteFormAction('display');
	  CEWriteFormFieldDropDown('uid_comp_name', 'Competition', $uid_comp_name, $con, $sql, 'Select a Competition');

	  echo '  <input type="submit" value="Continue">';
	  echo '</fieldset>';
	  echo '</form>';   
	  CEWritePageEnd();
    
elseif($action == 'display' OR $action=='update'):

	//update if commanded to do so

	$formname = 'Dance Scheduling Assistant';
	$formaction = '/scheduling/dance/';
	echo '<form name="'. $formname . '" action="' . $formaction . '" method="post">';
	CEWriteFormAction('update');
	CEWriteFormFieldHidden('uid_comp_name', $uid_comp_name);

	//table of counts of team by division
	$sql = 'SELECT DISTINCT(div_name) AS div_name, COUNT(uid) AS count, uid_division FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Dance%" GROUP BY div_name ORDER BY div_disp_order ASC';
	$dance_divisions = $con->query($sql);

	echo '<p>';
	echo "<table border='1'>
		<tr>
		  <th>Division</th>
		  <th>Qty Teams</th>
		  <th>Enter Num Rounds</th>
		</tr>";

	foreach($dance_divisions as $dance_division):
		echo '<tr>';
			echo '<td>' . $dance_division['div_name'] . '</td>';
			echo '<td>' . $dance_division['count'] . '</td>';
			echo '<td><input type="number" step="1" size="2" name="run_array[' . $dance_division['uid_division'] . ']" min="1" max="99" value="' . postFieldDefault('run_array[' . $dance_division['uid_division'] . ']', 2)  . '"></td>'; 			
		echo '</tr>';
	endforeach; 


	echo '<tr>';
		echo '<td colspan="3"><center><button type="submit" name="submit" value="generate">Generate Performances and Interviews</button></center></td>';
	echo '</tr>';

	echo '</table>';
	echo '</p>';


	if($action == 'update'):
		if($_POST['submit'] == 'generate'):		
			echo '<p>';
			echo "<table border='1'>
			  <tr>
				  <th>Division</th>
				  <th>Round Number</th>
				  <th>Organisation</th>
				  <th>Team Name</th>
			  </tr>";

			foreach(range(1, $_POST['run_array'][$dance_division['uid_division']]) as $round_number):
				$sql = 'SELECT DISTINCT(uid_division) AS uid_division, div_name FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Dance%" ORDER BY div_disp_order ASC';
				$dance_divisions = $con->query($sql);
				foreach($dance_divisions as $dance_division):
					$sql = 'SELECT uid, organisation, team_name from v_team WHERE uid_division = "' . $dance_division['uid_division'] . '" AND uid_comp_name = "' . $uid_comp_name . '" ORDER BY SUBSTRING(uid, 13) ASC, uid ASC';
					$teams = $con->query($sql);
					foreach($teams as $team):
						echo '<tr>';
							echo '<td>' . $dance_division['div_name'] . '</td>';
							echo '<td>' . $round_number . '</td>';
							echo '<td>' . $team['organisation'] . '</td>';
							echo '<td>' . $team['team_name'] . '</td>';
						echo '</tr>';
					endforeach;
				endforeach;
			endforeach;

			$sql = 'SELECT DISTINCT(uid_division) AS uid_division, div_name FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Dance%" ORDER BY div_disp_order ASC';
				$dance_divisions = $con->query($sql);
				foreach($dance_divisions as $dance_division):
					$sql = 'SELECT uid, organisation, team_name from v_team WHERE uid_division = "' . $dance_division['uid_division'] . '" AND uid_comp_name = "' . $uid_comp_name . '" ORDER BY organisation ASC, team_name ASC';
					$teams = $con->query($sql);
					foreach($teams as $team):
						echo '<tr>';
							echo '<td>' . $dance_division['div_name'] . '</td>';
							echo '<td>Interview</td>';
							echo '<td>' . $team['organisation'] . '</td>';
							echo '<td>' . $team['team_name'] . '</td>';
						echo '</tr>';
					endforeach;
				endforeach;

			echo "</table>";
			echo '</p>';
		endif;
	endif;
endif;
CEWritePageEnd();

?>




