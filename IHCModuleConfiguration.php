<html>
<script>
var http;

function response() {
	if(http.readyState == 4 && http.status == 200) {
		http = null;
		delete http;
	}
}

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

function getModuleState(moduleType,moduleNumber) {
	http = createRequestObject();
	var action;
	if (moduleType == "output") {
		action = "getOutputModuleState";
	} else if (moduleType == "input") {
		action = "getInputModuleState";
	} else {
		document.write("dunno");
		return;
	}
	var reqString = "action="+action+"&moduleType="+moduleType+"&moduleNumber="+moduleNumber;
        http.open('get', 'IHCConnection.php?'+reqString);
	http.onreadystatechange = setModuleState;
        http.send(null);
}

function getModuleConfiguration(moduleType,moduleNumber) {
	http = createRequestObject();
	var reqString = "moduleType="+moduleType+"&moduleNumber="+moduleNumber;
        http.open('get', 'IHCConnection.php?action=getModuleConfiguration&'+reqString);
	http.onreadystatechange = fillModuleConfiguration;
        http.send(null);
}

function fillModuleConfiguration() {
	if(http.readyState == 4) {
//		document.write(http.responseText);
		var response = JSON.parse(http.responseText);
		http = null;
		delete http;
		if(response.type == "moduleConfiguration") {
			document.getElementById("moduleState").checked = response.state;
			for(var j=0; j < response.ioDescriptions.length; j++) {
				var ioNumber = response.ioDescriptions[j].ioNumber;
				var ioDescription = response.ioDescriptions[j].ioDescription;
				document.getElementById(ioNumber).value = ioDescription;
			}
		}
	}
}

function setModuleState() {
	if(http.readyState == 4) {
		var response = JSON.parse(http.responseText);
		http = null;
		delete http;
		if(response.type == "outputModuleState" ||
			response.type == "inputModuleState") {
			document.getElementById("moduleState").checked = response.state;
		}
	}
}

function setModuleConfiguration(type,moduleNumber) {
	var req = new Object();
	req.type = "setModuleConfiguration";
	req.moduleType = type;
	req.moduleNumber = moduleNumber;
	req.ioDescriptions = new Array();
	req.state = document.getElementById("moduleState").checked;
	var toCheck = 8;
	if(type == "input") {
		toCheck = 16;
	}
	for(var i = 1; i <= toCheck; i++) {
		var ioDescription = new Object();
		var val = document.getElementById(i).value;
		if(val != "") {
			ioDescription.ioNumber = i;
			ioDescription.ioDescription = val;
			req.ioDescriptions.push(ioDescription);
		}
	}
	var jsonRequest = JSON.stringify(req);
//	document.write(jsonRequest);

	http = createRequestObject();
	http.open("POST", "IHCConnection.php?action=moduleConfiguration", true);
//	http.open("POST", "post.php?action=moduleConfiguration", true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Content-length", jsonRequest.length);
	http.setRequestHeader("Connection", "close");
	http.onreadystatechange = response;
	http.send(jsonRequest);
}

</script>
<body>
<?php
	$moduleType = $_REQUEST['moduleType'];
	$moduleNumber = $_REQUEST['moduleNumber'];
	echo "<h2>Configure IHC $moduleType module $moduleNumber <input type=button onclick=\"location.href='configuration.php'\" value='Back'></h2>";

	echo "<input type=\"checkbox\" id=\"moduleState\" value=\"state\"> Module present<br>";
	if($moduleType == "output") {
		echo "<h3>Output descriptions</h3>";
		for($cnt = 1; $cnt <= 8; $cnt++) {
			echo "Output $moduleNumber.$cnt <input type=\"text\" id=\"$cnt\"><br>";
		}
		echo "<br><button id=\"save\" onclick=setModuleConfiguration(\"$moduleType\",$moduleNumber)>Save</button>";
	} else if($moduleType == "input") {
		echo "<h3>Input descriptions</h3>";
		for($cnt = 1; $cnt <= 16; $cnt++) {
			echo "Input $moduleNumber.$cnt <input type=\"text\" id=\"$cnt\"><br>";
		}
		echo "<br><button id=\"save\" onclick=setModuleConfiguration(\"$moduleType\",$moduleNumber)>Save</button>";
	}
	echo "<script>var moduleNumber=$moduleNumber;var moduleType=\"$moduleType\";</script>";
?>
<script>
//getModuleState(moduleType,moduleNumber);
getModuleConfiguration(moduleType,moduleNumber);
</script>
</body>
</html>
