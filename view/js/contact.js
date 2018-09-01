(function() {

//definition for contact btn

    //contact btn event action
    let contactSubmit = $form => {
        let submitData = {
            nickname: $form.find("input[name='nickname']").val(),
            email: $form.find("input[name='email']").val(),
            content: $form.find("textarea[name='content']").val()
        }

        $.post("/controller/sendContactEmail.php", submitData, data => {
            if(data.alertType === 'success') {
                alertBoxPopAndOut($('.alert-success'), data.msg);
                setTimeout(() => {
                    location.href="/";
                }, 1800)
            } else {
                alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
            }
        }, 'json');
    }

    //contact btn event handler
    let contactSubmitBtnClicked = function(e) {
        e.preventDefault();
        let $form = $('#contactForm');

        contactSubmit($form);
    }

//events
    //contact form submit btn clicked

    $('.container .contact').find("[type='submit']").on('click', contactSubmitBtnClicked);
})();