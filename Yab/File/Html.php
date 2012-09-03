<?php
/**
 * Yab Framework
 *  
 * @category   Yab_File
 * @package    Yab_File_Html
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_File_Html extends Yab_File_Csv {

	protected function getContent() {

		if($this->_content !== null)
			return $this->_content;
	
		$filter_html = new Yab_Filter_Html();
		
		$headers = false;
		
		$this->_content = '<table cellpadding="1" cellspacing="1" border="1">'.$this->getLineEnding();

		$datas = $this->getDatas();
		$fields = $this->getFields();

		foreach($datas as $data) {
		
			if(!$headers) {

				$this->_content .= "\t".'<tr>'.$this->getLineEnding();
			
				foreach($fields as $key => $value) {

					if(is_numeric($key))
						$key = $value;

					$this->_content .= "\t\t".'<th>'.htmlspecialchars($key, ENT_QUOTES, $this->getEncoding()).'</th>'.$this->getLineEnding();

				}

				$this->_content .= "\t".'</tr>'.$this->getLineEnding();
				
				$headers = true;
			
			}

			$this->_content .= "\t".'<tr>'.$this->getLineEnding();
		
			foreach($fields as $key => $value) {

				if(is_numeric($key))
					$key = $value;

				$data[$key] = $this->_callback($data, $key);

				$this->_content .= "\t\t".'<td>'.htmlspecialchars(is_array($data[$key]) ? implode(', ', $data[$key]) : $data[$key], ENT_QUOTES, $this->getEncoding()).'</td>'.$this->getLineEnding();

			}
		
			$this->_content .= "\t".'</tr>'.$this->getLineEnding();

		}
		
		$this->_content .= '</table>'.$this->getLineEnding();

		$this->_content = $this->encode($this->_content);
		$this->_content = $this->convert($this->_content);

		return $this->_content;

	}

}

// Do not clause PHP tags unless it is really necessary