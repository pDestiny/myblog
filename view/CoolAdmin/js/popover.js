(function() {
    $('.writer-popover').each(function(idx) {
        $.get("/controller/admin/getEmailAndNickname.php", {id: $(this).data('userId')}, (thisElem => {
            return function(data) {
                thisElem.attr("data-content", "Email: " + data.email + "<br>" + "Nickname : " + data.nickname);
            }
        })($(this)), 'json');
    });

    $('.writer-popover[data-toggle="popover"]').popover({html: true});

    $('.writer-popover[data-toggle="popover"]').on('click', function() {
        $('.writer-popover').not(this).popover('hide');
    });
})();