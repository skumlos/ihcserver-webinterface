<html>
<head>
<?php
        $id = $_REQUEST['id'];
        if($id == "" || $id == null) {
                echo "No session id given!";
                return;
        }
        $action = $_REQUEST['action'];
        if($action == "" || $action == null) {
                echo "No action given!";
                return;
        }

        echo "<script>var sessionid=\"$id\";</script>";
        echo "<script>var action=\"$action\";</script>";
	if($action == "setCode") {
		$level = $_REQUEST['level'];
	        echo "<script>var level=\"$level\";</script>";
	}
?>
<script>
var http;

function createRequestObject() {
        var ro;
        var browser = navigator.appName;
        if(browser == "Microsoft Internet Explorer"){
                ro = new ActiveXObject("Microsoft.XMLHTTP");
        } else {
                ro = new XMLHttpRequest();
        }
        return ro;
}

function response() {
        if(http.readyState == 4 && http.status == 200) {
                http = null;
                delete http;
		location.assign('index.php?id='+sessionid);
//		history.back();
        }
}

function buttonPressed(key) {
	if(key == 'OK') {
		var req = new Object();
		req.type = action;
		req.input = document.getElementById('password').value;
		if(action == "setCode") {
			req.level = level;
		}
		var jsonRequest = JSON.stringify(req);
		http = createRequestObject();
		http.open("POST", "IHCConnection.php?id="+sessionid+"&action="+action, true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.setRequestHeader("Content-length", jsonRequest.length);
		http.setRequestHeader("Connection", "close");
		http.onreadystatechange = response;
		http.send(jsonRequest);
		document.getElementById("contents").innerHTML="Processing, please wait...";
		return;
	} else if (key == 'CLR') {
		document.getElementById('password').value = "";
	} else {
		document.getElementById('password').value = document.getElementById('password').value + key;
	}
}
</script>
</head>
<body>
<?php
	echo "<p id=\"contents\" style=\"margin-left:auto; margin-right:auto; text-align:center\">";
	echo "<input type=button onclick=\"history.back()\" value='Back'><br><br>";
	echo "<input type='password' id='password' name='password' style=\"font-size:28;\" size='9'>";
	echo "<br>";
	echo "<br>";
	echo "<input type='button' id=\"1\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('1') value='1'> ";
	echo "<input type='button' id=\"2\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('2') value='2'> ";
	echo "<input type='button' id=\"3\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('3') value='3'>";
	echo "<br>";
	echo "<input type='button' id=\"4\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('4') value='4'> ";
	echo "<input type='button' id=\"5\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('5') value='5'> ";
	echo "<input type='button' id=\"6\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('6') value='6'>";
	echo "<br>";
	echo "<input type='button' id=\"7\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('7') value='7'> ";
	echo "<input type='button' id=\"8\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('8') value='8'> ";
	echo "<input type='button' id=\"9\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('9') value='9'>";
	echo "<br>";
	echo "<input type='button' id=\"CLR\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('CLR') value='CLR'> ";
	echo "<input type='button' id=\"0\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('0') value='0'> ";
	echo "<input type='button' id=\"OK\" style=\"font-size:28; height:80px; width:80px; horizontal-align:middle; vertical-align:middle;\" onclick=buttonPressed('OK') value='OK'>";
	echo "</p>";
?>
</body>
</html>
