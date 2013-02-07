<html>
<head>
<?php
	$version = "0.2";
	$author = "Martin Hejnfelt";
	$email = "martin@hejnfelt.com";
	$year = "2013";

	$id = md5(date("dmY G:i:s",time()+rand(1,10000)));
        echo "<script>var sessionid=\"$id\";</script>";
?>
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

var ihcrequests = createRequestObject();
var eventrequest = createRequestObject();

function startEventListener() {
	eventrequest = createRequestObject();
        eventrequest.open('get', 'IHCEventListener.php?id='+sessionid);
        eventrequest.onreadystatechange = updateIOs;
        eventrequest.send(null);
}

function updateIOs() {
        if(eventrequest.readyState == 4) {
                var response = JSON.parse(eventrequest.responseText);
		eventrequest = null;
		delete eventrequest;
		startEventListener();
		if(response.type == "outputState") {
			var moduleID = "output."+response.moduleNumber+"."+response.ioNumber;
			var color = (response.state ? "lightgreen" : "lightgray");
			document.getElementById(moduleID).style.background = color;
		} else if(response.type == "inputState") {
			var moduleID = "input."+response.moduleNumber+"."+response.ioNumber;
			var color = (response.state ? "lightgreen" : "lightgray");
			document.getElementById(moduleID).style.background = color;
		}
	} else if(eventrequest.readyState == 4 && eventrequest.status != 200) {
		eventrequest = null;
		delete eventrequest;
		alert("Event request did not succeed!");
	}
}

function toggleOutput(moduleNumber,outputNumber) {
	ihcrequests.open('get', 'IHCConnection.php?action=toggleOutput&moduleNumber='+moduleNumber+'&outputNumber='+outputNumber);
        ihcrequests.onreadystatechange = handleResponse;
        ihcrequests.send(null);
}

function getAll() {
	ihcrequests = createRequestObject();
        ihcrequests.open('get', 'IHCConnection.php?action=getAll');
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
						ioParagraph += ("<button style=\"background-color:"+(outputState?"lightgreen":"lightgrey")+"\; height:60px\; width:100px\; white-space:normal\; vertical-align:middle\;\" id=\"output."+moduleNumber+"."+outputNumber+"\" onclick=toggleOutput("+moduleNumber+","+outputNumber+")>"+outputDescription+"</button> ");
					}
					ioParagraph += ("<br><br>");
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
						ioParagraph += ("<button style=\"background-color:"+(inputState?"lightgreen":"lightgrey")+"\; height:60px\; width:100px\; white-space:normal\; vertical-align:middle\;\" id=\"input."+moduleNumber+"."+inputNumber+"\">"+inputDescription+"</button> ");
					}
					ioParagraph += ("<br><br>");
				}
			}
			document.getElementById("ioOverview").innerHTML = ioParagraph;
		}
	}
}

</script>
</head>
<body>
<?php
echo "<h1>IHCServer Webinterface v$version</h1>";
?>
<input type=button onClick="location.href='configuration.php'" value='Configuration'>
<p id=ioOverview></p>

<script>
getAll();
startEventListener();
</script>
<?php
echo "IHCServer Webinterface v$version";
echo "<br>";
echo "(C) $year by $author ($email)";
echo "<br>IHC and IHC modules are properties of LK A/S<br>";
?>
</body>
</html>
