<?php

interface ISocket
{

	function open(  $host , $port  );

	/*
		@return bool
	*/
	function isOpen(  );
	function close();


	function getError();

	function write( $string );
	function writeln( $string );

	function readln(  ); // reads until a line break
	function getc(); //returns a single char
	function read(); // reads and collects all
	function feof();


}

class Socket implements ISocket
{

	
	public $host = null;

	public $port = null;

	public $socket = null;

	public $error_no = null;

	public $error_str = null;

	

	public function __construct(  )
	{
	}

	public function open( $host , $port )
	{
		$this->host = $host;
		$this->port = $port;
		try{
			$sock = @fsockopen( $this->host, $this->port , $this->error_no , $this->error_str );

			if( $sock )
			{
				$this->socket = $sock;
				return $this;
			}
			else{
				throw new Exception( "Socket could not be opened | Error: " . $this->error_str );
			}
		}catch( Exception $ex )
		{
			return $this;
		}
			
	}

	

	public function isOpen(){
		$open = ( $this->socket != null ) ? true : false;
		if( !$open )
		{
			echo "Socket closed";
		}
		return $open;
	}

	public function close(){
		if( $this->isOpen() )
		{
			fclose( $this->socket );
			return true;
		}

		return false;

	}

	public function getError(){
		return $this->error_str;
	}

	public function writeln( $string )
	{
		if( $this->isOpen() )
		{
			$this->write( $string . "\n" );
		}

		return $this;
	}

	public function write( $string )
	{
		if( $this->isOpen() )
		{
			fwrite( $this->socket , $string  );
		}


		return $this;
	}


	public function getc(){

		if( $this->isOpen() ){
			return fgetc( $this->socket );
		}

		return null;

	}

	function readln(  ) // reads until a line break
	{
		$content = "";
		if( $this->isOpen() )
		{
			while( !$this->feof() )
			{
				$c = $this->getc();
				if( $c == "\n" )
				{
					$content .= $c;
					return $content;
				}
				else{
					$content .= $c;
				}

				
			}
		}

		return $content;
	}
	
	function read(){

		$content = "";
		if( $this->isOpen() )
		{
			while( !$this->feof() )
			{
				$content .= $this->getc();
			}
		}

		return $content;
		
	}


	public function feof(){
		if( $this->isOpen() ){
			return feof( $this->socket );
		}

		return true;
	}





}