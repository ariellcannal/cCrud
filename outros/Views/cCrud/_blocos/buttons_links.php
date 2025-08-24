<?php
$link_modes[] = 'edit';
$link_modes[] = 'view';
$link_modes[] = 'create';
foreach($link_modes as $link_mode){
	if ($this->task == $link_mode && is_array($this->get_var('link_buttons_'.$link_mode))) {
		foreach ($this->get_var('link_buttons_'.$link_mode) as $button) {
			foreach ($button as $k => $v) {
				if(is_string($button[$k])){
					preg_match_all("/\{(.*?)\}/", $button[$k], $matches);
					$matches = $matches[1];
					foreach ($matches as $macro) {
						if (isset($this->fields_output[$this->table . '.' . $macro]['value'])) {
							$button[$k] = str_replace('{' . $macro . '}', $this->fields_output[$this->table . '.' . $macro]['value'], $button[$k]);
						}
					}
				}
				else{
					foreach($button[$k] as $k2=>$i){
						if(is_string($i)){
							preg_match_all("/\{(.*?)\}/", $i, $matches);
							$matches = $matches[1];
							foreach ($matches as $macro) {
								if (isset($this->fields_output[$this->table . '.' . $macro]['value'])) {
									$button[$k][$k2] = str_replace('{' . $macro . '}', $this->fields_output[$this->table . '.' . $macro]['value'], $i);
								}
							}
						}
					}
				}
			}
			$attr = "";
			if (isset($button['attr']) && is_array($button['attr'])) {
				foreach ($button['attr'] as $k => $v) {
					$attr .= " " . $k . "='" . $v . "' ";
				}
			}
			if(isset($button['icone'])){
				$button['icone'] = '<i class'.$button['icone'].'"></i>&nbsp;';
			}
			else{
				$button['icone'] = '';
			}
			if (isset($button['type']) && $button['type'] == "dropdown") {?>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" <?php echo $attr?>>
					<i class="<?php echo $button['icon']?>"></i>
					<span><?php echo $button['label']?></span>
				</a>
				<ul class="dropdown-menu pull-right">
				<?php foreach($button['list_links'] as $item):?>
	                <li><?php echo $item?></li>
	            <?php endforeach;?>
	            </ul>
			</li>
			<?php
			} else {
				?><a href="<?php echo $button['url'] ?>" <?php echo $attr?>><?php echo $button['icone']?><span><?php echo $button['label']?></span></a><?php
			}
		}
	}
}
?>