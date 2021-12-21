var test = document.getElementById('test');
var cont = '<input type="text" name="tags[]"></input><br>'

test.insertAdjacentHTML('afterbegin', cont);

function addColum(){
	test.insertAdjacentHTML('afterbegin',cont);
}
