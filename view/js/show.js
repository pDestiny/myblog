(function() {
    //definition space
    //reply format.
    function Reply () {
        this.replyId = '';
        this.writer = '';
        this.dashboardId = '';
        this.content = '';
        this.replyTo = '';
        this.replyToUser = '';
        this.replyLevel = "";
    }

    let blockTextarea = function($text) {
        $text.attr("disabled", ''); 
    }

    let saveUserToLocal = function() {
        $.get('/controller/getCurrentUserInfo.php', {}, function(userData) {
            if(userData.alertType === 'success') {

                localStorage.setItem("email", userData.email);
                localStorage.setItem('nickname', userData.nickname);
                localStorage.setItem('userId', userData.user_id);
                localStorage.setItem("dashboardId", $('.article').data("dashboardId"));
                localStorage.setItem("dashboardWriter", $(".article").data('ariticleWriter'));

            } else {
                blockTextarea($(".card textarea[name='content']"));
            }
        }, 'json');
    }

    let replyInit = function() {
        $.get('/controller/getReplyData.php', {dashboardId: $('.article').data("dashboardId")},function(replise) {
            addReplise(replise)
        }, 'json');
    }

    let setUserNameToWriteReplyForm = function() {
        $('.write-reply-form-box .card-header span:first-child').html(localStorage.getItem('nickname'))
    }

    let addReplise = function(replise) {
        
        for(let reply of replise) {
            let replyFormat = new Reply;
            replyFormat.replyId = reply.row.REPLY_ID;
            replyFormat.dashboardId = reply.row.DASH_ID;
            replyFormat.writer = reply.row.NICKNAME;
            replyFormat.content = reply.row.CONTENT;
            replyFormat.replyTo = reply.row.REPLY_TO;
            replyFormat.replyLevel = reply.row.REPLY_LEVEL;
            replyFormat.replyToUser = reply.row.REPLY_TO_USER;

            console.log(replyFormat.replyLevel);

            addReplise(reply.row.REPLY_REPLY);
            addReply(replyFormat);
        }

    }

    let postReplyAjax = function(reply, callback) {
        if(!reply instanceof Reply || !typeof callback === 'Function') {
            throw new Error("reply must be instance of Reply or callback is not a function");
        }
        $.post("/controller/postReply.php", reply, callback);
    }

    let addReply = function(reply) {
        if(!reply instanceof Reply)  throw new Error("argument must be instance of Reply");
        let $template = $('.reply.template').clone();

        //template data setting
        $template.removeClass("template");

        $template.data("replyId", reply.replyId);
        $template.data('writer', reply.writer);
        $template.data('content', reply.content);
        $template.data("replyTo", reply.replyTo);
        $template.attr("data-reply-level", reply.replyLevel);
        $template.data('replyToUser', reply.replyToUser);

        //template view setting
        $template.find(".view > .card-header > span:first-child").text(reply.writer);
        $template.find(".view > .card-header > span:last-child").text(reply.replyToUser);
        $template.find(".view > .card-body > .content").text(reply.content);

        //post data
        $('.reply-list').prepend($template);
    }

    //reply to article submit area

    let replyToArticlePostByAjax = function(reply, callback) {
        if(!reply instanceof Reply) throw new Error('reply must be instance of Reply');
        if(typeof callback !== 'function') throw new Error('callback must be a function');
        $.post("/controller/postReply.php", reply, callback, 'json');
    }

    let replySubmit = function() {
        let reply = new Reply();

        reply.content = $('.write-reply-form-box').find("textarea").val();
        reply.dashboardId = localStorage.getItem('dashboardId');
        reply.replyId = '';
        reply.replyLevel = 1;
        reply.replyTo = null;
        reply.replyToUser = $('.article').data("articleWriter");
        reply.writer = localStorage.getItem('nickname');

        replyToArticlePostByAjax(reply, function(data) {
            if(data.alertType === 'success') {
                reply.replyId = data.lastInsertId;
                reply.replyTo = data.lastInsertId;
                addReply(reply);
            } else {
                throw new Error('replyToArticlePostByAjax failed');
            }
            
        });

        $('.write-reply-form-box').find("textarea").val('');
    }

    let showReply = function($replyElem) {
        if(!$replyElem) throw new Error("showReply function needs an argument");

        $replyElem.find(".reply-to-form .card-header > span:first-child").text(localStorage.getItem('nickname'));
        $replyElem.find(".reply-to-form .card-header > span:last-child").text($replyElem.data('writer'));

        $replyElem.attr('data-mode', "reply-to");
    }

    let addReplyToReply = function(reply, $replyElem) {
        if(!reply instanceof Reply) throw new Error("reply must be instance of Reply");

        if(!$replyElem) throw new Error("addReplyToReply needs two arguments");

        let $template = $('.reply.template').clone();

        //template data setting
        $template.removeClass("template");

        $template.data("replyId", reply.replyId);
        $template.data('writer', reply.writer);
        $template.data('content', reply.content);
        $template.data("replyTo", reply.replyTo);
        $template.attr("data-reply-level", reply.replyLevel);
        $template.data('replyToUser', reply.replyToUser);

        //template view setting
        $template.find(".view > .card-header > span:first-child").text(reply.writer);
        $template.find(".view > .card-header > span:last-child").text(reply.replyToUser);
        $template.find(".view > .card-body > .content").text(reply.content);

        $replyElem.after($template);
        $replyElem.attr("data-mode", 'view');
    }

    let submitReplyToReply = function($replyElem) {
        let reply = new Reply();

        reply.content = $replyElem.find(".reply-to-form textarea").val();
        reply.replyId = '';
        reply.replyTo = $replyElem.data('replyId');
        reply.replyLevel = ($replyElem.data('replyLevel') - '') + 1;
        reply.dashboardId = localStorage.getItem('dashboardId');
        reply.replyToUser = $replyElem.data("writer");
        reply.writer = localStorage.getItem('nickname');

        replyToArticlePostByAjax(reply, function(data) {
            if(data.alertType === 'success') {
                reply.replyId = data.lastInsertId;
                addReplyToReply(reply, $replyElem)
            } else {
                throw new Error('replyToArticlePostByAjax execution failed');
            }
        });
    }

    let showEditReply = function($replyElem) {
        if(!$replyElem) throw new Error('showEditReply needs a one argument');

        if($replyElem.data('writer') !== localStorage.getItem('nickname')) {
            alertBoxPopAndOut($('.alert-info'), "You are not allowed to edit this reply");
        } else {
            $replyElem.attr("data-mode", "modify");
            $replyElem.find(".card.modify span:first-child").text(localStorage.getItem('nickname'));
            $replyElem.find(".card.modify span:last-child").text($replyElem.data("replyToUser"));
            $replyElem.find('.card.modify textarea').val($replyElem.data('content'));
        }
    }

    let deleteReply = function($replyElem) {
        if(!$replyElem) throw new Error('showEditReply needs a one argument');


        let replyLevel = $replyElem.data("replyLevel");
        
        let errors = []

        let promise = new Promise(function(resolve, reject) {
            $replyElem.nextUntil("[data-reply-level='1']").each(function(idx){
                $.post("/controller/deleteReply.php", {replyId: $(this).data("replyId")}, data => {
                    if(data.alertType !== 'success') {
                        reject({
                            alertType: data.alertType,
                            msg: data.msg
                        })
                    } else {
                        $(this).remove();
                    }
                }, 'json')
            });
            resolve()
        })

        promise.then(function() {
            return new Promise(function(resolve, reject) {
                $.post('/controller/deleteReply.php', {replyId: $replyElem.data('replyId')},  data => {
                    if(data.alertType === 'success') {
                        $replyElem.remove();
                        resolve('The reply has been deleted');
                    } else {
                        reject({
                            alertType: data.alertType,
                            msg: data.msg
                        });
                    }
                }, 'json');
                
            });
        }).then(successMsg => {
            alertBoxPopAndOut($('.alert-success'), successMsg);
        }).catch(failObj=> {
            alertBoxPopAndOut($('.alert-' + failObj.alertType), failObj.msg);
        })
    }

    let cancelEditReply = function($replyElem) {
        if(!$replyElem) throw new Error('showEditReply needs a one argument');

        $replyElem.attr("data-mode", 'view');
    }

    let submitEditReply = function($replyElem) {
        if(!$replyElem) throw new Error('showEditReply needs a one argument');

        let submitData = {
            replyId: $replyElem.data('replyId'),
            content: $replyElem.find('.modify textarea').val()
        }

        $.post("/controller/editReply.php", submitData, function(data) {
            if(data.alertType === "success") {
                $replyElem.find('.view .content').html(submitData.content);
                $replyElem.data('content', $replyElem.find('.modify textarea').val());
                $replyElem.attr("data-mode", 'view');
                alertBoxPopAndOut($('.alert-success'), data.msg);
            } else {
                alertBoxPopAndOut($('.alert-' + data.alertType), data.msg);
            }
        }, 'json');
        
    }

    let cancelReplyToReplyForm = function($replyElem) {

        $replyElem.find(".reply-to-form textarea").val('');
        
        $replyElem.attr("data-mode", 'view');
    }
//event handlers;
    //reply to aritcle btn click event handler
    let replySubmitBtnClicked = function(e) {
        let $replyElem = $(e.target).closest('.reply');
        replySubmit($replyElem);
    }
    //show reply to reply form btn event handler
    let showReplyToReplyFormBtnClicked = function(e) {
        let $replyElem = $(e.target).closest('.reply');

        showReply($replyElem);
        $replyElem.find('.card.reply-to-form textarea').val('');
    }
    let submitReplyToReplyClicked = function(e) {
        let $replyElem = $(e.target).closest('.reply');

        submitReplyToReply($replyElem);
    }
    let showEditReplyBtnClicked = function(e) {
        let $replyElem = $(e.target).closest('.reply');
        
        showEditReply($replyElem);
    }

    let deleteReplyBtnClicked = function(e) {
        let $replyElem = $(e.target).closest('.reply');

        deleteReply($replyElem);
    }

    let cancelEditReplyBtnClicked = function(e) {
        let $replyElem = $(e.target).closest('.reply');

        cancelEditReply($replyElem);
    }

    let submitEditReplyBtnClicked = function(e) {
        let $replyElem = $(e.target).closest('.reply');

        submitEditReply($replyElem);
    }

    let cancelReplyToReplyFormBtnClicked = function(e) {
        let $replyElem = $(e.target).closest('.reply');

        cancelReplyToReplyForm($replyElem);
    }

//working space

    //initial stage when documnet ready;
    saveUserToLocal();
    
    setUserNameToWriteReplyForm();

    replyInit();


    //event area

    //basic reply submit btn click event;
    $('.write-reply-form-box').on("click", ".reply-submit", replySubmitBtnClicked);

    //reply to reply show evnt 
    $('.container').on('click', ".reply .card.view .btn-group .reply-reply", showReplyToReplyFormBtnClicked);

    //reply to reply submit click evt;

    $('.container').on('click', ".reply .reply-to-form .btn-group .reply-submit", submitReplyToReplyClicked);

    //reply to reply cancel click evt

    $(".container").on("click", ".reply .reply-to-form .btn-group .reply-cancel", cancelReplyToReplyFormBtnClicked);

    //showing edit form btn click evt;

    $('.container').on('click', ".reply .view .btn-group .reply-edit", showEditReplyBtnClicked);

    //eidt box submit btn click event;

    $('.container').on('click', ".reply .modify .btn-group .reply-submit", submitEditReplyBtnClicked);
    
    //edit box cancel btn click event;

    $(".container").on('click', ".reply .modify .btn-group .reply-cancel", cancelEditReplyBtnClicked); 

    //delete edit btn clicked

    $(".container").on('click', ".reply .view .btn-group .reply-delete", deleteReplyBtnClicked);

//
})()