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

function getAll() {
	http.open('get', 'IHCConnection.php?id='+sessionid+'&action=getAll');
	http.onreadystatechange = handleResponse;
	http.send(null);
}

function handleResponse() {
	if(http.readyState == 4){
		var response = JSON.parse(http.responseText);
		http = null;
		delete http;
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
		}
	}
}

</script>

<body>
<?php
	echo "<h2>Configuration <input type=button onclick=\"location.href='index.php?id=$id'\" value='Back'></h2>";
	echo "<button onClick=\"location.href='kpinput.php?id=$id&action=setCode&level=superuser'\">Set superuser code</button> ";
	echo "<button onClick=\"location.href='kpinput.php?id=$id&action=setCode&level=admin'\">Set admin code</button>";
	echo "<br><br>";
	echo "Select an output module to configure it.<br>";
	echo "<h3>IHC Output modules</h3>";
	for($cnt = 1; $cnt <=16; $cnt++) {
		echo "<button id=\"outputModule$cnt\" type=\"button\" onclick=\"location.href='IHCModuleConfiguration.php?id=$id&moduleType=output&moduleNumber=$cnt'\">
		<img src=\"ihcoutput230.png\" alt=\"IHC Output Module $cnt\" />
		<br/>IHC Output<br>Module $cnt</button>";
		if($cnt == 8) {
			echo "<br>";
		}
	}
	echo "<h3>IHC Input modules</h3>";
	for($cnt = 1; $cnt <=8; $cnt++) {
		echo "<button id=\"inputModule$cnt\" type=\"button\" onclick=\"location.href='IHCModuleConfiguration.php?id=$id&moduleType=input&moduleNumber=$cnt'\">
		<img src=\"ihcinput24.png\" alt=\"IHC Input Module $cnt\" />
		<br/>IHC Input<br>Module $cnt</button>";
	}
?>
<script>
getAll();
</script>
</body>
</html>
