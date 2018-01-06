 <?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/connect-mysqli.php';

try {
  
  echo '<p><a href="./">Back to DB script index</a> | <a href="/">Home</a> | <a href="/recreate-database-00.php">Run again</a></p>';

  ceDropView($con,  'v_local_time');
  ceDropView($con,  'v_comp_name');
  ceDropView($con,  'v_mentor_team');
  ceDropView($con,  'v_comp_division');
  ceDropView($con,  'v_team');
  ceDropView($con,  'v_team_member');
  ceDropView($con,  'v_comp_state');

  // Might want to allow for day light savings time corrections, but hey, what's an hour....
  ceExecSQL(
    $con, 
    'create view v_local_time as ' .
    'select CONVERT_TZ(UTC_TIMESTAMP(), "+00:00", "+10:00") as now from dual',v
	  'Created: v_local_time');  

  ceExecSQL(
    $con, 
    'create view v_comp_name   ' .
    'as select                 ' .
    '  n.uid as uid_comp_name, ' .
    '  n.uid_year,             ' .
    '  n.uid_state,            ' .
    '  n.event_date,           ' .
    '  n.start_date,           ' .
    '  n.end_date,             ' .
    '  if(DATE(n.start_date) <= (select now from v_local_time) and (select now from v_local_time) < DATE_ADD(DATE(n.end_date), INTERVAL 1 DAY), 1, 0) as active, '.
    '  y.year,                 ' . 
    '  s.state,                ' . 
    '  n.comp_name,            ' . 
    '  n.custom_message,       ' .
    '  n.venue,                ' .
    '  u.first_name as enquiries_to_first_name, ' .
    '  u.last_name as enquiries_to_last_name, ' .
    '  u.email as enquiries_to_email ' .
    'from                      ' . 
    '  comp_name   n           ' . 
    'left join comp_state s    ' .
    '  on n.uid_state = s.uid  ' . 
    'left join comp_year y     ' .
    '  on n.uid_year = y.uid   ' . 
    'left join user u              ' .
    '  on n.uid_enquiries_to = u.uid ' . 
    'order by                  ' . 
    '  year,                   ' .
    '  state,                  ' .
    '  comp_name               ',
    'Created: v_comp_name');
    
  ceExecSQL(
    $con, 
    'create view v_mentor_team     ' .
    'as select                     ' .
    '  mt.uid,                     ' . 
    '  mt.uid_user,                ' .
    '  mt.uid_comp_name,           ' .
    '  mt.organisation,            ' .
    '  c.year,                     ' . 
    '  c.state,                    ' . 
    '  c.comp_name,                ' . 
    '  c.custom_message as comp_message, ' . 
    '  c.start_date,               ' .
    '  c.end_date,                 ' .
    '  c.active,                   ' .
    '  u.first_name,               ' .
    '  u.last_name                 ' .
    'from                          ' . 
    '  mentor_team  mt             ' . 
    'left join user u              ' .
    '  on mt.uid_user = u.uid      ' . 
    'left join v_comp_name c       ' .
    '  on mt.uid_comp_name = c.uid_comp_name ' .
    'order by                      ' .
    '  year,                       ' . 
    '  state,                      ' . 
    '  comp_name,                  ' . 
    '  primary_org,                ' .
    '  first_name,                 ' .
    '  last_name                   ',
    'Created: v_mentor_team');
    
  ceExecSQL(
    $con, 
    'create view v_comp_division   ' .
    'as select                     ' .
    '  y.uid as uid_year,          ' .
    '  d.uid as uid_comp_division, ' .
    '  n.uid_comp_name,            ' .
    '  n.uid_state,                ' .
    '  y.year,                     ' .
    '  n.state,                    ' .
    '  n.comp_name,                ' .
    '  n.start_date,               ' .
    '  n.active,                   ' .
    '  n.end_date,                 ' .
    '  d.disp_order,               ' .
    '  d.div_name                  ' .
    'from                          ' . 
    '  comp_division   d           ' . 
    'right join v_comp_name n       ' .
    '  on d.uid_comp_name = n.uid_comp_name  ' . 
    'right join comp_year y                  ' .
    '  on n.uid_year = y.uid                ' . 
    'order by                      ' . 
    '  year,                       ' .
    '  state,                      ' .
    '  comp_name,                  ' .
    '  disp_order                  ',
    'Created: v_comp_division');

  ceExecSQL(
    $con, 
    'create view v_team                ' .
    'as select                         ' .
    '  t.uid,                          ' .
    '  c.uid_comp_name,                ' .
    '  cd.uid as uid_division,         ' .
    '  mt.uid_user,                    ' .
    '  c.year,                         ' . 
    '  c.state,                        ' . 
    '  c.comp_name,                    ' . 
    '  c.start_date,                   ' .
    '  c.end_date,                     ' .
    '  c.active,                       ' .
    '  mt.organisation,                ' .
    '  u.first_name as mentor_first_name, ' .
    '  u.last_name as mentor_last_name,   ' .
    '  u.email as mentor_email,           ' . 
    '  cd.disp_order as div_disp_order,   ' .
    '  cd.div_name,                       ' .
    '  t.team_name,                    ' .
	'  t.soccer_group,                 ' .
	'  t.uid_mentor_team               ' .
    'from                              ' . 
    '  team t                          ' .
    'left join comp_division cd        ' .
    '  on t.uid_comp_division = cd.uid ' . 
    'left join mentor_team mt          ' .
    '  on t.uid_mentor_team = mt.uid   ' . 
    'left join user u                  ' .
    '  on mt.uid_user = u.uid          ' . 
    'left join v_comp_name c           ' .
    '  on cd.uid_comp_name = c.uid_comp_name ' .
    'order by                          ' .
    '  year,                           ' . 
    '  state,                          ' . 
    '  comp_name,                      ' . 
    '  organisation,                   ' .
    '  mentor_first_name,              ' .
    '  mentor_last_name,               ' .
    '  div_disp_order,                 ' .
    '  team_name                       ',
    'Created: v_team');    
    
  ceExecSQL(
    $con, 
    'create view v_team_member            ' .
    'as select                            ' .
    '  tm.uid as uid_team_member,         ' .
    '  t.uid  as uid_team,                ' .
    '  mt.uid  as uid_mentor_team,        ' .
    '  cd.uid as uid_comp_division,       ' .
    '  c.uid_comp_name as uid_comp_name,  ' .
    '  u.uid as uid_mentor,               ' .
    '  c.year,                            ' . 
    '  c.state,                           ' . 
    '  c.comp_name,                       ' . 
    '  c.start_date,                      ' .
    '  c.end_date,                        ' .
    '  c.active,                          ' .
    '  mt.organisation,                   ' .
    '  u.first_name as mentor_first_name, ' .
    '  u.last_name as mentor_last_name,   ' .
    '  cd.div_name,                       ' .
    '  cd.disp_order as div_disp_order,   ' .
    '  t.team_name,                       ' .
    '  tm.first_name as team_member_first_name, ' .
    '  tm.last_name as team_member_last_name,   ' .
    '  tm.gender,                               ' .
    '  tm.year_at_school                  ' .
    'from                                 ' . 
    '  team_member tm                     ' .
    'left join team t                     ' .
    '  on tm.uid_team = t.uid             ' . 
    'left join comp_division cd           ' .
    '  on t.uid_comp_division = cd.uid    ' . 
    'left join mentor_team mt             ' .
    '  on t.uid_mentor_team = mt.uid      ' . 
    'left join user u                     ' .
    '  on mt.uid_user = u.uid             ' . 
    'left join v_comp_name c              ' .
    '  on cd.uid_comp_name = c.uid_comp_name ' .
    'order by                        ' .
    '  year,                         ' . 
    '  state,                        ' . 
    '  comp_name,                    ' . 
    '  organisation,                 ' .
    '  mentor_first_name,            ' .
    '  mentor_last_name,             ' .
    '  div_disp_order,               ' .
    '  team_name,                    ' .
    '  team_member_last_name,        ' .
    '  team_member_first_name        ',
    'Created: v_team_member');

  ceExecSQL(
    $con, 
    'create view v_comp_state as
     (
       select 
         s.uid,
         s.state,
         u.first_name as treasurer_first_name,
         u.last_name as treasurer_last_name,
         u.email as treasurer_email,
         u.primary_org as treasurer_primary_org,
         u.adrs_line_1 as treasurer_adrs_line_1,
         u.adrs_line_2 as treasurer_adrs_line_2,
         u.suburb as treasurer_suburb,
         u.postcode as treasurer_postcode,
         u.state as treasurer_state,
         s.account_name,
         s.account_bsb,
         s.account_number
       from 
         comp_state s
       left join user u on u.uid = s.uid_treasurer)',
     'View v_comp_state created');  


  ceExecSQL(
    $con, 
    'drop trigger if exists trg_user_access_level_insert',
    'drop trigger trg_user_access_level_insert');

  ceExecSQL(
    $con, 
    'create trigger trg_user_access_level_insert before insert on user ' . 
    '  for each row ' . 
    '  begin ' .  
    '    if (new.access_level <> "MENTOR") and ' . 
    '       (new.access_level <> "COMP_ADMIN") and ' .
    '       (new.access_level <> "SYS_ADMIN") and ' .
    '       (new.access_level <> "SYS_DEV") then ' .
    '       SIGNAL SQLSTATE "45000" ' .   
    '       SET MESSAGE_TEXT = "Invalid access_level"; ' .
    '    end if; ' . 
    '  end; ', 
    'Created constraint');
    
  ceExecSQL(
    $con, 
    'drop trigger if exists trg_user_access_level_update',
    'drop trigger trg_user_access_level_update');

  ceExecSQL(
    $con, 
    'create trigger trg_user_access_level_update before update on user ' . 
    '  for each row ' . 
    '  begin ' .  
    '    if (new.access_level <> "MENTOR") and ' . 
    '       (new.access_level <> "COMP_ADMIN") and ' .
    '       (new.access_level <> "SYS_ADMIN") and ' .
    '       (new.access_level <> "SYS_DEV") then ' .
    '       SIGNAL SQLSTATE "45000" ' .   
    '       SET MESSAGE_TEXT = "Invalid access_level"; ' .
    '    end if; ' . 
    '  end; ', 
    'Created constraint'); 
    
} 

catch (Exception $e) 

{
    echo $e->getMessage();
}
 
  //$con->close;
  
  echo '<p><a href="./">Back to DB script index</a> | <a href="/">Home</a> | <a href="/recreate-database-00.php">Run again</a></p>';
  echo '</html>';
      
?>