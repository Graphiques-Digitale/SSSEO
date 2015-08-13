<?php

/**
 *
 * @author 4t4r1
 *
 */
class OpenGraphAdaptor() {

	//
	private static $types = array(
		'article'
	);

	//
	private $type;
	private $title;
	private $description;

	//
	public function __construct($data = null, $image = null) {
		//
		$vars = $this->decode($data, true);
		//
		$this->type = (array_key_exists('type', $vars)) ? $vars['type'] : null;
		$this->title = (array_key_exists('title', $vars)) ? $vars['title'] : null;
		$this->description = (array_key_exists('description', $vars)) ? $vars['description'] : null;
		//

	}

	//
	public function decode($data) {
		//
		$values = json_decode($data);
		//

	}

	//
	public function encode() {
		//

		//
		return $data;
	}

	//
	public function CMSFields() {

	}

}
