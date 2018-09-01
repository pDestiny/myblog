(function() {
    const $logoutBtn = $("#logoutModal .logout-btn");

    $logoutBtn.on('click', function() {
        $.post('/controller/logout.php', data => {
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
})();