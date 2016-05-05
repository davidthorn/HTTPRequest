<?php

require_once "HTTPRequest.php";

interface IHTTPPostRequest
{
	function setParameters( array $params );
}


class HTTPPostRequest extends HTTPRequest implements  IHTTPPostRequest
{

	public $params = array();

	public function __construct(  )
	{
		parent::__construct("POST");
	}


	public function setParameters( array $params ){
		$this->params = $params;
	}

	public function send( $file  )
	{

		$header = $this->method . " " . $file . " HTTP/1.1";
		
		if( $this->isOpen() ){

			$this->writeln( $header );

			$this->setRequestHeader( "Host" , $this->host );
			$this->setRequestHeader( "Content-Type" , "application/x-www-form-urlencoded" );

			$str = array();
			foreach( $this->params as $k => $v )
			{
				$l = $k . "=" . $v;
				$str[] = $l;
			}
			
			$data = implode( "&" , $str );

			$this->setRequestHeader("Content-Length" , strlen($data));

			foreach( $this->REQUEST_HEADERS as $k => $v )
			{
				$this->writeln( $k . ": $v" );
			}

			$this->write( "\n" );
			$this->write( $data );
		}

	}


}