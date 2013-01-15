<html>
<?php
	$version = "0.1";
	$author = "Martin Hejnfelt";
	$email = "martin@hejnfelt.com";
	$year = "2013";
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
        eventrequest.open('get', 'IHCEventListener.php');
        eventrequest.onreadystatechange = updateIOs;
        eventrequest.send(null);
}

function updateIOs() {
        if(eventrequest.readyState == 4){
                var response = JSON.parse(eventrequest.responseText);
		if(response.type == "outputState") {
			var moduleID = "output."+response.moduleNumber+"."+response.ioNumber;
			var color = (response.state ? "lightgreen" : "lightgray");
			document.getElementById(moduleID).style.background = color;
		} else if(response.type == "inputState") {
			var moduleID = "input."+response.moduleNumber+"."+response.ioNumber;
			var color = (response.state ? "lightgreen" : "lightgray");
			document.getElementById(moduleID).style.background = color;
		}
		startEventListener();
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
						ioParagraph += ("<button style=\"background-color:"+(outputState?"lightgreen":"lightgrey")+"\; height:30px\; width:100px\;\" id=\"output."+moduleNumber+"."+outputNumber+"\" onclick=toggleOutput("+moduleNumber+","+outputNumber+")>Output "+moduleNumber+"."+outputNumber+"</button> ");
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
						ioParagraph += ("<button style=\"background-color:"+(inputState?"lightgreen":"lightgrey")+"\; height:30px\; width:100px\;\" id=\"input."+moduleNumber+"."+inputNumber+"\">Input "+moduleNumber+"."+inputNumber+"</button> ");
					}
					ioParagraph += ("<br><br>");
				}
			}
			document.getElementById("ioOverview").innerHTML = ioParagraph;
		}
	}
}

</script>

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
