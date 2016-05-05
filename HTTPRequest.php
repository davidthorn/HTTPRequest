<?php

require_once "Socket.php";

interface IHTTPRequest
{

	function send( $file );
	
	function setResponseHeadersContent(); // sets the string content of the returned headers from the response
	function setResponseHeaders(  $first_header ,   $headers );

	function getRequestHeaders();
	function getResponseHeaders();

	function setRequestHeader( $key , $value );

}



class HTTPRequest extends Socket implements IHTTPRequest
{

	public $method = null;


	public $headers_string = null;

	public $content_string = null;

	public $RESPONSE_HEADERS = array();
	public $REQUEST_HEADERS = array();

	public $statusCode = null;

	public $statusText = null;

	public $httpVersion = null;

	public function __construct( $method ){
		$this->method = $method;
		parent::__construct();
	}

	
	public function send( $file ){
		
		$header = $this->method . " " . $file . " HTTP/1.1";
		
		if( $this->isOpen() ){

			$this->writeln( $header );

			$this->setRequestHeader( "Host" , $this->host );
			$this->setRequestHeader( "Content-Type" , "text/plain" );

			foreach( $this->REQUEST_HEADERS as $k => $v )
			{
				$this->writeln( $k . ": $v" );
			}

			$this->write( "\n" );
		}
		
	}

	public function setResponseHeadersContent(){
	
		if( $this->isOpen() )
		{
			$lb = 0;
			$first_header_complete =  false;
			$first_header = "";
			$headers_complete = false;
			$headers = "";
			$array = array();
			
			while( !$this->feof() )
			{
				//$line = fget( $sock );
				$c = $this->getc();

				if( $c == "\n" && !$headers_complete )
				{

					if( $lb == 0 )
					{
						$lb += 1;
						$headers .= $c;
						$this->headers_string .= $c;
						if( $first_header_complete )
						{
							$array[] = $headers;
							$headers = "";
						}
						else{
							$first_header_complete = true;
							$first_header = $headers;
							$headers = "";
						}
						

					}
					else if ( $lb == 1 ){
						$headers_complete = true;
						//$this->headers_string .= $c;
						break;
					}
					//echo "LINE BREAK";
				}
				else if( !$headers_complete && $c == "\r" )
				{
					$this->headers_string .= $c;
					$headers .= $c;
				}
				else if( !$headers_complete )
				{
					$this->headers_string .= $c;
					$headers .= $c;
					$lb = 0;
				}
				
			}

			//echo "FIRST HEADER: $first_header";

			$this->setResponseHeaders( $first_header , $array );
			return true;
		}
	
		return false;
	}

	public function setContent(){
		if( $this->isOpen() )
		{
			while( !$this->feof() )
			{
				$c = $this->getc();
				$this->content_string .= $c;
			}
		}
		
	}


	public function setResponseHeaders( $first_header ,   $headers )
	{

		$first_header = str_replace("\n", "", $first_header);
		$first_header = str_replace("\r", "", $first_header);

		if( preg_match( "!(HTTP/[\d]\.[\d]) ([\d]+) (.*)!" , $first_header , $matches ) )
		{
			$this->statusText = $matches[3];
			$this->statusCode = $matches[2];
			$this->httpVersion = $matches[1];
		}

		if( !is_array( $headers ) )
		{
			return;
		}	

		foreach( $headers as $v )
		{
			echo $v;
			$v = str_replace("\n", "", $v);
			$v = str_replace("\r", "", $v);
			$e = explode( ":" , $v );
			$this->RESPONSE_HEADERS[$e[0]] = $e[1];
		}

	}

	public function getResponseHeaders(){
		return $this->RESPONSE_HEADERS;
	}

	public function getRequestHeaders(){
		return $this->REQUEST_HEADERS;
	}

	public function setRequestHeader( $key , $value ){
		$this->REQUEST_HEADERS[ $key ] = $value;
	}

	public function read(){
		if( $this->isOpen() )
		{
			$this->setHeadersContent();
			$this->setContent();
		}

		return $this->content_string;
		

	}

}
