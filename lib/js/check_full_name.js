let button = $('#check_full_name');
let inputValue = document.querySelector('#full_name_field');
let checkButton = $('#check_appeal_view_after_validation')

button.click(function(){
    $('#check_full_name_result').load('ajax.php', {
        inputText: inputValue.value
    });
});