#!/usr/bin/php
<?php

require_once "HTTPRequest.php";
require_once "HTTPPostRequest.php";


if( $argc == 2 )
{
	$method = "GET";
	$file = $argv[1];
	$domain = "localhost";
	$port = 8000;
}
else{

	if( count( $argv ) < 3 )
	{
		echo "METHOD FILE [DOMAIN : PORT]";
		die();
	}
	
	$method = $argv[1];
	$file = $argv[2];
	$domain = ( isset( $argv[3] ) ) ? $argv[3]  : "localhost";
	$port =  ( isset( $argv[4] ) ) ? $argv[4]  : "80";
}


$error = "";

$request = new HTTPPostRequest( $method );
$request->open( $domain , $port );

if( !$request->isOpen() )
{
	echo $request->getError();
	echo "\n";
	die();
}




if( $request->isOpen() )
{
	$request->setParameters( array( "t"  => 1 , "s" => "2" , "f" => 12) );
	$request->send( $file );
	$request->setResponseHeadersContent();
	$request->setContent();
	$request->close();

	echo "#### HEADERS ######\n";

	echo $request->headers_string;
	echo "#### CONTENT ######\n";
	echo $request->content_string;
	echo "#### RESPONSE HEADERS ######\n";
	print_r( $request->RESPONSE_HEADERS );
	echo "#### REQUEST_HEADERS ######\n";
	print_r( $request->REQUEST_HEADERS );

}