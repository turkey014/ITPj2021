var test = document.getElementById('test');
test.insertAdjacentHTML('afterbegin','<input type="text" name="tags[]"></input><br>');

function addColum(){
	test.insertAdjacentHTML('afterbegin','<input type="text" name="tags[]"></input><br>');
}
