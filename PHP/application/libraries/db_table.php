<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('././system/libraries/Table.php'); 

class Db_table extends CI_Table {

	protected function makeHeading($object) {
		if ($this->auto_heading === TRUE && empty($this->heading))
		{
			$this->heading = $this->_prep_args($object->list_fields());
		}
	}

	public function generateDBUsingPK($table_data, $pk, $link, $table)
	{
		$this->makeHeading($table_data);

		$customRows = $table_data->result_array();

		// Is there anything to display? No? Smite them!
		if (empty($this->heading) && empty($this->rows))
		{
			return 'Undefined table data';
		}

		// Compile and validate the template date
		$this->_compile_template();

		// Validate a possibly existing custom cell manipulation function
		if (isset($this->function) && ! is_callable($this->function))
		{
			$this->function = NULL;
		}

		$name = html_escape($table);
		$script =
		'
		<script>
			function remove(id) {
				$.ajax({
				  type: "GET",
				  url: "'.$link.'",
				  data: {
				  	"t":"'.$name.'",
				  	"s":"r",
				  	"id":id
				  },
				  success: function(data) {
				  	window.location = "'.$link.'?t='.$name.'";
				  }
				});
			}
		</script>
		';

		// Build the table!
		$out = $script.$this->newline;

		$out .= $this->template['table_open'].$this->newline;

		// Add any caption here
		if ($this->caption)
		{
			$out .= '<caption>'.$this->caption.'</caption>'.$this->newline;
		}

		// Is there a table heading to display?
		if ( ! empty($this->heading))
		{
			$out .= $this->template['thead_open'].$this->newline.$this->template['heading_row_start'].$this->newline;

			foreach ($this->heading as $heading)
			{
				$temp = $this->template['heading_cell_start'];

				foreach ($heading as $key => $val)
				{
					if ($key !== 'data')
					{
						$temp = str_replace('<th', '<th '.$key.'="'.$val.'"', $temp);
					}
				}

				$out .= $temp.(isset($heading['data']) ? $heading['data'] : '').$this->template['heading_cell_end'];
			}

			$out .= $this->template['heading_row_end'].$this->newline.$this->template['thead_close'].$this->newline;
		}

		// Build the table rows
		if ( ! empty($customRows))
		{
			$out .= $this->template['tbody_open'].$this->newline;

			$i = 1;
			foreach ($customRows as $row)
			{

				// We use modulus to alternate the row colors
				$name = fmod($i++, 2) ? '' : 'alt_';

				$out .= $this->template['row_'.$name.'start'].$this->newline;

				foreach ($row as $key => $cell)
				{
					$temp = $this->template['cell_'.$name.'start'];

					$cell = isset($cell) ? $cell : '';
					$out .= $temp;

					if ($cell === '' OR $cell === NULL)
					{
						$out .= $this->empty_cells;
					}
					elseif (isset($this->function))
					{
						$out .= call_user_func($this->function, $cell);
					}
					else
					{
						$out .= $cell;
					}

					$out .= $this->template['cell_'.$name.'end'];
				}

				$out .= $this->template['cell_'.$name.'start'];
				$out .= '<button onclick="remove('.$row[$pk].');">remove</button>';
				$out .= $this->template['cell_'.$name.'end'];


				$out .= $this->template['row_'.$name.'end'].$this->newline;
			}

			$out .= $this->template['tbody_close'].$this->newline;
		}

		$out .= $this->template['table_close'];

		// Clear table class properties before generating the table
		$this->clear();

		return $out;
	}

}
