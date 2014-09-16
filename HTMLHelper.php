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

class HTMLAttributes{
	var $attributeDict;

	function output(){
		$r = "";
		foreach($this->attributeDict as $key => $value){
			$r .= " ".$key."='";
			$options = $value;
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
	var $tableRows; 
	var $htmlAttributes;
	var $datasource;

	function output(){
		$attribute = "";

		if($this->htmlAttributes instanceof HTMLAttributes ){
			$attribute = $this->htmlAttributes->output();
		}

		$r = $this->addTabs("<table ".$attribute.">").EOL;
		if($this->datasource instanceof TableUIDatasource){
			for($i = 0; $i < $this->datasource->numberOfRows(); $i++){
				$r .= $this->datasource->rowOutputForIndex($i);
			}
		}

		else if(is_array($this->tableRows)){
			foreach($this->tableRows as $row){
				$r .= $row->output();
			}
		}

		$r .= $this->addTabs("</table>").EOL;
		return $r;
	}
}

class TableRowUI extends HTMLSource{
	var $tableCells;

	function output(){
		$r = $this->addTabs("<tr>").EOL;

		foreach($this->tableCells as $cell){
			$r .= $cell->output();
		}

		$r .= $this->addTabs("</tr>").EOL;
		return $r;
	}
}

class TableCellUI extends HTMLSource{
	var $innerHTML = "Empty cell";
	
	function output(){
		$r = $this->addTabs("<td>");
		$r .= $this->innerHTML;
		$r .= "</td>".EOL;
		return $r;
	}
}

interface TableUIDatasource{
	public function rowOutputForIndex($index);
	public function numberOfRows();
}	

class ExampleTableDatasource implements TableUIDatasource{
	var $rows = [ ];

	function __construct($table){
		for($i = 0; $i < 1000; $i++){
			$row = new TableRowUI($table);
			$cell1 = new TableCellUI($row);
			$cell2 = new TableCellUI($row);
			$cell3 = new TableCellUI($row);
			$cell1->innerHTML = "This is cell".($i++ +1);
			$cell2->innerHTML = "This is cell".($i++ +1);
			$cell3->innerHTML = "This is cell".($i +1);
			$row->tableCells = [ $cell1, $cell2, $cell3 ];
			array_push($this->rows, $row);
		}
	}

	function rowOutputForIndex($index){
		$row = $this->rows[$index];
		return $row->output();
	}

	function numberOfRows(){
		return count($this->rows);
	}
}
?>


<!DOCTYPE html>

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

	$test = [ "style" => [ "hello" => "World" ]];

	$attributes = new HTMLAttributes();
	$attributes->attributeDict = [ 	
									"style" => [ "width" => "100%", "background-color" => "#00FF00" ],
					 				"border" =>  "1px solid black"
					 			 ];

	$table->htmlAttributes = $attributes;
	$datasource = new ExampleTableDatasource($table);
	$table->datasource = $datasource;

	echo $table->output();
?>
</body>
</html>