(function() {
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        $.get("/controller/admin/delete.php", {
            id, type
        }, function(data) {
            if(data.alertType === 'success') {
                location.href="/controller/admin/admin.php";
            } else {
                alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
            }
        }, 'json');
    });
})();