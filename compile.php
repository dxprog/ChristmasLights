<?php

$data = [];

include('serializexml.php');

class Frame {
	public $timestamp;
	public $actions = [];
	
	public function __construct($timestamp) {
		$this->timestamp = $timestamp;
	}
	
	public function addAction($stringId, $transform, $value) {
		$action = new stdClass;
		$action->stringId = $stringId;
		$action->transform = $transform;
		$action->value = (int)$value;
		$this->actions[] = $action;
	}
	
}

function parseLightFile($file) {

	$rows = file($file);
	$retVal = [];
	$frameDelay = 0;
	$transform = '';
	$stringId = 0;
	
	for ($i = 0, $count = count($rows); $i < $count; $i++) {
		$row = trim($rows[$i]);
		$params = explode("\t", $row);
		
		switch ($params[0]) {
			case 'Units Per Second':
				$frameDelay = 1000 / (int)$params[1];
				break;
			case 'String ID':
				$stringId = (int)$params[1];
				break;
			case 'Transform':
				$transform = strtolower($params[1]);
				break;
			case 'Frame':
			case '':
				break;
			case 'End of Keyframe Data':
				$transform = '';
			default:
				if ($transform) {
					$time = round((int)$params[0] * $frameDelay);
					if (!isset($retVal[$time])) {
						$retVal[$time] = new Frame($time);
					}
					$retVal[$time]->addAction($stringId, $transform, $params[1]);
				}
				break;
		}
		
	}
	
	return $retVal;

}

$commands = parseLightFile('holy_night.txt');
ksort($commands);

echo Lib\SerializeXML::serialize($commands, 'Frames');

file_put_contents('holy_night.js', json_encode(array_values($commands)));