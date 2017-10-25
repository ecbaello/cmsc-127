<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('././system/libraries/Table.php'); 

class Db_table extends CI_Table {

	protected function makeHeading($object) {
		if ($this->auto_heading === TRUE && empty($this->heading)) {
			$this->heading = $this->_prep_args($object->list_fields());
		}
	}

	public function generateDBUsingPK($table_data, $pk_name, $link, $request_param, $associations = array()) {

		if (empty($table_data)) return;

		$it_key = 'key';
		$header = array();
		$fields = $table_data->list_fields();


		foreach ($fields as $field) {
			$item = array();

			$item[$it_key]  = $field;
			if (isset($associations[$field])) 
				foreach ($associations[$field] as $skey => $value) {
					$item[$skey] = $value;
				}
			else {
				$item[TBL_TITLE] = '';
				$item[TBL_INPUT] = 'TEXT';
			}
			array_push($header, $item);
		}

		$heading = array();

		foreach ($header as $item) {
			array_push($heading, $item[TBL_TITLE]);
		}

		$this->set_heading($heading);

		$postScript = 'function (data) {$("#container").replaceWith(data);}';

		$i = 0;
		$tabledit = 'onSuccess: '.$postScript.', update: "'.DB_UPDATE.'", delete: "'.DB_DELETE.'",';
		$tabledit .= 'columns: {';
		$identifier = 'identifier: ';
		$editable = 'editable: [';

		$firstEditable = TRUE;


		foreach ($header as $item) {
			if ($item[$it_key]==$pk_name) {
				$identifier .=' ['.$i.', "'.DB_PKWORD.'"]';
			} else {
				if (!$firstEditable) $editable .= ',';
				$editable .= ' ['.$i.', "'.$item[$it_key].'", \'{';
				switch ($item[TBL_INPUT]) {
					case 'PASSWORD':
						$editable .= '"type":"password", "data-validation":"length alphanumeric strength", "data-validation-length":"min8", "data-validation-allowing":"-_", "data-validation-strength":"2"';
						break;

					case 'URL':
						$editable .= '"type":"url", "data-validation":"url"';
						break;

					case 'EMAIL':
						$editable .= '"type":"email", "data-validation":"email"';
						break;

					case 'CHECKBOX':
						$editable .= '"type":"checkbox"';
						break;

					case 'TEXTAREA':
						$editable .= '"type":"text"';
						break;

					case 'DATE':
						$editable .= '"type":"text", "data-validation":"date"';
						break;

					case 'NUMBER':
						$editable .= '"type":"number", "data-validation":"number"';
						break;

					case 'FLOAT':
						$editable .= '"type":"number", "data-validation":"number", "step":"0.01", "data-validation-allowing":"float"';
						break;
					
					default:
						break;
				}
				$editable .= '}\']';
				$firstEditable = FALSE;
			}
			
			$i++;
		}

		$editable .= ']';

		$tabledit .= $identifier .', '. $editable . '}';


		$this->makeHeading($table_data);
		$customRows = $table_data->result_array();

		if (empty($this->heading) && empty($customRows)) return NULL;
		
		$this->_compile_template();
		

		// Make request parameters
		$request_str = "{";
		if (!empty($request_param))
			foreach ($request_param as $key => $value) {
				$request_str .= '"'.$key.'":"'.$value.'",'.$this->newline;
			}

		// Determine request type
		$request_type = "'POST'";

		// To get csrf
		$ci =& get_instance();
		$token = $ci->security->get_csrf_token_name();
		$hash = $ci->security->get_csrf_hash();

		$linkHasParam = strpos($link, '?') !== false;

		// Create the script for ui queries
		// $script =
		// '
		// <script>
		// 	function remove(id) {
		// 		$.ajax({
		// 		  type: '.$request_type.',
		// 		  url: "'.$link.($linkHasParam?'&':'?').DB_REQUEST.'='.DB_DELETE.'",
		// 		  data:
		// 		  	'
		// 		  	.$request_str.'
		// 		  	"'.$token.'": "'.$hash.'",
		// 		  	"id":id,
		// 		  	"'.DB_GET.'":"'.BOOL_ON.'"
		// 		  }'.',
		// 		  success: function(data) {
		// 		  	'.$postScript.'
		// 		  }
		// 		});
		// 	}
		// 	function update(id, value) {
		// 		$.ajax({
		// 		  type: '.$request_type.',
		// 		  url: "'.$link.($linkHasParam?'&':'?').DB_REQUEST.'='.DB_UPDATE.'",
		// 		  data: '
		// 		  	.$request_str.'
		// 		  	"'.$token.'": "'.$hash.'",
		// 		  	"id":id,
		// 		  	"'.DB_GET.'":"'.BOOL_ON.'"
		// 		  }'.',
		// 		  success: function(data) {
		// 		  	'.$postScript.'
		// 		  }
		// 		});
		// 	}
		// </script>
		// ';
		$out = '<div id="db-table">'.$this->newline;
		// Build the table!
		//$out .= $script.$this->newline;
		$out .= '<form class="tabledit-form" action="'.$link.'" method="post">';
		$out .= '<input type="hidden" name="'.$token.'" value="'.$hash.'">';
		$out .= '<input type="hidden" name="'.DB_GET.'" value="'.BOOL_ON.'">';
		$out .= $this->template['table_open'].$this->newline;

		// Add any caption here
		if ($this->caption) {
			$out .= '<caption>'.$this->caption.'</caption>'.$this->newline;
		}

		// Is there a table heading to display?
		if ( ! empty($this->heading)) {
			$out .= $this->template['thead_open'].$this->newline.$this->template['heading_row_start'].$this->newline;

			foreach ($this->heading as $heading) {
				$temp = $this->template['heading_cell_start'];

				foreach ($heading as $key => $val) {
					if ($key !== 'data') {
						$temp = str_replace('<th', '<th '.$key.'="'.$val.'"', $temp);
					}
				}

				$out .= $temp.(isset($heading['data']) ? $heading['data'] : '').$this->template['heading_cell_end'];
			}

			$out .= $this->template['heading_cell_start'].$this->template['heading_cell_end'];
			$out .= $this->template['heading_row_end'].$this->newline.$this->template['thead_close'].$this->newline;
		}

		// Build the table rows
		if ( ! empty($customRows)) {
			$out .= $this->template['tbody_open'].$this->newline;
			$i = 1;
			foreach ($customRows as $row) {
				// We use modulus to alternate the row colors
				$name = fmod($i++, 2) ? '' : 'alt_';
				$out .= $this->template['row_'.$name.'start'].$this->newline;

				foreach ($row as $key => $cell) {
					$temp = $this->template['cell_'.$name.'start'];
					$temp = str_replace('>', ' data-key = "'.$key.'">', $temp);
					$cell = isset($cell) ? $cell : '';
					$out .= $temp;
					if ($cell === '' OR $cell === NULL) {
						$out .= $this->empty_cells;
					}
					else {
						$out .= $cell;
					}
					$out .= $this->template['cell_'.$name.'end'];
				}

				// $out .= $this->template['cell_'.$name.'start'];
				// $out .= '<button class="button" onclick="remove('.$row[$pk_name].');">remove</button>';

				// $out .= $this->template['cell_'.$name.'end'];
				$out .= $this->template['row_'.$name.'end'].$this->newline;
			}

			$out .= $this->template['tbody_close'].$this->newline;
		}

		$out .= $this->template['table_close'];
		$out .= '</form>';
		$out .= '</div>';

		$out .= '<script>$("#db-table table").Tabledit({'.$tabledit.'});</script>';

		// Clear table class properties before generating the table
		$this->clear();

		return $out;
	}



}
