(function () {
    let $currentForm = $('#btn-1-form');
    let $changeBtn = $('#editAccountModal .change');
    let $emailInput = $('#btn1-email');
    let $nicknameInput = $('#btn2-nickname');
    let $password1Input = $('#btn3-password1');
    let $password2Input = $('#btn3-password2');
    let $editFormSliderCnt = $('#editAccountModal .edit-form-slider-cnt');
    //eventDelegation
    $('.btn-form').on('click', function () {
        if($editFormSliderCnt.data('isWorking') === 'yes') {
            return;
        }
        let currentFormId = $currentForm.attr('id');
        let thisId = $(this).attr('id');
        $editFormSliderCnt.data("isWorking", 'yes');
        if (!currentFormId.includes(thisId)) {
            $('#' + thisId + '-form').css('display', 'block').animate({
                top: "40%"
            }, 'fast');
            $currentForm.animate({top : "140%"}, "fast");
            
            setTimeout((function ($current) {
                return function () {
                    $current.css({display: "none"}).animate({top: "-30%"}, 'fast');
                    $editFormSliderCnt.data("isWorking", 'no');
                }
            })($currentForm), 600);

            $currentForm = $('#' + thisId + "-form");

            $changeBtn.data('type', $currentForm.data('type'));
        } else {
            $editFormSliderCnt.data('isWorking', 'no');
        }
    });

    $changeBtn.on('click', function(){

        const type = $(this).data('type');        

        if(type === 'email') {
            //서버로 데이터를 보낸다.
            $.post("/controller/editAccnt/changeEmail.php", {
                email: $emailInput.val()
            }, data => {
                if(data.alertType === 'success') {
                    alertBoxPopAndOut($('.alert-success'), data.msg);
                } else {
                    alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
                }
            }, 'json');

        }else if(type === 'nickname') {
            $.post("/controller/editAccnt/changeNickname.php", {
                nickname: $nicknameInput.val()
            }, data => {
                if(data.alertType === 'success') {
                    alertBoxPopAndOut($('.alert-success'), data.msg);
                } else {
                    alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
                }
            }, 'json');

        }else if(type === 'password') {
            //password는 먼저 서버로부터 pw_recover_cd를 가져온 후 reset_password 페이지를 제활용한다.
            $.post('/controller/editAccnt/changePw.php', { password1: $password1Input.val(), password2: $password2Input.val()}, function(data) {
                if(data.alertType === 'success') {
                    $.post('/controller/changePassword.php', {
                        email: data.email,
                        pw_recover_cd: data.pw_recover_cd,
                        password1: $password1Input.val(),
                        password2: $password2Input.val()
                    }, function(data2) {
                        alertBoxPopAndOut($('.alert-' + data2.alertType), data2.msg);
                    }, 'json')
                } else {
                    alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
                }
            }, 'json');
            
        }
    });
    $('#editAccountModal .close-modal').on('click', function() {
        $emailInput.val('');
        $nicknameInput.val('');
        $password1Input.val('');
        $password2Input.val('');
    });
    
})();