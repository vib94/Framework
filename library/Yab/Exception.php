<?php
/**
* Yab Framework
*
* @category   Yab
* @package    Yab_Exception
* @author     Yann BELLUZZI
* @copyright  (c) 2010 YBellu
* @license    http://www.ybellu.com/yab-framework/license.html
* @link       http://www.ybellu.com/yab-framework 
*/

class Yab_Exception extends Exception {

	const CODE_OFFSET = 10;

	private $_nb_traces = 0;

	public function __toString() {

		if(PHP_SAPI == 'cli')
			return $this->getMessage().PHP_EOL;

		return $this->getHtml();

	}
	
	public function getHtml() {

		$render = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'.PHP_EOL;
		$render .= '<html>'.PHP_EOL;
		$render .= '<head>'.PHP_EOL;
		$render .= '<title>A '.get_class($this).' has been caught !</title>'.PHP_EOL;
		$render .= '<style type="text/css">'.PHP_EOL;
		$render .= "\t".'h1 {font-size: 18px; margin: 0px; padding: 0px}'.PHP_EOL;
		$render .= "\t".'h1 span.exceptionClass {color: #ff6040}'.PHP_EOL;
		$render .= "\t".'h2 {font-size: 14px; margin: 10px 0px; padding: 0px; color: #ffb000}'.PHP_EOL;
		$render .= "\t".'h3 {font-size: 14px; text-decoration: underline}'.PHP_EOL;
		$render .= "\t".'strong {font-size: 12px; text-decoration: underline}'.PHP_EOL;
		$render .= "\t".'div#trace {margin: 10px 0px; padding: 0px}'.PHP_EOL;
		$render .= "\t".'div#trace a {text-decoration: none; color: #1c66ad}'.PHP_EOL;
		$render .= "\t".'div#trace a:hover {text-decoration: underline}'.PHP_EOL;
		$render .= "\t".'div#trace div.trace_file {background-color: #ffffcc; margin: 20px 0px }'.PHP_EOL;
		$render .= "\t".'div#trace div.trace_file p {border-bottom: 1px solid #dddddd; margin: 0px; padding: 0px}'.PHP_EOL;
		$render .= "\t".'div#trace div.trace_file p.current {background-color: #ff9966}'.PHP_EOL;
		$render .= "\t".'div#trace div.trace_file span.line_number {display: inline-block; width: 35px; background-color: #cccccc; padding: 0 5px; margin-right: 5px;}'.PHP_EOL;
		$render .= "\t".'div#trace div.trace_file span.current {background-color: #ff9966; border-right: 1px solid #dddddd;}'.PHP_EOL;
		$render .= "\t".'div#trace div.trace_file span.comment {display: inline-block; color: #cccccc;}'.PHP_EOL;
		$render .= '</style>'.PHP_EOL;
		$render .= '</head>'.PHP_EOL;
		$render .= '<body>'.PHP_EOL;
		$render .= '<pre><h1>A <span class="exceptionClass">'.get_class($this).'</span> has been caught !</h1><h2>&gt; '.htmlspecialchars($this->getMessage()).'</h2><strong>Traces</strong> : <div id="trace">';

		$render .= $this->trace($this->getFile(), $this->getLine(), '', '', array(), true);

		foreach($this->getTrace() as $trace) 
			$render .= $this->trace(array_key_exists('file', $trace) ? $trace['file'] : '', array_key_exists('line', $trace) ? $trace['line'] : '', array_key_exists('class', $trace) ? $trace['class'] : '', array_key_exists('function', $trace) ? $trace['function'] : '', array_key_exists('args', $trace) ? $trace['args'] : '');
    
		$render .= '</div></pre></body></html>';

		return $render;
	
	}

	public function trace($file, $line, $class, $function, $args, $display = false) {

		$this->_nb_traces = $this->_nb_traces + 1; 

		$trace = '<div>';
		$trace .= '<a href="#trace'.$this->_nb_traces.'" onclick="document.getElementById(\'trace'.$this->_nb_traces.'\').style.display = document.getElementById(\'trace'.$this->_nb_traces.'\').style.display != \'block\' ?  \'block\' : \'none\'; return false;" title="Toggle trace !">'.($file ? $file : 'unknown').' (L: '.($line ? $line : 'unknown').')</a>';
		$trace .= '<div id="trace'.$this->_nb_traces.'" style="padding-top: 10px; display: '.(!$display ? 'none' : 'block').'">';
		
		if($class)
			$trace .= '<strong>Class</strong> : '.$class.'<br />';
		
		if($function) {
			
			$nb_args = count($args);
		
			$trace .= '<strong>Method</strong> : '.$function.'<br />';
			
			if($nb_args) {

				for($i = 0; $i < $nb_args; $i++)
					$trace .= '<strong>Arg '.($i + 1).'</strong> : <p>'.Yab_Loader::getInstance()->dump($args[$i]).'</p>';
					
			} else {
			
				$trace .= '<strong>No arg</strong><br />';
				
			}
			
		}
		
		$trace .= ($file && $line ? '<strong>Source file</strong> : <div class="trace_file">'.$this->traceFile($file, $line, $class, $function, $args).'</div>' : '');
		$trace .= '</div></div>';

		return $trace;
		
	}

	public function traceFile($file, $line_number, $class, $function, $args) {

		if($function == '__construct')
			$function = $class;

		$lines = file($file);

		$start = max(0, $line_number - self::CODE_OFFSET - 1);
		$offset = min(count($lines), $line_number - $start + self::CODE_OFFSET);

		$trace_file = '';

		$lines = array_slice($lines, $start, $offset);

		foreach($lines as $number => $line) {

			$number = $start + $number + 1;

			$trace_line = trim(preg_replace('#^<span[^>]+>(.*)</span>$#uis', '$1', trim(strip_tags(str_replace('&lt;?php&nbsp;', '', trim(highlight_string('<?php '.$line, true))), '<span>'))));

			$trace_file .= '<p'.($number == $line_number ? ' class="current"' : '').'><span class="line_number'.($number == $line_number ? ' current"' : '').'">'.$number.'</span>'.$trace_line.'</p>';

		}

		return $trace_file;

	}
	
}

// Do not clause PHP tags unless it is really necessary