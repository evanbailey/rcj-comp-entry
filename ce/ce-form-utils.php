<?php
  function CEWritePageHeader($site_title, $title){
    echo '<html>
          <head>
          <title>' . htmlspecialchars($site_title) . ' | ' . htmlspecialchars($title) . '</title>
            <link rel="icon" type="image/ico" href="/favicon.ico">
	        <link rel="stylesheet" type="text/css" href="/shared/style.css">
            <script src="/ce/ce-utils.js"></script>
          </head>
          <body>';
  }

  function CEWriteFormStart($heading, $formname, $formaction)
  {
    echo '<h1>' . htmlspecialchars($heading) . '</h1>';
    echo '<form name="'. $formname . '" action="' . $formaction . '" method="post">';
    echo '<fieldset><legend>' . htmlspecialchars($heading) . '</legend>';
  }
  
  function ceWriteSaveAndCancelButtons($back = ''){
    echo '  <input type="submit" value="Save">';
    if (!empty($back))
      echo '  <input type="button" value="Cancel" onclick="window.location=\'' . $back . '\';">';    
  }
  
  function CEWriteFormEnd($back = '')
  {
    ceWriteSaveAndCancelButtons($back);
    echo '</fieldset>';
    echo '</form>';
  }

  function CEWritePageEnd($back = '')
  {
    echo '<p><a href="/">Home</a>';
    if (!empty($back)){
      echo ' | <a href="' . $back . '">Back</a>';
    };
    echo '</p>';
    echo '</body>';
    echo '</html>';
  }

  function CEWriteFormAction($action)
  {
    echo '  <input type="hidden" name="action" value="' . $action . '">';
  }  
  
  function CEWriteFormFieldHidden($name, $value)
  {
    echo '  <input type="hidden" name="' . $name . '" id=".' . $name . '" value="' . $value . '">';
  }
  
  function CEWriteFormFieldText($name, $prompt, $value, $maxlen, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="text" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" maxlength="' . $maxlen . 
         '" class="textbox"></p>';    
  }

  function CEWriteFormFieldMemo($name, $prompt, $value, $rows, $cols, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<textarea rows="' . $rows . '" cols="' . $cols . '" ' .
         'name="' . $name . '" ' .
         '" id="' . $name . '" ' .
         'class="textarea" ' .
         '>' . htmlspecialchars($value) .         
         '</textarea></p>';    
  }
  
  
  function CEWriteFormFieldNumber($name, $prompt, $value, $min, $max, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="number" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" min="' . $min . 
         '" max="' . $max . 
         '" class="textbox"></p>';    
  }
  
  function CEWriteFormFieldCurrency($name, $prompt, $value, $min, $max, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="number" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" min="' . $min . 
         '" max="' . $max .
         '" step="0.01"' .         
         ' class="textbox"></p>';    
  }


  function CEWriteFormFieldPassword($name, $prompt, $value, $maxlen, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="password" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" maxlength="' . $maxlen . 
         '" class="textbox"></p>';    
  }

    function CEWriteFormFieldPasswordAutoFocus($name, $prompt, $value, $maxlen, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="password" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" maxlength="' . $maxlen . 
         '" class="textbox" autofocus></p>';    
  }

function CEWriteFormFieldTextAutofocus($name, $prompt, $value, $maxlen, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="text" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" maxlength="' . $maxlen . 
         '" class="textbox" autofocus></p>';
  }
  
  function CEWriteFormFieldDate($name, $prompt, $value, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="date" name="' . $name . 
         '" id="' . $name . 
         '" value="' . date('Y-m-d', strtotime($value)) . 
         '" class="textbox"></p>';
  }

  // Writes a drop-down list of name:value pairs. SQL must return two colums called "uid" and "display"
  function CEWriteFormFieldDropDown($name, $prompt, $value, $con, $sql, $message = '', $onchange = '')
  { 
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<select name="' . $name . '" id="' . $name . '"';
    if (!empty($onchange))
    {
      echo 'onchange="' . $onchange . '"';
	}		
	echo ' class="textbox">';
    echo '<option value=""></option>';
    foreach($con->query($sql) as $row)
    {
      echo '<option value="' . $row['uid'] . '"';
      if ($row['uid'] == $value )
        echo ' selected';
      echo '>' . htmlspecialchars($row['display']) . '</option>';
    }
    echo '</select>';
    echo '</p>';  
    
  }

  function CEWriteFormFieldCheckBox($name, $prompt, $value)
  {
    echo '<p>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="checkbox" name="' . $name . 
         '" id="' . $name . 
         '" class="textbox"';
    if ($value){
        echo ' checked="checked" ';
    }     
    echo '></p>';    
  }
  
  /* Write a drop-down field, with hard coded name-value pairs.
     Items must be passed like this:
         array('NAME_1' => 'Value 1', 
               'NAME_1' => 'Value 2')
  */             
  function CEWriteFormFieldDropDownHardCoded($name, $prompt, $value, $items, $message = '')
  { 
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<select name="' . $name . '" id="'. $name . '" class="textbox">';
    echo '<option value=""></option>';
	foreach ($items as $itemkey => $itemvalue) 
	{
      echo '<option value="' . $itemkey . '"';
      if ($value == $itemkey )
        echo ' selected';
      echo '>' . $itemvalue . '</option>';
    }
    echo '</select>';
    echo '</p>';  
    
  } 

  // Writes a JSON array of name:value pairs from a query with two colums, uid and display
  function CEWriteJSONArrayFromQuery($con, $sql)
  {
    $i = 0;
    echo '{';
    foreach($con->query($sql) as $row)
    {
      if ($i > 0)
	    {echo ", \r\n";}
	  echo '"' . $row['uid'] . '":"' . $row['display'] . '"';
	  $i++;
    }
    echo '}';
  }  
  
  function CEWriteLinkToJQueryLibrary()
  {
    echo '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	     ';
  }

  // $FunctionName: The name of the JavaScript to be written to your HTML
  // $KeyFieldName: The name of the primary key used for the lookup. Maps to the HTML form and the param passed to the server
  // $FormName:     The name of the form within the HTML to be referenced
  // $DataSource: The page that will supply the response in the form written by CEWriteJSONArrayFromQuery()
  // $SelectName:   The name of the <select> field to be populated with the results 
  function CEWriteSelectRefreshEvent($FunctionName, $KeyFieldName, $FormName, $DataSource, $SelectName)
  {
	echo '<script> ' . PHP_EOL ;
	echo 'function ' . $FunctionName . '() ' . PHP_EOL ;
	echo '{ var i = 1; ' . PHP_EOL ;
	echo '  var ' . $KeyFieldName . ' = document.getElementById("' . $KeyFieldName . '").value; ' . PHP_EOL ;
	echo '  $.getJSON("' . $DataSource . '?' . $KeyFieldName . '=" + ' . $KeyFieldName . ', function(data) ' . PHP_EOL ;
	echo '  { ' . PHP_EOL ;
    echo '      document.' . $FormName . '.' . $SelectName . '.options.length = 0; ' . PHP_EOL ;
    echo '      document.' . $FormName . '.' . $SelectName . '.options[0]=new Option("", "", true, true); ' . PHP_EOL ;
    echo '      $.each(data, function(index, value) ' . PHP_EOL ; 
	echo '	   { ' . PHP_EOL ;
    echo '       document.' . $FormName . '.' . $SelectName . '.options[document.' . $FormName . '.' . $SelectName . '.options.length]=new Option(value, index, false, false); ' . PHP_EOL ;
    echo '      }); ' . PHP_EOL ;
	echo '  }); ' . PHP_EOL ;
    echo '}'  . PHP_EOL ;
    echo '</script>'  . PHP_EOL ;

  }
  
  function CEWritePageFooter(){
      echo '<p><a href="/">Home</a></p>
            </body>
            </html>';
  }

  function postFieldDefault($FieldName, $Default = ''){
	if( isset($_POST[$FieldName]) )
	{	
	  $result = trim($_POST[$FieldName]);
    }
	else
	{
	  $result = $Default;  
    }
	return $result;  
  }
  
  function getFieldDefault($FieldName, $Default = ''){
	if( isset($_GET[$FieldName]) )
	{	
	  $result = trim($_GET[$FieldName]);
    }
	else
	{
	  $result = $Default;  
    }
	return $result;  
  }
  
<?php
  function CEWritePageHeader($site_title, $title){
    echo '<html>
          <head>
          <title>' . htmlspecialchars($site_title) . ' | ' . htmlspecialchars($title) . '</title>
            <link rel="icon" type="image/ico" href="/favicon.ico">
	        <link rel="stylesheet" type="text/css" href="/shared/style.css">
            <script src="/ce/ce-utils.js"></script>
          </head>
          <body>';
  }

  function CEWriteFormStart($heading, $formname, $formaction)
  {
    echo '<h1>' . htmlspecialchars($heading) . '</h1>';
    echo '<form name="'. $formname . '" action="' . $formaction . '" method="post">';
    echo '<fieldset><legend>' . htmlspecialchars($heading) . '</legend>';
  }
  
  function ceWriteSaveAndCancelButtons($back = ''){
    echo '  <input type="submit" value="Save">';
    if (!empty($back))
      echo '  <input type="button" value="Cancel" onclick="window.location=\'' . $back . '\';">';    
  }
  
  function CEWriteFormEnd($back = '')
  {
    ceWriteSaveAndCancelButtons($back);
    echo '</fieldset>';
    echo '</form>';
  }

  function CEWritePageEnd($back = '')
  {
    echo '<p><a href="/">Home</a>';
    if (!empty($back)){
      echo ' | <a href="' . $back . '">Back</a>';
    };
    echo '</p>';
    echo '</body>';
    echo '</html>';
  }

  function CEWriteFormAction($action)
  {
    echo '  <input type="hidden" name="action" value="' . $action . '">';
  }  
  
  function CEWriteFormFieldHidden($name, $value)
  {
    echo '  <input type="hidden" name="' . $name . '" id=".' . $name . '" value="' . $value . '">';
  }
  
  function CEWriteFormFieldText($name, $prompt, $value, $maxlen, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="text" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" maxlength="' . $maxlen . 
         '" class="textbox"></p>';    
  }

  function CEWriteFormFieldMemo($name, $prompt, $value, $rows, $cols, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<textarea rows="' . $rows . '" cols="' . $cols . '" ' .
         'name="' . $name . '" ' .
         '" id="' . $name . '" ' .
         'class="textarea" ' .
         '>' . htmlspecialchars($value) .         
         '</textarea></p>';    
  }
  
  
  function CEWriteFormFieldNumber($name, $prompt, $value, $min, $max, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="number" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" min="' . $min . 
         '" max="' . $max . 
         '" class="textbox"></p>';    
  }
  
  function CEWriteFormFieldCurrency($name, $prompt, $value, $min, $max, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="number" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" min="' . $min . 
         '" max="' . $max .
         '" step="0.01"' .         
         ' class="textbox"></p>';    
  }


  function CEWriteFormFieldPassword($name, $prompt, $value, $maxlen, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="password" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" maxlength="' . $maxlen . 
         '" class="textbox"></p>';    
  }

    function CEWriteFormFieldPasswordAutoFocus($name, $prompt, $value, $maxlen, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="password" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" maxlength="' . $maxlen . 
         '" class="textbox" autofocus></p>';    
  }

function CEWriteFormFieldTextAutofocus($name, $prompt, $value, $maxlen, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="text" name="' . $name . 
         '" id="' . $name . 
         '" value="' . htmlspecialchars($value) . 
         '" maxlength="' . $maxlen . 
         '" class="textbox" autofocus></p>';
  }
  
  function CEWriteFormFieldDate($name, $prompt, $value, $message = "")
  {
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="date" name="' . $name . 
         '" id="' . $name . 
         '" value="' . date('Y-m-d', strtotime($value)) . 
         '" class="textbox"></p>';
  }

  // Writes a drop-down list of name:value pairs. SQL must return two colums called "uid" and "display"
  function CEWriteFormFieldDropDown($name, $prompt, $value, $con, $sql, $message = '', $onchange = '')
  { 
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<select name="' . $name . '" id="' . $name . '"';
    if (!empty($onchange))
    {
      echo 'onchange="' . $onchange . '"';
	}		
	echo ' class="textbox">';
    echo '<option value=""></option>';
    foreach($con->query($sql) as $row)
    {
      echo '<option value="' . $row['uid'] . '"';
      if ($row['uid'] == $value )
        echo ' selected';
      echo '>' . htmlspecialchars($row['display']) . '</option>';
    }
    echo '</select>';
    echo '</p>';  
    
  }

  function CEWriteFormFieldCheckBox($name, $prompt, $value)
  {
    echo '<p>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<input type="checkbox" name="' . $name . 
         '" id="' . $name . 
         '" class="textbox"';
    if ($value){
        echo ' checked="checked" ';
    }     
    echo '></p>';    
  }
  
  /* Write a drop-down field, with hard coded name-value pairs.
     Items must be passed like this:
         array('NAME_1' => 'Value 1', 
               'NAME_1' => 'Value 2')
  */             
  function CEWriteFormFieldDropDownHardCoded($name, $prompt, $value, $items, $message = '')
  { 
    echo '<p>';
    if (!empty($message))
      echo '<span class="field_message">' . $message . '</span>';
    echo '<span class="field_label">' . $prompt . ':</span>';
    echo '<select name="' . $name . '" id="'. $name . '" class="textbox">';
    echo '<option value=""></option>';
	foreach ($items as $itemkey => $itemvalue) 
	{
      echo '<option value="' . $itemkey . '"';
      if ($value == $itemkey )
        echo ' selected';
      echo '>' . $itemvalue . '</option>';
    }
    echo '</select>';
    echo '</p>';  
    
  } 

  // Writes a JSON array of name:value pairs from a query with two colums, uid and display
  function CEWriteJSONArrayFromQuery($con, $sql)
  {
    $i = 0;
    echo '{';
    foreach($con->query($sql) as $row)
    {
      if ($i > 0)
	    {echo ", \r\n";}
	  echo '"' . $row['uid'] . '":"' . $row['display'] . '"';
	  $i++;
    }
    echo '}';
  }  
  
  function CEWriteLinkToJQueryLibrary()
  {
    echo '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	     ';
  }

  // $FunctionName: The name of the JavaScript to be written to your HTML
  // $KeyFieldName: The name of the primary key used for the lookup. Maps to the HTML form and the param passed to the server
  // $FormName:     The name of the form within the HTML to be referenced
  // $DataSource: The page that will supply the response in the form written by CEWriteJSONArrayFromQuery()
  // $SelectName:   The name of the <select> field to be populated with the results 
  function CEWriteSelectRefreshEvent($FunctionName, $KeyFieldName, $FormName, $DataSource, $SelectName)
  {
	echo '<script> ' . PHP_EOL ;
	echo 'function ' . $FunctionName . '() ' . PHP_EOL ;
	echo '{ var i = 1; ' . PHP_EOL ;
	echo '  var ' . $KeyFieldName . ' = document.getElementById("' . $KeyFieldName . '").value; ' . PHP_EOL ;
	echo '  $.getJSON("' . $DataSource . '?' . $KeyFieldName . '=" + ' . $KeyFieldName . ', function(data) ' . PHP_EOL ;
	echo '  { ' . PHP_EOL ;
    echo '      document.' . $FormName . '.' . $SelectName . '.options.length = 0; ' . PHP_EOL ;
    echo '      document.' . $FormName . '.' . $SelectName . '.options[0]=new Option("", "", true, true); ' . PHP_EOL ;
    echo '      $.each(data, function(index, value) ' . PHP_EOL ; 
	echo '	   { ' . PHP_EOL ;
    echo '       document.' . $FormName . '.' . $SelectName . '.options[document.' . $FormName . '.' . $SelectName . '.options.length]=new Option(value, index, false, false); ' . PHP_EOL ;
    echo '      }); ' . PHP_EOL ;
	echo '  }); ' . PHP_EOL ;
    echo '}'  . PHP_EOL ;
    echo '</script>'  . PHP_EOL ;

  }
  
  function CEWritePageFooter(){
      echo '<p><a href="/">Home</a></p>
            </body>
            </html>';
  }

  function postFieldDefault($FieldName, $Default = ''){
	if( isset($_POST[$FieldName]) )
	{	
	  $result = trim($_POST[$FieldName]);
    }
	else
	{
	  $result = $Default;  
    }
	return $result;  
  }
  
  function getFieldDefault($FieldName, $Default = ''){
	if( isset($_GET[$FieldName]) )
	{	
	  $result = trim($_GET[$FieldName]);
    }
	else
	{
	  $result = $Default;  
    }
	return $result;  
  }
  
?>