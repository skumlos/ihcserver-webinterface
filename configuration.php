<html>
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

function saveConfiguration() {
	http.open('get', 'IHCConnection.php?action=saveConfiguration');
	http.onreadystatechange = handleResponse;
	http.send(null);
}

function toggleOutputModule(id) {
	http.open('get', 'IHCConnection.php?action=toggleOutputModule&moduleNumber='+id);
	http.onreadystatechange = handleResponse;
	http.send(null);
}

function toggleInputModule(id) {
	http.open('get', 'IHCConnection.php?action=toggleInputModule&moduleNumber='+id);
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
//		document.write(http.responseText);
		var response = JSON.parse(http.responseText);
		if(response.type == "allModules") {
			for(var i = 0; i < response.modules.outputModules.length; i++) {
				var state = response.modules.outputModules[i].state;
				var moduleID = "outputModule"+response.modules.outputModules[i].moduleNumber;
				var imgsrc = (state ? "ihcoutput230.png" : "ihcoutput230_off.png");
				document.getElementById(moduleID).innerHTML='<img src=\"'+imgsrc+'\"><br>IHC Output<br>Module '+response.modules.outputModules[i].moduleNumber;
			}
			for(var i = 0; i < response.modules.inputModules.length; i++) {
				var state = response.modules.inputModules[i].state;
				var moduleID = "inputModule"+response.modules.inputModules[i].moduleNumber;
				var imgsrc = (state ? "ihcinput24.png" : "ihcinput24_off.png");
				document.getElementById(moduleID).innerHTML='<img src=\"'+imgsrc+'\"><br>IHC Input<br>Module '+response.modules.inputModules[i].moduleNumber;
			}
		} else if(response.type == "outputModuleState") {
			var moduleID = "outputModule"+response.moduleNumber;
			var imgsrc = (response.state ? "ihcoutput230.png" : "ihcoutput230_off.png");
			document.getElementById(moduleID).innerHTML='<img src=\"'+imgsrc+'\"><br>IHC Output<br>Module '+response.moduleNumber;
		} else if(response.type == "inputModuleState") {
			var moduleID = "inputModule"+response.moduleNumber;
			var imgsrc = (response.state ? "ihcinput24.png" : "ihcinput24_off.png");
			document.getElementById(moduleID).innerHTML='<img src=\"'+imgsrc+'\"><br>IHC Input<br>Module '+response.moduleNumber;
		}
	}
}

</script>

<body>
<h2>Configuration <input type=button onclick="location.href='index.php'" value='Back'><input type=button onclick=saveConfiguration() value='Save'></h2>
<?php
	echo "<h3>IHC Output modules</h3>";
	echo "Select and deselect which modules are present in the system<br>";
	for($cnt = 1; $cnt <=16; $cnt++) {
		echo "<button id=\"outputModule$cnt\" type=\"button\" onclick=\"toggleOutputModule($cnt)\">
		<img src=\"ihcoutput230.png\" alt=\"IHC Output Module $cnt\" />
		<br/>IHC Output<br>Module $cnt</button>";
		if($cnt == 8) {
			echo "<br>";
		}
	}
	echo "<h3>IHC Input modules</h3>";
	echo "Select and deselect which modules are present in the system<br>";
	for($cnt = 1; $cnt <=8; $cnt++) {
		echo "<button id=\"inputModule$cnt\" type=\"button\" onclick=\"toggleInputModule($cnt)\">
		<img src=\"ihcinput24.png\" alt=\"IHC Input Module $cnt\" />
		<br/>IHC Input<br>Module $cnt</button>";
	}
?>
<script>
getAll();
</script>
</body>
</html>
