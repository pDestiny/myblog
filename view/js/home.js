(function() {

//definitions
let addPreview = function(preview) {
    console.dir(preview);
    let $template = $('.post-template').clone();

    $template.removeClass('d-none');

    $template.find('.post-preview > a').attr("href", "/controller/show.php?id=" + preview.ID);
    $template.find(".post-preview > a > .post-title").html(preview.TITLE);
    $template.find(".post-preview > a > .post-subtitle").html(preview.SUBTITLE);
    $template.find(".post-preview > .post-meta > .writer").html(preview.NICKNAME);
    $template.find(".post-preview > .post-meta > .written").html(preview.WRITTEN);

    $('body > .container').append($template);    
}

let getOlderPost = function($olderPostBtn) {
    $.get('/controller/getOlderPost.php', {
        start: $olderPostBtn.data('start')
    }, function(data) {
        if(data.alertType === "success") {

            if(data.previews.length != 0) {

                data.previews.forEach(function(preview) {
                    addPreview(preview);
                });

                $olderPostBtn.data('start', ($olderPostBtn.data('start') - '') + 5);

            } else {
                alertBoxPopAndOut($('.alert-info'), "No more articles");
            }
        } else {
            alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
        }
    }, 'json');
}

//older post btn click handler
let getOlderPostBtnClicked = function(e) {

    let $olderPostBtn = $(e.target);

    getOlderPost($olderPostBtn);
}

//events
    // order-post button click event
    $('.older-post-btn').on('click', getOlderPostBtnClicked);
})();