<?php

namespace Infira\Error;

class AlertException extends \Exception
{
	private mixed $data = null;
	
	public function __construct($message = "", mixed $data = null)
	{
		parent::__construct($message, E_USER_ERROR);
		$this->data = $data;
	}
	
	public function getData(): mixed
	{
		return $this->data;
	}
	
}