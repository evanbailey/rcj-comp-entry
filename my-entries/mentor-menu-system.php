<?php

function invoiceURL($uidUser, $uidCompName){
  $result =
    '    <a href="/invoice/?uid_user=' . $uidUser . '&uid_comp_name=' . $uidCompName . '" target="_blank">View invoice</a>';
  return $result;
}

function viewEditEntryText($viewEdit, $count){
  if ($count > 1){
    $result = $viewEdit . ' my ' . $count . ' entries';
  } else {
    $result = $viewEdit . ' my ' . $count . ' entry';    
  }
  return $result;  
}

function outputCustomMessage($uid){
  echo '<br><a href="javascript:cePost(\'/my-entries/show-comp-message.php\', {\'uid_comp_name\': \'' . $uid . '\'})">Read the event notes...</a>.<br>';
}

function WriteMentorCompetitionIndex($con)
{
  
  $sql = 
    'select ' . 
    '  cn.uid_comp_name   as uid_comp_name, ' .
    '  cn.year            as year, ' .
    '  cn.state           as state, ' .
    '  cn.comp_name       as comp_name, ' .
    '  cn.custom_message  as custom_message, ' .
    '  cn.active          as active, ' .
    '  date_format(cn.event_date, "%d %b %Y") as event_date, ' .
    '  IF(cn.active, date_format(end_date, "%d %b %Y"), "Closed") as open_until, ' .
    '  IFNULL(cnt.cnt , 0) as count ' .
    'from ' .
    '  (select ' .
    '    uid_comp_name   as uid_comp_name, ' .
    '    year            as year, ' .
    '    state           as state, ' .
    '    comp_name       as comp_name, ' .
    '    custom_message  as custom_message, ' .
    '    active          as active, ' .
    '    end_date        as end_date, ' .
	'	 event_date		 as event_date ' .
    '   from ' .
    '     v_comp_name ) as cn ' .
    'left join ' .
    ' (SELECT ' .
    '    uid_comp_name, ' .
    '    count(*)       as cnt ' .
    '    FROM v_team ' .
    '   where ' .
    '        uid_user = "' . $_SESSION['uid_logged_on_user'] . '" ' .
    '   group by ' .
    '     uid_comp_name) as cnt ' .
    'on cn.uid_comp_name = cnt.uid_comp_name ' .
	'where cn.year = YEAR(NOW()) OR cn.year = YEAR(NOW())-1 ' .
	'order by year DESC, state ASC, event_date ASC ';
    
  echo '<script>
         function editMyEntries(uidCompName){
           cePost("/my-entries/edit-comp.php", {uid_comp_name: uidCompName});       
         }
         function viewMyEntries(uidCompName){
           cePost("/my-entries/edit-comp.php", {uid_comp_name: uidCompName, read_only: true});       
         }
         
        </script>';  
    
  echo '<h2>Rules and results</h2>';  
  echo '<p class="indent"><a href="http://www.robocupjunior.org.au/dance">View the RCJA National Dance Rules</a></p>';
  echo '<p class="indent"><a href="http://www.robocupjunior.org.au/rescue">View the RCJA National Rescue Rules</a></p>';
  echo '<p class="indent"><a href="http://www.robocupjunior.org.au/soccer">View the RCJA National Soccer Rules</a></p>';
  
  echo '<p class="indent"><a href="http://www.robocupjunior.org.au/vic/rescue">View RCJV Victorian Rescue Rules</a></p>';
  
  echo '<p class="indent"><a href="http://www.robocupjunior.org.au/sites/default/files/RCJA_Media_Release_Deed_5.pdf">View RCJA Media Release Deed</a></p>';
    
  echo '<h2>Enter a competition | Edit my competition entries</h2>';
  echo "<table border='1'>
          <tr>
            <th>Year</th>
            <th>State</th>
            <th>Event Name</th>
            <th>Event Date</th>
            <th>Open Until</th>
            <th>My entries and invoice</th>
        </tr>";

  foreach($con->query($sql) as $row)
  {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['year']) . "</td>";
    echo "<td>" . htmlspecialchars($row['state']) . "</td>";
    echo "<td>" . htmlspecialchars($row['comp_name']); 
    if (!empty($row['custom_message'])){
      outputCustomMessage($row['uid_comp_name']);
    }
    echo "</td>";
    echo "<td>" . $row['event_date'] . "</td>";
    echo "<td>" . $row['open_until'] . "</td>";
    echo '<td align="center">';
    if ($row['count'] > 0)
    {
      if ($row['active']){
        echo '<a href="javascript:editMyEntries(\'' . $row["uid_comp_name"] . '\')">' . viewEditEntryText('Edit', $row['count']) . '</a> | ';
        echo '<a href="javascript:CEPostDelete(\'' . $row["uid_comp_name"] . '\', \'/my-entries/delete-comp.php\')">Delete</a> | ';
      } else {
        echo '<a href="javascript:viewMyEntries(\'' . $row["uid_comp_name"] . '\')">' . viewEditEntryText('View', $row['count']) . '</a> | ';        
      }
      echo invoiceURL($_SESSION['uid_logged_on_user'], $row["uid_comp_name"]); 

    }
    else
    {
      if ($row['active']){
        echo '<a href="javascript:editMyEntries(\'' . $row["uid_comp_name"] . '\')">Enter</a>';
      } else {
        echo '&nbsp;';  
      }  
    } 
      echo '</td>';
      echo "</tr>";    
  }

  echo "</table>"; 
 
}

?>