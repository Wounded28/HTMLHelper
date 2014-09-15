<?php
const EOL = PHP_EOL;
const TAB = "   ";

function tabs($count){
	$r = "";
	for($i = 0; $i < $count; $i++){
		$r .= TAB;
	}
	return $r;
}

class HTMLSource{
	var $tabcount = 0;
	var $tabincrement = 1;

	function __construct($htmlParent){
		if(is_subclass_of($htmlParent, "HTMLSource")){
			$this->tabcount = $htmlParent->tabcount + $htmlParent->tabincrement;
		}
	}

	function addTabs($source){
		if(func_num_args() > 1){
			if(func_get_args()[1] == false){
				return $source.tabs($this->tabcount);
			}
		}

		return tabs($this->tabcount).$source;
	}
}

class HtmlAttribute{
	var $attrNames = [  0 => "style",
						1 => "border"];
	var $attrOptions = [ 0 => [ "width" => "100%",
					 			"background-color" => "#00FF00" ],
					 	 1 => [ 0 => "1px", 
					 	 		1 => "solid",
					 	 		2 => "black" ] ];

	function output(){
		$r = ""; // $this->name."='";
		foreach($this->attrNames as $i => $name){
			$r .= " ".$name."='";
			$options = $this->attrOptions[$i];
			if(is_array($options)){
				foreach($options as $key => $value){
					if(is_string($key)){
						$r .= $key.":".$value."; ";	
					}

					else if(is_int($key)){
						$r .= $value." ";
					}
					
				}
			}

			else if(is_string($options)){
				$r .= $options;
			}
			$r = rtrim($r, " ")."'";
		}

		return $r;
	}
}

class TableUI extends HTMLSource{
	var $rows = 2;
	var $columns = 2;

	function output(){
		$attribute = (new HtmlAttribute)->output();
		$r = $this->addTabs("<table ".$attribute.">").EOL;
		for($i = 0; $i < $this->rows; $i++){
			$row = new TableRowUI($this);
			$row->iColumns = $this->columns;
			$r .= $row->output();
		}
		$r .= $this->addTabs("</table>").EOL;
		return $r;
	}
}

class TableRowUI extends HTMLSource{
	var $iColumns = 2;

	function output(){
		$r = $this->addTabs("<tr>").EOL;
		for($i = 0; $i < $this->iColumns; $i++){
			$cell = new TableCellUI($this);
			$r .= $cell->output("This is a column");
		}
		$r .= $this->addTabs("</tr>").EOL;
		return $r;
	}
}

class TableCellUI extends HTMLSource{
	var $colspan = 0;
	var $rowspan = 0;
	
	function output($innerHTML){
		$r = $this->addTabs("<td>");
		$r .= $innerHTML;
		$r .= "</td>".EOL;
		return $r;
	}
}

?>


<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>Alfred's test page</title>
  <meta name="description" content="The HTML5 Herald">
  <meta name="author" content="Alfred Cepeda">

</head>
<body>
<?php
	$table = new TableUI(null);
	$table->tabcount = 1;
	echo $table->output();
?>
</body>
</html>