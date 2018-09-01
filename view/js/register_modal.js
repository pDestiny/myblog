(function(){
    const $submitBtn = $('#registerModal .modal-footer > .submit');
    const $registerForm = $("#registerModal #register-form");

    $submitBtn.on('click', () => {
        let submitData = {
            email: $('#registerModal input#register-email').val(),
            nickname: $('#registerModal input#nickname').val(),
            password1: $('#registerModal input#password1').val(),
            password2: $('#registerModal input#password2').val()
          }
        $.post("/controller/register.php", submitData, (data) => {
            //alertType이 success, info, warning, danger가 아닐 경우에
            if(!data.alertType.match(/(success|info|warning|danger)/)) {
                alertBoxPopAndOut($(".alert-danger"), "Invalid Access");       
            } else if(data.alertType === 'success') {
                alertBoxPopAndOut($(".alert-" + data.alertType), data.msg);
                setTimeout(function() {
                    location.href = "/controller/home.php";
                }, 1800);
            } else if(data.alertType.match(/(info|warning|danger)/)) {
                alertBoxPopAndOut($(".alert-" + data.alertType), data.msg);
            }
        }, 'json');
    });  
})();