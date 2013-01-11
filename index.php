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

function toggleIO(action) {
	http.open('get', 'IHCConnection.php?action='+action);
	http.onreadystatechange = handleResponse;
	http.send(null);
}

function handleResponse() {
	if(http.readyState == 4){
		var response = http.responseText;
		var update = new Array();
//		document.write("response: " + response + ".");
		if(response.indexOf('|' != -1)) {
			update = response.split('|');
			var idvar = update[0]+' '+update[1];
			if(update[0] == "outputModule") {
				if(update[2] == "off") {
					document.getElementById(update[0]+" "+update[1]).innerHTML='<img src=\"ihcoutput230_off.png\"><br>IHC Output '+update[1];
				} else if(update[2] == "on") {
					document.getElementById(update[0]+" "+update[1]).innerHTML='<img src=\"ihcoutput230.png\"><br>IHC Output '+update[1];
				}
			}
			if(update[0] == "inputModule") {
				if(update[2] == "off") {
					document.getElementById(update[0]+' '+update[1]).innerHTML='<img src=\"ihcinput24_off.png\"><br>IHC Input '+update[1];
				} else if(update[2] == "on") {
					document.getElementById(update[0]+' '+update[1]).innerHTML='<img src=\"ihcinput24.png\"><br>IHC Input '+update[1];
				}
			}
		}
	}
}

</script>

<body>
<?php
	echo "<h3>IHC Output modules</h3>";
	echo "Select and deselect which modules are present in the system<br>";
	for($cnt = 1; $cnt <=16; $cnt++) {
		echo "<button id=\"outputModule $cnt\" type=\"button\" onclick=\"toggleIO(this.id)\">
		<img src=\"ihcoutput230.png\" alt=\"IHC Output $cnt\" />
		<br/>IHC Output $cnt</button>";
		if($cnt == 8) {
			echo "<br>";
		}
	}
	echo "<h3>IHC Input modules</h3>";
	echo "Select and deselect which modules are present in the system<br>";
	for($cnt = 1; $cnt <=8; $cnt++) {
		echo "<button id=\"inputModule $cnt\" type=\"button\" onclick=\"toggleIO(this.id)\">
		<img src=\"ihcinput24.png\" alt=\"IHC Input $cnt\" />
		<br/>IHC Input $cnt</button>";
	}
?>
</body>
</html>
