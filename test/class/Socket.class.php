<?php 
class Socket
{
	private $service_port;
	private $ip_address;
	private $socket;	
	
	function __construct(){
		$this->service_port = getservbyname('www', 'tcp');//port 80
		$this->ip_address = gethostbyname('oyvent.com');
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);	
		if ($socket === false) {
    		echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
		} else {
    		echo "OK.\n";
		}
		
	}
	
		
	public function connect(){
		echo "Attempting to connect to '$this->ip_address' on port '$this->service_port'...";
		$result = socket_connect($this->socket, $this->ip_address, $this->service_port);
		if ($result === false) {
    		echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
		} else {
    		echo "OK.\n";
		}
	}
	
	public function send(){
		
		$this->connect();
		
		$in = "HEAD / HTTP/1.1\r\n";
		$in .= "Host: oyvent.com\r\n";
		$in .= "Connection: Close\r\n\r\n";
		$out = '';

		echo "Sending HTTP HEAD request...";
		socket_write($this->socket, $in, strlen($in));
		echo "OK.\n";

		echo "Reading response:\n\n";
		while ($out = socket_read($this->socket, 2048)) {
    		echo $out;
		}
		
		$this->close();
	}
	
	public function close(){
		echo "Closing socket...";
		socket_close($socket);
		echo "OK.\n\n";
	}
	
}

?>