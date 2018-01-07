<?php

  function SoccerGroupCalc1($num_teams, $num_groups) {
	  $teams_per_group = floor($num_teams/$num_groups);
	  
	  $games_per_group = ($teams_per_group*($teams_per_group-1))/2;
	  $total_games = $num_groups * $games_per_group;  
	  
	  if($teams_per_group < 1 OR $games_per_group < 1):
	      return '';
	  else:
	  	return $teams_per_group . ' Teams and ' . $games_per_group . ' Games per Group<br/>' . $total_games . ' Games in Total<br/>' . ($num_teams - ($num_groups * $teams_per_group)) . ' Teams Unallocated';
	  endif;
	  
	  
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
		//handles automatic allocation
		if($_POST['submit'] == 'auto_allocate'):
			echo 'auto alloc time!';
			$group_number = 1;
			foreach(array_keys($_POST['auto_alloc_array']) as $current_division):
				//empty out groups first
				$update = $con->query('UPDATE team SET soccer_group = NULL WHERE uid_comp_division = "' . $current_division .  '"');
				
				$teams_in_div = $con->query('SELECT COUNT(uid) FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND uid_division = "' . $current_division . '"')->fetchColumn();
				$teams_per_group = floor($teams_in_div/$_POST['auto_alloc_array'][$current_division]);
				$sql = 'SELECT uid FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND uid_division = "' . $current_division . '" ORDER BY RAND() LIMIT ' . $teams_per_group*$_POST['auto_alloc_array'][$current_division];
				$teams_in_div = $con->query($sql);
				
				$counter = 0;
				foreach($teams_in_div as $team):
					$update = $con->query('UPDATE team SET soccer_group = "' . $group_number .  '" WHERE uid = "' . $team['uid'] .  '"');
					$counter ++;
					if($counter == $teams_per_group):
						$group_number ++;
						$counter = 0;
					endif;
				endforeach;
			endforeach;
		endif;
		
		//handles changing allocations
		if($_POST['submit'] == 'reallocate'):
        	echo 'realloc time!';
			foreach(array_keys($_POST['realloc_array']) as $current_team):
				$update = $con->query('UPDATE team SET soccer_group = "' . $_POST['realloc_array'][$current_team] .  '" WHERE uid = "' . $current_team . '"');
			endforeach;
		endif;
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
			  <th>1 Group</th>
			  <th>2 Groups</th>
			  <th>3 Groups</th>
			  <th>4 Groups</th>
			  <th>5 Groups</th>
		  	</tr>";
		
		foreach($soccer_divisions as $soccer_division):
			echo '<tr>';
				echo '<td>' . $soccer_division['div_name'] . '</td>';
				echo '<td>' . $soccer_division['count'] . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 1) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 2) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 3) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 4) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 5) . '</td>';

			echo '</tr>';
		endforeach;


		$sql = 'SELECT DISTINCT(div_name) AS div_name, COUNT(uid) AS count FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Soccer%" GROUP BY div_name ORDER BY div_disp_order ASC';
		$soccer_divisions = $con->query($sql);

		echo "<tr>
			  <th>Division</th>
			  <th>Qty Teams</th>
			  <th>6 Groups</th>
			  <th>7 Groups</th>
			  <th>8 Groups</th>
			  <th>9 Groups</th>
			  <th>10 Groups</th>
		  	</tr>";
		
		foreach($soccer_divisions as $soccer_division):
			echo '<tr>';
				echo '<td>' . $soccer_division['div_name'] . '</td>';
				echo '<td>' . $soccer_division['count'] . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 6) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 7) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 8) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 9) . '</td>';
				echo '<td>' . SoccerGroupCalc1($soccer_division['count'], 10) . '</td>';
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
			$sql = 'SELECT COUNT(DISTINCT(soccer_group)) FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name = "' . $soccer_division['div_name'] . '"';
			$groups_in_div = $con->query($sql)->fetchColumn();
			echo '<tr>';
				echo '<td>' . $soccer_division['div_name'] . '</td>';
				echo '<td><input type="number" step="1" size="2" name="auto_alloc_array[' . $soccer_division['uid_division'] . ']" min="1" max="99" value="' . $groups_in_div . '"></td>'; 			
			echo '</tr>';
		endforeach;
		
		echo '<tr>';
			echo '<td colspan="2"><center><button type="submit" name="submit" value="auto_allocate">Auto Allocate</button></center></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td colspan="2"><center>This will write over any reallocations done below</center></td>';
		echo '</tr>';

		echo '</table>';
		echo '</p>';


		//table of counts of team by group
        // DISTINCT(IF(ISNULL(soccer_group), "Unallocated", soccer_group)) AS
		$sql = 'SELECT soccer_group, COUNT(uid) AS count, div_name FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Soccer%" GROUP BY soccer_group, div_name ORDER BY div_disp_order ASC, soccer_group ASC';
		//echo $sql;
		$soccer_divisions = $con->query($sql);
		
		echo '<p>';
		echo "<table border='1'>
	  		<tr>
			  <th>Soccer Group</th>
			  <th>Division</th>
			  <th>Qty Teams in Group</th>
			  <th>Warning</th>
		  	</tr>";
		
		foreach($soccer_divisions as $soccer_division):
			echo '<tr>';
				echo '<td>'; if($soccer_division['soccer_group']==''): echo 'Unallocated'; else: echo $soccer_division['soccer_group']; endif; echo '</td>';
				echo '<td>' . $soccer_division['div_name'] . '</td>';
				echo '<td>' . $soccer_division['count'] . '</td>';
				$sql = 'SELECT COUNT(DISTINCT(uid_division)) FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND soccer_group = "' . $soccer_division['soccer_group'] . '"';
				$check = $con->query($sql)->fetchColumn();
				
				if($check==0):
					echo '<td><strong>The ' . $soccer_division['div_name'] .  ' division has unallocated teams</strong></td>';
				elseif($check>1):
					echo '<td><strong>This group has teams from multiple divions</strong></td>';
				else:
					echo '<td>All good here</td>';
				endif;
			echo '</tr>';
		endforeach;		

		echo "</table>";
		echo '</p>';

		
		$soccer_teams = $con->query('SELECT uid, team_name, div_name, organisation, concat(mentor_first_name, " ", mentor_last_name) AS mentor_name, mentor_email, soccer_group FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%Soccer%" ORDER BY div_disp_order ASC,  soccer_group ASC, team_name ASC');
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

				//echo '<td>'; if($soccer_team['soccer_group']==''): echo '<strong>Unallocated</strong>'; else: echo $soccer_team['soccer_group']; endif; echo '</td>';
				echo '<td><input type="number" step="1" size="2" name="realloc_array[' . $soccer_team['uid'] . ']" min="1" max="99" value="' . $soccer_team['soccer_group'] . '"></td>'; 			
				echo '<td><button type="submit" name="submit" value="reallocate">Update All Reallocations</button></td>';
			echo '</tr>';
		endforeach;
		
		echo "</table>";
		echo '</p>';
		
		echo '<p>';
		echo '<td><button type="submit" name="submit" value="combinations">Show Combinations</button></td>';
		echo '</p>';

		echo '</form>';
		
		if($action == 'update'):
			if($_POST['submit'] == 'combinations'):		
				echo '<p>';
				echo "<table border='1'>
				  <tr>
					  <th>Group</th>
					  <th>Division</th>
					  <th>Team Name A</th>
					  <th>Organisation A</th>
					  <th>Team Name B</th>
					  <th>Organisation B</th>
				  </tr>";

				$sql = 'SELECT soccer_group FROM v_team WHERE uid_comp_name = "' . $uid_comp_name . '" AND div_name LIKE "%SOCCER%" GROUP BY soccer_group ORDER BY div_disp_order ASC, soccer_group ASC';
				$soccer_groups = $con->query($sql);

				foreach($soccer_groups as $soccer_group):
					$sql = 'SELECT uid from v_team WHERE soccer_group = "' . $soccer_group['soccer_group'] . '" AND uid_comp_name = "' . $uid_comp_name . '"';
					$teams_in_group = $con->query($sql);
					//print_r($teams_in_group);
					$t = array();
					foreach($teams_in_group as $team):
						array_push($t, $team['uid']);
					endforeach;
					
					//adapted from https://stackoverflow.com/questions/19991710/creating-unique-array-pairs-in-php
					$n = $teams_in_group->rowCount();
					$r_temp = $t;
					$r_result = array();
					foreach($t as $r):
						$i = 0;
						while($i < $n-1):
							array_push($r_result, array($r_temp[0],$r_temp[$i+1]));
							$i++;
						endwhile;
						$n--;
						array_shift($r_temp); //Remove the first element since all the pairs are used
					endforeach;
					//end of adapted code
				
					foreach($r_result as $pair):
						$team_a_details = $con->query('SELECT team_name, organisation, div_name FROM v_team WHERE uid = "' . $pair[0] . '"')->fetch();
						$team_b_details = $con->query('SELECT team_name, organisation FROM v_team WHERE uid = "' . $pair[1] . '"')->fetch();
						echo '<tr>';
							echo '<td>' . $soccer_group['soccer_group'] . '</td>';
							echo '<td>' . $team_a_details['div_name'] . '</td>';
							echo '<td>' . $team_a_details['team_name'] . '</td>';
							echo '<td>' . $team_a_details['organisation'] . '</td>';
							echo '<td>' . $team_b_details['team_name'] . '</td>';
							echo '<td>' . $team_b_details['organisation'] . '</td>';
						echo '</tr>';
					endforeach;
				endforeach;

				echo "</table>";
				echo '</p>';
			endif;
		endif;
	CEWritePageEnd();
 	endif;

?>




