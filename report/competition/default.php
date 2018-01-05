<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';


function UIDCompNameToDescription($con, $uid_comp_name){
  $sql = $con->prepare('select year, state, comp_name FROM v_comp_name where uid_comp_name = :uid_comp_name');
  $sql->bindParam(':uid_comp_name', $uid_comp_name);
  $sql->execute();
  $row            = $sql->fetch(PDO::FETCH_ASSOC);
  $return = htmlspecialchars($row['year'] . ' - ' . $row['state'] . ' - ' . $row['comp_name']);
  if ($row['active'] = '1'){
    $return .= ' (Entries Open)';    
  } else {
    $return .= ' (Entries Closed)';    
  }
  return $return;
}   

function DateTimeAsFileNameString($con){
  $sql = $con->prepare('select now from v_local_time');
  $sql->execute();
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  $return = preg_replace("/\W|_/", "", $row['now']);
  return $return;      
}

function UIDCompNameToFileName($con, $uid_comp_name, $report_name){
  $time = DateTimeAsFileNameString($con);
  $sql = $con->prepare('select active, year, state, comp_name FROM v_comp_name where uid_comp_name = :uid_comp_name');
  $sql->bindParam(':uid_comp_name', $uid_comp_name);
  $sql->execute();
  $row            = $sql->fetch(PDO::FETCH_ASSOC);
  $return = sanitize_file_name(
    $row['year'] . '-' . $row['state'] . '-' . 
    $row['comp_name'] . '-' . $report_name . '-' . $time) . '.csv';
  return $return;
}   

 
function QueryToCSVDownload($con, $sql, $file_name){
  $result = $con->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $num_fields = $result->columnCount();
  $headers = array();
  for ($i = 0; $i < $num_fields; $i++) {
      $col = $result->getColumnMeta($i);
      $headers[] = $col['name'];
  }
  $fp = fopen('php://output', 'w');
  if ($fp && $result) {
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $file_name . '"');
      header('Pragma: no-cache');
      header('Expires: 0');
      fputcsv($fp, $headers);
      foreach($result as $row){
          fputcsv($fp, array_values($row));
      }
  }
 
}
 
function QueryToHTMLTable($con, $sql){
  $result = $con->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $num_fields = $result->columnCount();
  $return = '<table><tr>';
  for ($i = 0; $i < $num_fields; $i++) {
      $col = $result->getColumnMeta($i);
      $col_name = $col['name'];
      $return .= '<th>' . $col_name . '</th>';
  }
  $return .=  '</tr>';
  foreach($result as $row){
    $return .= '<tr>';
    for ($i = 0; $i < $num_fields; $i++) {
        $col = $result->getColumnMeta($i);
        $col_name = $col['name'];
        $return .= '<td>' . htmlspecialchars($row[$col_name]) . '</td>';
    }
    $return .= '</tr>';
  }
  $return .= '</table>';
  return $return;
}    

function QueryToPage($con, $sql, $title, $uid_comp_name, $report_type, $report_format){
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, $title);
  $table = QueryToHTMLTable($con, $sql);
  echo '<script>' .
       '  function do_back(){' .
       '    cePost("/report/competition/", '.
       '           {'.
       '             uid_comp_name: "' . $uid_comp_name . '",' . 
       '             report_type:   "' . $report_type .   '",' . 
       '             report_format: "' . $report_format . '",' . 
       '           });' .
       '  }'.
       '</script>';
  echo '<h1>' . $title . '</h1>';
  echo '<p><a href="/">Home</a> | <a href="javascript:do_back();">Back</a></p>';  
  echo $table;
  CEWritePageEnd('javascript:do_back()');  
}
 
    
function WriteHTML($con, 
  $uid_comp_name, $comp_message, 
  $report_type, $report_type_message, 
  $report_format, $report_format_message){
    
  $sql = 
    'select ' .
    '  uid_comp_name as uid, ' .
    '  concat(year, " - ", state, " - ", comp_name) display ' .
    'from ' .
    '  v_comp_name ' .
    'order by ' .
    '  display';
  
  CEWritePageHeader(C_SITE_TITLE, 'Download Robocup Entry Data');
  WriteConnectUserDetails($con);
  CEWriteFormStart('Download Robocup Entry Data', 'report', '/report/competition/');
  CEWriteFormAction('execute');
  CEWriteFormFieldDropDown('uid_comp_name', 'Competition', $uid_comp_name, $con, $sql, $comp_message);
  CEWriteFormFieldDropDownHardCoded('report_type', 'Report Type', $report_type, 
    array('SUMMARY' => 'Summary', 
          'TEAMS' => 'Team Listing', 
          'STUDENTS' => 'Student Listing', 
          'ORGANISATION' => 'Organisation Listing', 
          'SQL' => 'SQL For Import'),
    $report_type_message);
  CEWriteFormFieldDropDownHardCoded('report_format', 'Report Format', $report_format, 
    array('BROWSER' => 'Display in Browser', 'DOWNLOAD' => 'Download'),
    $report_format_message);
  echo '  <input type="submit" value="Run Report">';
  echo '  <input type="button" value="Cancel" onclick="window.location=\'/\';">';
  echo '</fieldset>';
  echo '</form>';   
  CEWritePageEnd();
}

function IsValid(
  $uid_comp_name, &$comp_message, 
  $report_type, &$report_type_message, 
  $report_format, &$report_format_message){

  $comp_message          = ''; 
  $report_type_message   = '';
  $report_format_message = '';
  
  if (empty($uid_comp_name)){
      $comp_message = 'Please select the competition';
  }

  if (empty($report_type)){
      $report_type_message = 'Please select a report type';
  }

  if (empty($report_format)){
      $report_format_message = 'Please select a report format';
  }
  
  return (empty($comp_message) and empty($report_type_message) and empty($report_format_message));
}

function GetReportDefinition($report_type, $report_format, $uid_comp_name, &$sql, &$title){
  if($report_type == 'SUMMARY'){
    $title = 'Summary';
    $sql = 
      '(select "Team Count by Division" as "Category", "" as "Group", "" as "Count" from dual) ' .
      'union all ' .
      '(select ' .
      '  "", ' .
      '  div_name, ' .
      '  count(*)  ' .
      'from ' .
      '  v_team ' .
      'where ' .
      '  uid_comp_name = "' . $uid_comp_name . '" ' .
      'group by ' .
      '  div_name ' .
      'order by ' .
      '  div_name) ' .
      'union all ' .
      '(select "", "Total", count(*) from v_team where uid_comp_name = "' . $uid_comp_name . '") ' .
      'union all ' .
      '(select "Student Count by Division", "", "" from dual) ' .
      'union all ' .
      '(select ' . 
      '  "", ' .
      '  div_name, ' .
      '  count(*) ' .
      'from ' .
      '  v_team_member ' .
      'where ' .
      '  uid_comp_name = "' . $uid_comp_name . '" ' .
      'group by ' .
      '  div_name ' .
      'order by ' .
      '  "div_disp_order") ' .
      'union all ' .
      '(select "", "Total", count(*) from v_team_member where uid_comp_name = "' . $uid_comp_name . '") ' .
      'union all ' .
      '(select "Team Count by School / Club" as "Category", "" as "Group", "" as "Count" from dual) ' .
      'union all ' .
      '(select ' .
      '  "", ' .
      '  organisation, ' .
      '  count(*) ' .
      'from ' .
      '  v_team ' .
      'where ' .
      '  uid_comp_name = "' . $uid_comp_name . '" ' .
      'group by ' .
      '  organisation ' .
      'order by ' .
      '  "organisation") ' .
      'union all ' .
      '(select ' .
      '  "", "Count of Schools / Clubs", count(*) ' .
      'from ' .
      '  (select ' .
      '     organisation ' .
      '  from ' .
      '    v_team ' .
      '  where ' .
      '    uid_comp_name = "' . $uid_comp_name . '" ' .
      '  group by ' .
      '    organisation) t)';      
  } else if ($report_type == 'TEAMS'){
    $title = 'Teams';
    if ($report_format == 'DOWNLOAD'){
      $sql = 
        'select ' . 
        '  uid as "Team Unique ID", ' .
        '  uid_division as "Division Unique ID", ' .    
        '  organisation as "Mentor Organisation", ' .
        '  concat(mentor_first_name, " ", mentor_last_name) as "Mentor Name", ' .
        '  mentor_email as "Mentor Email", ' .    
        '  div_name as "Division Name", ' .                       
        '  team_name as "Team Name" ' .                     
        'from ' .
        '  v_team ' .
        'where ' .
        '  uid_comp_name = "' . $uid_comp_name . '"' .
        'order by ' .
        '  div_disp_order, ' .                       
        '  team_name';
    } else {
      $sql = 
        'select ' . 
        '  div_name as "Division Name", ' .                       
        '  organisation as "Mentor Organisation", ' .
        '  concat(mentor_first_name, " ", mentor_last_name) as "Mentor Name", ' .
        '  mentor_email as "Mentor Email", ' .    
        '  team_name as "Team Name" ' .                     
        'from ' .
        '  v_team ' .
        'where ' .
        '  uid_comp_name = "' . $uid_comp_name . '"' .
        'order by ' .
        '  div_disp_order, ' .                       
        '  organisation, ' .                       
        '  team_name';
    }      
  } else if ($report_type == 'STUDENTS'){
    $title = 'Students';
    $sql = 
        'select ' .
        '  organisation as "Mentor Organisation", ' .
        '  concat(mentor_first_name, " ", mentor_last_name) as "Mentor Name", ' .
        '  div_name as "Division Name", ' .
        '  team_name as "Team Name", ' .
        '  concat(team_member_first_name, " ", team_member_last_name) as "Team Member Name" ' . 
        'from ' .
        '  v_team_member ' .
        'where ' .
        '  uid_comp_name = "' . $uid_comp_name . '" ' .
        'order by ' .
        '  "Mentor Organisation", ' .
        '  "Division Name", ' .
        '  "Team Name", ' .
        '  "Student Name"';
  } else if ($report_type == 'SQL'){
    $title = 'Insert SQL';
    $sql = 'select "truncate table imported_team;" from dual union all '
         . 'select concat("insert into imported_team (uid, team_name, organisation, mentor_first, mentor_last, mentor_email, registered_division, uid_imported_mentor, num_member) values (",' 
         . 'quote(uid), ", ", '
         . 'quote(team_name), ", ", '
         . 'quote(organisation), ", ", ' 
         . 'quote(mentor_first_name), ", ", ' 
         . 'quote(mentor_last_name), ", ", ' 
         . 'quote(mentor_email), ", ", ' 
         . 'quote(div_name), ", ", ' 
		 . 'quote(uid_mentor_team), ", ", ' 
         . '      ' . 'tm.team_member_count' . ' , "); "'
         . ') '         
         . 'from '
         . '  v_team '
         . 'left join (select uid_team, count(*) as team_member_count from team_member group by uid_team) tm '
         . '  on tm.uid_team = v_team.uid          '
         . 'where '
         . '  uid_comp_name = "' . $uid_comp_name . '" '
         . 'union all '
         . 'select "truncate table imported_team_member;" from dual union all '
         . 'select concat("insert into imported_team_member (uid, uid_imported_team, first_name, last_name) values (",' 
         . 'quote(uid_team_member), ", ", '
         . 'quote(uid_team), ", ", '
         . 'quote(team_member_first_name), ", ", ' 
         . 'quote(team_member_last_name),"); ") '
         . 'from '
         . '  v_team_member '
         . 'where '
         . '  uid_comp_name = "' . $uid_comp_name . '" '
		 . 'union all '
		 . 'select "truncate table imported_mentor;" from dual union all '
		 . 'select concat ("insert into imported_mentor (uid, pmt, comments) values (",'
		 . 'quote(uid), ", ", '
		 . 'quote(pmt_ok), ", ", '
		 . 'quote(pmt_notes),"); ") '
		 . 'from '
		 . 'mentor_team '
		 . 'where '
		 . '  uid_comp_name = "' . $uid_comp_name . '"';
	  
  } else if ($report_type == 'ORGANISATION'){
    $title = 'Organisation Listing';
    $sql = 
      'select ' .
      '  organisation as "Mentor Organisation", ' .
      '  concat(mentor_first_name, " ", mentor_last_name) as "Mentor Name", ' .
      '  mentor_email as "Mentor Email", ' .    
      '  div_name as "Division Name", ' .
      '  count(*) as "Team Count" ' .
      'from ' .
      '  v_team ' .
      'where ' .
      '  uid_comp_name = "' . $uid_comp_name . '"' .
      'group by ' .
      '  organisation, ' .                        
      '  div_name ' .
      'order by ' .
      '  organisation, ' .                       
      '  div_disp_order';
  } 
  return $sql;  
}

function RunReport($con, $uid_comp_name, $report_type, $report_format){
  GetReportDefinition($report_type, $report_format, $uid_comp_name, $sql, $report_name);
  
  if ($report_format == 'DOWNLOAD'){
    $file_name = UIDCompNameToFileName($con, $uid_comp_name, $report_name);  
    QueryToCSVDownload($con, $sql, $file_name);
  } else {
    $title = UIDCompNameToDescription($con, $uid_comp_name) . ' | ' . $report_name;  
    QueryToPage($con, $sql, $title, $uid_comp_name, $report_type, $report_format);
  }
}
    
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }

  //cleanup any crap from closed competitions before running reports, don't cleanup open competitions in case somebody is half way through inputting first team
  	$comps_to_clean = $con->query('SELECT uid FROM comp_name WHERE NOW()>end_date');
	foreach($comps_to_clean as $comp):
		$result = $con->query('SELECT uid FROM mentor_team WHERE uid_comp_name = "' . $comp['uid'] . '"');
		 foreach($result as $row):
			$team_count = $con->query('SELECT count(uid) AS count FROM team WHERE uid_mentor_team = "' . $row['uid'] . '"')->fetchColumn();
			if($team_count==0):
				$delete = $con->query('DELETE FROM mentor_team where uid = "' . $row['uid'] . '"');
			endif;
		 endforeach;
	endforeach;
    
 $action                = postFieldDefault('action'); 
 $uid_comp_name         = postFieldDefault('uid_comp_name'); 
 $report_type           = postFieldDefault('report_type'); 
 $report_format         = postFieldDefault('report_format');
 $comp_message          = '';
 $report_type_message   = '';
 $report_format_message = '';
 
 if (($action == 'execute') and IsValid($uid_comp_name, $comp_message, $report_type, $report_type_message, $report_format, $report_format_message)){
   RunReport($con, $uid_comp_name, $report_type, $report_format);
 } else{
   if (empty($report_type)){$report_type = 'SUMMARY';}
   if (empty($report_format)){$report_format = 'BROWSER';} 
   WriteHTML($con, $uid_comp_name, $comp_message, $report_type, $report_type_message, $report_format, $report_format_message);
 }
      
?>