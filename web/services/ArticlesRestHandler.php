<?php
require_once("SimpleRest.php");
		
class ArticlesRestHandler extends SimpleRest {
//var_dump()

	public function handleGet($get) {
		var_dump($get);
	}

	public function handlePost($post) {
		var_dump($post);
	}

}
?>