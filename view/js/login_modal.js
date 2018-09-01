(function() {
    const $loginBtn = $('#loginModal .login-btn');
    const $findPwBtn = $(".find-pw-btn");
    
    $loginBtn.on("click", () => {
        let submitData = {
            "email": $('#loginModal #login-email').val(),
            "password": $('#loginModal #password').val()
        }

        console.dir(submitData);
        $.post("/controller/login.php", submitData, (data) => {
            if(data.alertType === 'success') {
                alertBoxPopAndOut($('.alert-success'), data.msg);
                setTimeout(() => {
                    location.href = "/controller/home.php";
                }, 1800)
            } else {
                alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
            }
        }, 'json');
    });

    $findPwBtn.on('click', () => {
        //form이 로그인 form에서 비밀번호 찾기 폼으로 변경
        $('#login-form').toggleClass('hidden');
        $('#find-pw-form').toggleClass('hidden');

        //버튼 그룹을 로그인 버튼에서 비밀번호 찾기 폼으로 변경
        $('.login-footer').toggleClass("hidden");
        $('.find-pw-footer').toggleClass("hidden");
    });

    $(".send-email-btn").on('click', () => {
        let submitData = {
            "email": $('#find-pw-email').val()
        } 
        $.post("/controller/findPassword.php", submitData, function(data) {
            if(data.alertType === 'success') {
                alertBoxPopAndOut($('.alert-success'), data.msg);
            } else {
                alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
            }
        }, 'json');
    });

    $(".cancel-find-pw").on('click', () => {
        //비밀번호 찾기 폼 -> 로그인 폼
        $('#login-form').toggleClass('hidden');
        $('#find-pw-form').toggleClass('hidden');

        //비밀번호 찾기 버튼 그룹 -> 로그인 버튼 그룹
        $('.login-footer').toggleClass("hidden");
        $('.find-pw-footer').toggleClass("hidden");
    });
})();