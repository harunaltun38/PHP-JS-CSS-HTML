"use strict";
// When the user scrolls the page, execute myFunction
window.onscroll = function() {myFunction()};

// Get myHeader
var nav = document.getElementsByTagName("header")[0];

// Get the offset position of the navbar
var sticky = nav.offsetTop;

// Add the sticky class to the header when you reach its scroll position. Remove "sticky" when you leave the scroll position
function myFunction() {
    "use strict";
    if (window.pageYOffset > sticky) {
        nav.classList.add("sticky");
    } else {
        nav.classList.remove("sticky");
    }
}

var open = false;

function openSlideMenu() {
    "use strict";
    if (open) {
        closeSlideMenu();
    } else {
        document.getElementById('side-menu').style.display = 'block';
        open = true;
    }
}

function closeSlideMenu() {
    "use strict";
    document.getElementById('side-menu').style.display = 'none';
    open = false;
}

closeSlideMenu();
