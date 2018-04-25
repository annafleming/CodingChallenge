<?php

/*
Challenge 3: Use reflection to get access to Question::$answer from $e->getAnswer
*/

class Question
{
	private $answer = 42;

	public function __construct($e)
	{
		try {
			throw $e;
		} catch (Exception $e) {
			echo $e->getAnswer($this) . PHP_EOL;
		}
	}
}

// start editing here

class MyException extends Exception
{

	public function __call($method, $instance)
	{
		$propertyName = $this->getPropertyNameFromGetter($method);

		return $this->getPrivateProperty(current($instance), $propertyName);
	}

	private function getPrivateProperty($instance, $propertyName)
	{
		$reflection = new ReflectionObject($instance);
		if (!$reflection->hasProperty($propertyName))
		{
			$className = get_class($instance);
			throw new Exception("Property {$propertyName} is missing in {$className}");
		}

		$property = $reflection->getProperty($propertyName);
		$property->setAccessible(true);
		return $property->getValue($instance);
	}

	private function isGetter($method)
	{
		return substr(strtolower($method), 0, 3 ) === "get";
	}

	private function getPropertyNameFromGetter($method)
	{
		if (!$this->isGetter($method))
		{
			throw new Exception("Method {$method} is not a getter");
		}
		return substr(strtolower($method), 3);
	}
}

$e = new MyException;

// end editing here

new Question($e);
