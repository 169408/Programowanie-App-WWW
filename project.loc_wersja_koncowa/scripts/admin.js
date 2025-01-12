var params = new URLSearchParams(window.location.search);

var admin_board = document.getElementById("admin_board");
console.log(admin_board);
console.log(params);

if (params.toString() === '') {
    admin_board.classList.add('default-width');
    admin_board.classList.remove('full-width');
} else {
    admin_board.classList.add('full-width');
    admin_board.classList.remove('default-width');
}