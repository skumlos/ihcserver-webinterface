<?php
	$service_port = 45201;
	$address = '127.0.0.1';

	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false) {
		echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
	}

	$result = socket_connect($socket, $address, $service_port);
	if ($result === false) {
		echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
	}

	socket_recv($socket,$header,4,MSG_WAITALL);

	$toReceive = unpack("L",$header);

	$msg = "";
	socket_recv($socket,$msg,$toReceive[1],MSG_WAITALL);
	echo $msg;

	socket_shutdown($socket,2);

	socket_close($socket);
?>
