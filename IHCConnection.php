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

	$req = $_REQUEST['action'];
	sscanf($req,"%s %d",$cmd,$no);

	class IHCRequest {
		public $type;
		public $moduleNumber;
		public $ioNumber;
	}

	$serverreq = new IHCRequest();

	switch($cmd) {
		case "inputModule":
			$serverreq->type = "toggleInputModule";
			$serverreq->moduleNumber = $no;
			$serverreq->ioNumber = 0;
//		echo "inputModule|$no|off";
		break;
		case "outputModule":
			$serverreq->type = "toggleOutputModule";
			$serverreq->moduleNumber = $no;
			$serverreq->ioNumber = 0;
//		echo "outputModule|$no|off";
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
		$header = '';
		socket_recv($socket,$header,4,MSG_WAITALL);
		$toReceive = unpack("L",$header);
		$msg = "";
		socket_recv($socket,$msg,$toReceive[1],MSG_WAITALL);
		$jsonResponse = json_decode($msg);
		if($jsonResponse != null) {
			$type = $jsonResponse->{"type"};
			switch($type) {
				case "outputModuleState":
					$modNo = $jsonResponse->{"moduleNumber"};
					$state = $jsonResponse->{"state"} ? "on" : "off";
					echo "outputModule|".$modNo."|".$state;
				break;
				case "inputModuleState":
					$modNo = $jsonResponse->{"moduleNumber"};
					$state = $jsonResponse->{"state"} ? "on" : "off";
					echo "inputModule|".$modNo."|".$state;
				break;
				default:
					echo "Unknown response";
				break;
			}
		} else {
			echo "Response was null";
		}
	} else if ($buf == 'NAK') {
		echo "NAK";
	}

	socket_close($socket);
?>
