<?php
	$service_port = 45200;
//	$address = '127.0.0.1';
	$address = '192.168.1.149';

	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket === false) {
		echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
	}

	$result = socket_connect($socket, $address, $service_port);
	if ($result === false) {
		echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
	}

	class IHCRequest {
		public $type;
		public $moduleNumber;
		public $ioNumber;
	}

	$serverreq = new IHCRequest();
	$serverreq->type = $_REQUEST['action'];

	$responseRequired = true;

	switch($serverreq->type) {
		case "toggleOutput":
			$serverreq->moduleNumber = intval($_REQUEST['moduleNumber']);
			$serverreq->ioNumber = intval($_REQUEST['outputNumber']);
		break;
		case "toggleInputModule":
			$serverreq->moduleNumber = intval($_REQUEST['moduleNumber']);
			$serverreq->ioNumber = 0;
		break;
		case "toggleOutputModule":
			$serverreq->moduleNumber = intval($_REQUEST['moduleNumber']);
			$serverreq->ioNumber = 0;
		break;
		case "getAll":
			$serverreq->moduleNumber = 0;
			$serverreq->ioNumber = 0;
		break;
		case "saveConfiguration":
			$responseRequired = false;
			$serverreq->moduleNumber = 0;
			$serverreq->ioNumber = 0;
		break;
		default:
			echo "Unknown request";
		break;
	}

	$jsonString = json_encode($serverreq);

	socket_write($socket,pack("L",strlen($jsonString)),4);
	socket_write($socket,$jsonString,strlen($jsonString));

	$buf = "";

	$recvd = socket_recv($socket,$buf,3,MSG_WAITALL);

	if ($buf == 'ACK') {
		if(responseRequired) {
			$header = '';
			socket_recv($socket,$header,4,MSG_WAITALL);
			$toReceive = unpack("L",$header);
			$msg = "";
			socket_recv($socket,$msg,$toReceive[1],MSG_WAITALL);
			$jsonResponse = json_decode($msg);
			if($jsonResponse != null) {
				$type = $jsonResponse->{"type"};
				switch($type) {
					case "outputState":
					case "outputModuleState":
					case "inputModuleState":
					case "allModules":
						echo $msg;
					break;
					default:
						echo "Unknown response";
					break;
				}
			} else {
				echo "Response was null";
			}
		}
	} else if ($buf == 'NAK') {
		echo "NAK";
	}

	socket_close($socket);
?>
