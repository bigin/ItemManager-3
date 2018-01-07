<?php namespace Imanager;

interface InputInterface
{
	public function __construct(Field $field);

	public function prepareInput($value, $sanitize);

	public function prepareOutput();
}