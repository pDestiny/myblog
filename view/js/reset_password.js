(function() {
    setTimeout(function() {
        window.scrollBy({
            top: $('.reset-pw-form-cnt').offset().top,
            left:0,
            behavior: 'smooth'
        });
    }, 500);
    
    let queryMap = getQueryStringMap();

    localStorage.setItem('pw_recover_cd', queryMap.get('pw_recover_cd'));
    localStorage.setItem('email', queryMap.get('email'));    

    const $resetPwSubmit = $('.reset-pw-submit');
    const $resetPwCancel = $('.reset-pw-cancel');

    $resetPwSubmit.on('click',function() {
        
        let submitData = {
            password1: $('#reset-password1').val(),
            password2: $('#reset-password2').val(),
            email: localStorage.getItem('email'),
            pw_recover_cd: localStorage.getItem('pw_recover_cd') 
        }

        $.post('/controller/changePassword.php', submitData, data=> {
            if(data.alertType === 'success') {
                alertBoxPopAndOut($('.alert-success'), data.msg);
                setTimeout(function() {
                    window.scrollTo({
                        top: 0,
                        left: 0,
                        behavior: 'smooth'
                    });
                    setTimeout(function() {
                        $('#nav-login-btn').trigger('click');
                    }, 1400)
                }, 300)

            } else {
                alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
            }
        }, 'json');
    });

    $resetPwCancel.on('click', function() {

        $.post("/controller/pwFindCancel.php", { email: localStorage.getItem('email')}, function(data) {

            alertBoxPopAndOut($('.alert-'+ data.alertType), data.msg);

            localStorage.clear();

            setTimeout(() => {
                location.href = "/controller/home.php";
            }, 2000);

        }, 'json'); 
    });
})();