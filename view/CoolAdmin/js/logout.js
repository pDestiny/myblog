(function() {
    $('.logout-btn').on("click", function(e) {
        e.preventDefault();
        $.get("/controller/logout.php", {}, data => {
            if(data.alertType === 'success') {
               alertBoxPopAndOut($('.alert-success'), data.msg);
               setTimeout(() => {
                 location.href="/"
               }, 1800) 
            }
        }, 'json');
    });
})();   