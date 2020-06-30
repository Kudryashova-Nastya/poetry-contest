for (let i = 1; i < 25; i++) {
$('#button'+i).on('click', Toggle);

function Toggle(){
    $('.work__comments'+i).slideToggle(1500);
  }
}