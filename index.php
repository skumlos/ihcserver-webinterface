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

var http = createRequestObject();

function toggleOutput(moduleNumber,outputNumber) {
	http.open('get', 'IHCConnection.php?action=toggleOutput&moduleNumber='+moduleNumber+'&outputNumber='+outputNumber);
        http.onreadystatechange = handleResponse;
        http.send(null);
}

function getAll() {
        http.open('get', 'IHCConnection.php?action=getAll');
        http.onreadystatechange = handleResponse;
        http.send(null);
}

function handleResponse() {
	if(http.readyState == 4){
		var response = JSON.parse(http.responseText);
		if(response.type == "allModules") {
			var ioParagraph = "";
			for(var i = 0; i < response.modules.outputModules.length; i++) {
				if(response.modules.outputModules[i].state) {
					ioParagraph += ("<h3>Output module "+response.modules.outputModules[i].moduleNumber+"</h3>");
					for(var j=0; j < response.modules.outputModules[i].outputStates.length; j++) {
						var moduleNumber = response.modules.outputModules[i].moduleNumber;
						var outputNumber = response.modules.outputModules[i].outputStates[j].outputNumber;
						ioParagraph += ("<button id=output."+moduleNumber+"."+outputNumber+" onclick=toggleOutput("+moduleNumber+","+outputNumber+")>Output "+moduleNumber+"."+outputNumber+"</button>");
					}
					ioParagraph += ("<br><br>");
				}
			}
			for(var i = 0; i < response.modules.inputModules.length; i++) {
				if(response.modules.inputModules[i].state) {
					ioParagraph += ("<h3>Input module "+response.modules.inputModules[i].moduleNumber+"</h3>");
					for(var j=0; j < response.modules.inputModules[i].inputStates.length; j++) {
						if(j%8 == 0) {
							ioParagraph += ("<br>");
						}
						var moduleNumber = response.modules.inputModules[i].moduleNumber;
						var inputNumber = response.modules.inputModules[i].inputStates[j].inputNumber;
						ioParagraph += ("<button id=input."+moduleNumber+"."+inputNumber+">Input "+moduleNumber+"."+inputNumber+"</button>");
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
<h1>IHC control v0.1 <input type=button onClick="location.href='configuration.php'" value='Configuration'></h1>
<p id=ioOverview></p>

<script>
getAll();
</script>
<?php
echo "IHCServer webinterface v$version";
echo "<br>";
echo "(C) $year by $author ($email)";
?>
</body>
</html>
