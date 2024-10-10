var computed = false;
var decimal = 0;

function convert(entryform, from, to) {
    convertfrom = from.selectedIndex;
    convertto = to.selectedIndex;
    entryform.display.value = (entryform.input.value * from[convertfrom].value / to[convertto].value);
}

function addchar(input, character) {
    if ((character == '.' && decimal == "0") || character != '.') {
        (input.value == "" || input.value == "0") ? input.value = character : input.value += character
        convert(input.form, input.form.measure1, input.form.measure2)
        computed = true;
        if (character == '.') {
            decimal = 1;
        }
    }
}

function openVothcom() {
    window.open("", "Display window", "toolbar=no,directories=no,menubar=no");
}

function clear(form) {
    form.input.value = 0;
    form.display.value = 0;
    decimal = 0;
}

function changeBackground(hexNumber) {
    document.bgColor = hexNumber;
}

var b = "borys h!";
function dasd() {
    alert('dasd')
}

var arrayHex = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F"];

git = true;

function stop() {
    git = false;
}

var stringColor = "";
    for (let i = 0; i < 5; i++) {
        var randomBox = Math.floor(Math.random() * arrayHex.length);
        stringColor = stringColor + arrayHex[randomBox];
    }

    stringColor = "#" + stringColor;

setTimeout(() => {stop()}, 5000);

for (let j = 0; j < 10; j++) {
    
    setTimeout(() => {changeBackground(stringColor)}, 1000);
}