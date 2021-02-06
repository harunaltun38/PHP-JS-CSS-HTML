var xmlhttp = new XMLHttpRequest();

// AJAX response handler 
xmlhttp.onreadystatechange = function () {
    "use strict";
    if (this.readyState == 1) {
        console.log("OPENED");
    } else if (this.readyState == 2) {
        console.log("HEADERS");
    } else {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            processJSONResponse(response);
        }
    }
}

function getPizzaListe() {
    "use strict";    
    xmlhttp.open("GET", "kueche.php", true);
    xmlhttp.send();
}

function processJSONResponse(json_array) {
    "use strict";
    if (json_array.length > 0) {
        removeChildren("list_container");
        document.getElementById("list_container").setAttribute("size", json_array.length+1);
        // for each item call add element to html
        for (var i = 0; i < json_array.length; i++) {
            createStatusLine(json_array[i]);
        }
    }
}

function removeChildren(node_name) {
    "use strict";
    var parent = document.getElementById(node_name);
    if (parent != null) {
        while (parent.hasChildNodes()) {
            parent.removeChild(parent.lastChild);
        }
    }
    var ueberschrift = document.createElement("legend");
    ueberschrift.append(document.createTextNode("Bestellstatus"));
    parent.append(ueberschrift);
}

function refreshData() {
    "use strict";
    setInterval(getPizzaListe, 3000);
}

function createStatusLine(data) {
    "use strict";
    var parent = document.getElementById("list_container");
    var fieldset = document.createElement("fieldset");
    fieldset.setAttribute("class", "formular");
    var legend = document.createElement("legend");
    legend.append(document.createTextNode(data.pizzaname));
    var radios = document.createElement("div");
    radios.setAttribute("class", "radios");
    fieldset.appendChild(legend);
    
    
    radios.appendChild(createStatusNode(0, data));
    radios.appendChild(createStatusNode(1, data));
    radios.appendChild(createStatusNode(2, data));
    
    fieldset.appendChild(radios);
    parent.appendChild(fieldset);
}

function createStatusNode(number, data) { // ‘number’—> {0,1,2}: PizzaStatus
    "use strict";
    var label = document.createElement("label");
    var span = document.createElement("span");
    var statuscase = "";
    switch(number) {
        case 0: {
            statuscase = "Bestellt";
            break;
        }
        case 1: {
            statuscase = "In Zubereitung";
            break;
        }
        case 2: {
            statuscase = "Fertig";
            break;
        }
    }
    var text = document.createTextNode(statuscase);
    span.append(text);
    
    var radio = document.createElement("input");
    radio.setAttribute("type", "radio");
    radio.setAttribute("name", data.pizza_id);
    radio.setAttribute("value", number);
    radio.setAttribute("onclick", "udpateStatus(this)");
    if (number == data.status) {
        radio.checked = true;
    }
    label.appendChild(span);
    label.appendChild(radio);
    
    return label;
}

function udpateStatus(node) {
    "use strict";
    var id = node.getAttribute("name");
    var stat = node.getAttribute("value");
    var statusitem = {"pizza_id": id, "status": stat};
    var json = JSON.stringify(statusitem);
    
    xmlhttp.open("POST", "kueche.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("change="+json);
}