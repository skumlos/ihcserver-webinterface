var mouseDownX = 0;
var triggerDelta = 200;

function mouseup(event) {
        var triggerLevel = mouseDownX - triggerDelta;
        if(triggerLevel > 0 && event.screenX <= triggerLevel) {
                location.href="index2.html";
        }
}

function mousedown(event) {
        mouseDownX = event.screenX;
}
