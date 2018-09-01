(function() {
     const $articleSubmit = $('.article-submit');
     const $articleCancel = $('.article-cancel');
     const $articleEdit = $('.article-edit');
     const $title = $('.article-form-cnt input[name="title"]');
     const $subtitle = $('.article-form-cnt input[name="subtitle"]');
     const $content = $('.article-form-cnt textarea[name="content"]');
     const $preview = $('.article-form-cnt .preview');

     $articleSubmit.on('click', function() {
        let submitData = {
            title: $title.val(),
            subtitle: $subtitle.val(),
            content: $content.val()
        }
    
        $.post("/controller/admin/saveArticle.php", submitData, function(data) {
            console.dir(data);
            if(data.alertType === 'success') {
                alertBoxPopAndOut($('.alert-success'), data.msg);
                setTimeout(() => {
                    location.href="/controller/admin/admin.php";
                }, 1800)
            } else {
                alertBoxPopAndOut($(".alert-" + data.alertType), data.msg);
            }
        }, 'json');
     });

    $articleCancel.on('click', function() {
        location.href="/controller/admin/admin.php";
    });

    $content.on('input', function() {
        $.get("/controller/admin/getParsedText.php", {
            text: $(this).val()
        }, function(data) {
            $preview.html(data.parsedText);
            $content.data('isWorking', 'N');
        }, 'json');
    });

    $articleEdit.on('click', function() {
        console.log('clicked!');
        let submitData = {
            title: $title.val(),
            subtitle: $subtitle.val(),
            content: $content.val(),
            id: $(this).data('dashboardId')
        }

        $.post("/controller/admin/editArticle.php", submitData, function(data) {
            if(data.alertType == "success") {
                alertBoxPopAndOut($('.alert-success'), data.msg);
                setTimeout(function() {
                    location.href = "/controller/admin/admin.php";
                }, 1800)
            } else {
                alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
            }
        }, 'json');
    });

    $content.on('scroll', function() {
        let top = $(this).scrollTop();
        $preview.scrollTop(top);
    });
})()