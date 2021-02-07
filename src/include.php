<?php
require "vendor/autoload.php";
$db = new \hellsh\smw(
	"localhost", // DB Server Address
	"root", // Username
	"", // Password
	"netdex_example" // Database
);
$netdex_app_id = "000000000example";

function signData($field, $value)
{
	$key = openssl_pkey_get_private("file:///".__DIR__."/private.pem");
	if($key === false)
	{
		http_response_code(500);
		die("Failed to load private key");
	}
	$data = $field.":".$value;
	openssl_sign($data, $sig, $key);
	if(PHP_MAJOR_VERSION < 8)
	{
		openssl_free_key($key);
	}
	return base64_encode($data.":".$sig);
}

function verifyData($field, $data)
{
	$data = explode(":", base64_decode($data), 3);
	if(count($data) != 3 || $data[0] != $field)
	{
		http_response_code(400);
		die("Invalid data for $field");
	}
	$key = openssl_pkey_get_public("file:///".__DIR__."/public.pem");
	if($key === false)
	{
		http_response_code(500);
		die("Failed to load public key");
	}
	openssl_verify($data[0].":".$data[1], $data[2], $key);
	if(PHP_MAJOR_VERSION < 8)
	{
		openssl_free_key($key);
	}
	return $data[1];
}
