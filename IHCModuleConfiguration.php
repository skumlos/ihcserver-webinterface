<html>
<?php
	$id = $_REQUEST['id'];
	if($id == "" || $id == null) {
		echo "No session id given!";
		return;
	}
	echo "<script>var sessionid=\"$id\";</script>";
?>

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
	var reqString = "id="+sessionid+"&action="+action+"&moduleType="+moduleType+"&moduleNumber="+moduleNumber;
        http.open('get', 'IHCConnection.php?'+reqString);
	http.onreadystatechange = setModuleState;
        http.send(null);
}

function getModuleConfiguration(moduleType,moduleNumber) {
	http = createRequestObject();
	var reqString = "moduleType="+moduleType+"&moduleNumber="+moduleNumber;
        http.open('get', 'IHCConnection.php?id='+sessionid+'&action=getModuleConfiguration&'+reqString);
	http.onreadystatechange = fillModuleConfiguration;
        http.send(null);
}

function fillModuleConfiguration() {
	if(http.readyState == 4) {
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
			for(var j=0; j < response.ioProtectedStates.length; j++) {
				var ioNumber = response.ioProtectedStates[j].ioNumber;
				var ioProtected = response.ioProtectedStates[j].ioProtected;
				document.getElementById('protected'+ioNumber).checked = ioProtected;
			}
			for(var j=0; j < response.ioAlarmStates.length; j++) {
				var ioNumber = response.ioAlarmStates[j].ioNumber;
				var ioAlarm = response.ioAlarmStates[j].ioAlarm;
				document.getElementById('alarm'+ioNumber).checked = ioAlarm;
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
	req.ioProtectedStates = new Array();
	req.ioAlarmStates = new Array();
	req.state = document.getElementById("moduleState").checked;
	var toCheck = 8;
	if(type == "input") {
		toCheck = 16;
	}
	for(var i = 1; i <= toCheck; i++) {
		var ioDescription = new Object();
		var val = document.getElementById(i).value;
		ioDescription.ioNumber = i;
		ioDescription.ioDescription = val;
		req.ioDescriptions.push(ioDescription);
	}
	for(var i = 1; i <= toCheck; i++) {
		var ioProtected = new Object();
		var protected = document.getElementById('protected'+i).checked;
		ioProtected.ioNumber = i;
		ioProtected.ioProtected = protected;
		req.ioProtectedStates.push(ioProtected);
	}
	for(var i = 1; i <= toCheck; i++) {
		var ioAlarm = new Object();
		var alarm = document.getElementById('alarm'+i).checked;
		ioAlarm.ioNumber = i;
		ioAlarm.ioAlarm = alarm;
		req.ioAlarmStates.push(ioAlarm);
	}
	var jsonRequest = JSON.stringify(req);

	http = createRequestObject();
	http.open("POST", "IHCConnection.php?id="+sessionid+"&action=moduleConfiguration", true);
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
	echo "<h2>Configure IHC $moduleType module $moduleNumber <input type=button onclick=\"location.href='configuration.php?id=$id'\" value='Back'></h2>";

	echo "<input type=\"checkbox\" id=\"moduleState\" value=\"state\"> Module present<br>";
	if($moduleType == "output") {
		echo "<h3>Output descriptions</h3>";
		for($cnt = 1; $cnt <= 8; $cnt++) {
			echo "Output $moduleNumber.$cnt <input type=\"text\" id=\"$cnt\"> ";
			echo "<input type=\"checkbox\" id=\"protected$cnt\" value=\"state\"> Protected ";
			echo "<input type=\"checkbox\" id=\"alarm$cnt\" value=\"state\"> Alarm<br>";
		}
		echo "<br><button id=\"save\" onclick=setModuleConfiguration(\"$moduleType\",$moduleNumber)>Save</button>";
	} else if($moduleType == "input") {
		echo "<h3>Input descriptions</h3>";
		for($cnt = 1; $cnt <= 16; $cnt++) {
			echo "Input $moduleNumber.$cnt <input type=\"text\" id=\"$cnt\"> ";
			echo "<input type=\"checkbox\" id=\"protected$cnt\" value=\"state\"> Protected ";
			echo "<input type=\"checkbox\" id=\"alarm$cnt\" value=\"state\"> Alarm<br>";
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
