<html>
<head>
<?php
	$version = "0.2";
	$author = "Martin Hejnfelt";
	$email = "martin@hejnfelt.com";
	$year = "2013";
	$id = $_REQUEST['id'];
	if($id == null) {
		$id = md5(date("dmY G:i:s",time()+rand(1,10000)));
		echo "<script>";
		echo "window.location.assign(\"index.php?id=$id\")";
		echo "</script>";
		return;
	}
        echo "<script>var sessionid=\"$id\";</script>";
        echo "<script>var alarmState=false;</script>";
	echo "<title>IHCServer Webinterface v$version</title>";
?>
<script language="javascript" src="FloatLayer.js"></script>
<script>
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

var ihcrequests = null;
var eventrequest = null;
var alarmStateRequest = null;
var init = true;

function startEventListener() {
	eventrequest = createRequestObject();
        eventrequest.open('get', 'IHCEventListener.php?id='+sessionid);
        eventrequest.onreadystatechange = updateIOs;
        eventrequest.send(null);
}

function setAlarm() {
	if(eventrequest != null) {
		eventrequest.abort();
		delete eventrequest;
		eventrequest = null;
	}
	if(alarmStateRequest != null) {
		alarmStateRequest.abort();
		delete alarmStateRequest;
		alarmStateRequest = null;
	}
	if(alarmState) {
		location.href='kpinput.php?id='+sessionid+'&action=disarm';
	} else {
		location.href='kpinput.php?id='+sessionid+'&action=arm';
	}
}

function setAlarmButton(state) {
	if(state) {
		document.getElementById('alarm').style.backgroundColor="red";
		document.getElementById('alarm').style.color="white";
		document.getElementById('alarm').innerHTML="<img src='alarm-armed.png'><br>Alarm on";
	} else if(!state) {
		document.getElementById('alarm').style.backgroundColor="lightgreen";
		document.getElementById('alarm').style.color="black";
		document.getElementById('alarm').innerHTML="<img src='alarm-disarmed.png'><br>Alarm off";
	}
}

function setAlarmState() {
	if(alarmStateRequest.readyState == 4) {
		var response = alarmStateRequest.responseText;
		delete alarmStateRequest;
		alarmStateRequest = null;
		if(response == "true") {
			alarmState = true;
			setAlarmButton(alarmState);
		} else if(response == "false") {
			alarmState = false;
			setAlarmButton(alarmState);
		} else {
			// Something went wrong, start over
			getAlarmState();
		}
		getAll();
	} else if(alarmStateRequest.readyState == 4 && eventrequest.status != 200) {
		alert("Ballade");
	}
}

function getAlarmState() {
	alarmStateRequest = createRequestObject();
        alarmStateRequest.open('get', 'IHCConnection.php?id='+sessionid+'&action=getAlarmState');
        alarmStateRequest.onreadystatechange = setAlarmState;
        alarmStateRequest.send(null);
}

function updateIOs() {
	if(eventrequest.readyState == 4) {
		var responseText = eventrequest.responseText;
                var response = null;
		try {
			response = JSON.parse(responseText);
		} catch (err) {
			return;
		}
		delete eventrequest;
		eventrequest = null;
		startEventListener();
		if(response.type == "outputState") {
			var moduleID = "output."+response.moduleNumber+"."+response.ioNumber;
			var color = (response.state ? "lightgreen" : "lightgray");
			document.getElementById(moduleID).style.background = color;
		} else if(response.type == "inputState") {
			var moduleID = "input."+response.moduleNumber+"."+response.ioNumber;
			var color = (response.state ? "lightgreen" : "lightgray");
			document.getElementById(moduleID).style.background = color;
		} else if(response.type == "alarmState") {
			alarmState = response.state;
			setAlarmButton(alarmState);
		}
	} else if(eventrequest.readyState == 4 && eventrequest.status != 200) {
		delete eventrequest;
		eventrequest = null;
		alert("Event request did not succeed!");
	}
}

function toggleOutput(moduleNumber,outputNumber) {
	ihcrequests = createRequestObject();
	ihcrequests.open('get', 'IHCConnection.php?id='+sessionid+'&action=toggleOutput&moduleNumber='+moduleNumber+'&outputNumber='+outputNumber);
        ihcrequests.onreadystatechange = handleResponse;
        ihcrequests.send(null);
}

function getAll() {
	ihcrequests = createRequestObject();
        ihcrequests.open('get', 'IHCConnection.php?id='+sessionid+'&action=getAll');
        ihcrequests.onreadystatechange = handleResponse;
        ihcrequests.send(null);
}

function handleResponse() {
	if(ihcrequests.readyState == 4){
		var response = JSON.parse(ihcrequests.responseText);
		if(response.type == "allModules") {
			var ioParagraph = "";
			for(var i = 0; i < response.modules.outputModules.length; i++) {
				if(response.modules.outputModules[i].state) {
					ioParagraph += ("<h3>Output module "+response.modules.outputModules[i].moduleNumber+"</h3>");
					for(var j=0; j < response.modules.outputModules[i].outputStates.length; j++) {
						var moduleNumber = response.modules.outputModules[i].moduleNumber;
						var outputNumber = response.modules.outputModules[i].outputStates[j].outputNumber;
						var outputState = response.modules.outputModules[i].outputStates[j].outputState;
						var outputDescription = response.modules.outputModules[i].outputStates[j].description;
						if(outputDescription == "") { outputDescription = "Output "+moduleNumber+"."+outputNumber; };
						ioParagraph += ("<input type=\"button\" style=\"background-color:"+(outputState?"lightgreen":"lightgrey")+"\; height:60px\; width:100px\; white-space:normal\; word-wrap:break-word\; vertical-align:middle\;\" id=\"output."+moduleNumber+"."+outputNumber+"\" value=\""+outputDescription+"\" onclick=toggleOutput("+moduleNumber+","+outputNumber+")> ");
					}
					ioParagraph += ("<br>");
				}
			}
			for(var i = 0; i < response.modules.inputModules.length; i++) {
				if(response.modules.inputModules[i].state) {
					ioParagraph += ("<h3>Input module "+response.modules.inputModules[i].moduleNumber+"</h3>");
					for(var j=0; j < response.modules.inputModules[i].inputStates.length; j++) {
						if(j == 8) {
							ioParagraph += ("<br><br>");
						}
						var moduleNumber = response.modules.inputModules[i].moduleNumber;
						var inputNumber = response.modules.inputModules[i].inputStates[j].inputNumber;
						var inputState = response.modules.inputModules[i].inputStates[j].inputState;
						var inputDescription = response.modules.inputModules[i].inputStates[j].description;
						if(inputDescription == "") { inputDescription = "Input "+moduleNumber+"."+inputNumber; };
						ioParagraph += ("<input type=\"button\" style=\"background-color:"+(inputState?"lightgreen":"lightgrey")+"\; height:60px\; width:100px\; white-space:normal\; word-wrap:break-word\; vertical-align:middle\;\" id=\"input."+moduleNumber+"."+inputNumber+"\" value=\""+inputDescription+"\"> ");
					}
					ioParagraph += ("<br>");
				}
			}
			document.getElementById("ioOverview").innerHTML = ioParagraph;
			// I/Os are now set up, get alarm state and start listening for events
			if(init) {
				startEventListener();
				setTimeout("getAlarmState()",1000);
				init = false;
			}
		}
	}
}

</script>
</head>
<body onresize="alignFloatLayers()" onscroll="alignFloatLayers()">
<?php
echo "<div id=\"controlpanel\" style=\"width:98%; background:#d0d0ff; border:solid black 1px; padding:5px; font-family:arial; font-size:20px; text-align:center\">";
echo "<strong>IHCServer Webinterface v$version</strong>";
echo "<button onClick=\"setAlarm()\" id=\"alarm\" style=\"height:74px; width:92px; float: left\"<img src='loading.gif'><br>Alarm</button> ";
echo "<button onClick=\"location.href='kpinput.php?id=$id&action=login'\" style=\"height:74px; width:74px; float: right\" align=right><img src='login.png'><br>Login</button>";
echo "<button onClick=\"location.href='configuration.php?id=$id'\" style=\"height:74px; float: right\"><img src='configuration.png'><br>Configuration</button>";
echo "</div>";
?>
<br><br><br><br>
<p id=ioOverview></p>
<script>
addFloatLayer('controlpanel',5,5,10);
function detach(layername){
	lay  = document.getElementById(layername);
	left = getXCoord(lay);
	top  = getYCoord(lay);
	lay.style.position = 'absolute';
	lay.style.top      = top;
	lay.style.left     = left;
	getFloatLayer(layername).initialize();
	alignFloatLayers();
}
detach('controlpanel');
getAll();
</script>
<?php
echo "IHCServer Webinterface v$version";
echo "<br>";
echo "(C) $year by $author ($email)";
echo "<br>IHC and IHC modules are properties of LK A/S<br>";
?>
</body>
</html>
