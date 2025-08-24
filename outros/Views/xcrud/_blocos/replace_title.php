<?php
if (in_array($this->task, array('edit','view')) && $this->get_var('replace_title') != "") {
	$title = $this->get_var('replace_title');
	preg_match_all("/\{(.*?)\}/", $title, $matches);
	$matches = $matches[1];
	foreach ($matches as $macro) {
		if (! strpos($macro, '.')) {
			$field = $this->table . "." . $macro;
		} else
			$field = $macro;
			if (isset($this->fields_output[$field])) {
				$title = str_replace('{' . $macro . '}', $this->fields_output[$field]['value'], $title);
			}
	}
} else
	$title = false;

?>