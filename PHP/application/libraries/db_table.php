<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('././system/libraries/Table.php'); 

class Db_table extends CI_Table {

	protected function makeHeading($object) {
		if ($this->auto_heading === TRUE && empty($this->heading)) {
			$this->heading = $this->_prep_args($object->list_fields());
		}
	}

	public function generateDBUsingPK($table_data, $pk_name, $link, $request_param) {
		$this->makeHeading($table_data);
		$customRows = $table_data->result_array();

		if (empty($this->heading) && empty($customRows)) return NULL;
		
		$this->_compile_template();
		$postScript = '$("#db-table").replaceWith(data);';

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
		$script =
		'
		<script>
			
			function remove(id) {
				$.ajax({
				  type: '.$request_type.',
				  url: "'.$link.($linkHasParam?'&':'?').DB_REQUEST.'='.DB_DELETE.'",
				  data:
				  	'
				  	.$request_str.'
				  	"'.$token.'": "'.$hash.'",
				  	"id":id,
				  	"'.DB_GET.'":"'.BOOL_ON.'"
				  }'.',
				  success: function(data) {
				  	'.$postScript.'
				  }
				});
			}
			function update(id, value) {
				$.ajax({
				  type: '.$request_type.',
				  url: "'.$link.($linkHasParam?'&':'?').DB_REQUEST.'='.DB_UPDATE.'",
				  data: '
				  	.$request_str.'
				  	"'.$token.'": "'.$hash.'",
				  	"id":id,
				  	"'.DB_GET.'":"'.BOOL_ON.'"
				  }'.',
				  success: function(data) {
				  	'.$postScript.'
				  }
				});
			}
		</script>
		';
		$out = '<div id="db-table">'.$this->newline;
		// Build the table!
		$out .= $script.$this->newline;

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

				$out .= $this->template['cell_'.$name.'start'];
				$out .= '<button class="button" onclick="remove('.$row[$pk_name].');">remove</button>';

				$out .= $this->template['cell_'.$name.'end'];
				$out .= $this->template['row_'.$name.'end'].$this->newline;
			}

			$out .= $this->template['tbody_close'].$this->newline;
		}

		$out .= $this->template['table_close'];
		$out .= '</div>';

		// Clear table class properties before generating the table
		$this->clear();

		return $out;
	}



}
