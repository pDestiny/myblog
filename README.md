# 블로그

데이터베이스 스키마

1. DASHBOARD
    - ID(INT)
    - TITLE(VARCHAR)
    - NICKNAME(INT)
    - WRITTEN(TIMESTAMP)
    - MD_CONTENT(TEXT)
    - HTML_CONTENT(TEXT)
2. USERS
    - USER_ID(INT)
    - EMAIL(VARCHAR)
    - PASSWORD(VARCHAR)
    - PW_RECOVER_CD(VARCHAR)
    - POSITION(VARCHAR)
3. REPLY
    - REPLY_ID(INT)
    - DASH_ID(INT)
    - USER_ID(INT)
    - CONTENT(TEXT)
    - REPLY_TO(INT)
    - REPLY_LEVEL(INT)
    - REPLY_TO_USER(VARCHAR)

## 블로그에서 가장 신경쓴 부분

1. form 슬라이더

1. 패스워드 찾기

2. 무한계층 댓글 달기

### 1. form 슬라이더 만들기

사용자가 자신의 정보를 수정하려고 할 때 자신이 원하는 데이터만 수정하고 싶을 것이라 생각하였다. 그래서 슬라이더를 사용하여 수정을 원하는 데이터를 가리키는 버튼을 클릭하면, 수정을 할 수 있는 폼이 슬라이드로 내려오는 기능을 만들었다.

![사용자 정보 수정 슬라이더](/images/edit_account.gif)

1. 슬라이더 작동 방식

    ```javascript
        //사용자 버튼 클릭 이벤트 리스너
        $('.btn-form').on('click', function () {

            //슬라이더 작동중에 방
            해를 막기위한 if 구문
            if($editFormSliderCnt.data('isWorking') === 'yes') {
                return;
            }

            //현재 폼의 아이디를 가져온다.
            let currentFormId = $currentForm.attr('id');

            // 클릭된 버튼 데이터의 아이디를 가져온다.
            let thisId = $(this).attr('id');

            // 현재 슬라이더의 상태를 작동중으로 변경한다.
            $editFormSliderCnt.data("isWorking", 'yes');

            //보이는 현재 폼과 클릭한 버튼의 맵핑이 일치하지 않을 경우 슬라이드 
            if (!currentFormId.includes(thisId)) {

                //상단에 있는 폼을 아래로 내린다.
                $('#' + thisId + '-form').css('display', 'block').animate({
                    top: "40%"
                }, 'fast');

                //현재 보이는 폼은 아래로 내린다.
                $currentForm.animate({top : "140%"}, "fast");
                
                //아래로 내린 보였던 폼은 display: none으로 바꾸고 다시 다시 상단으로 끌어 올린다.
                setTimeout((function ($current) {
                    return function () {
                        $current.css({display: "none"}).animate({top: "-30%"}, 'fast');
                        //모든 작업이 끝났을 때 슬라이더 상태를 작업 중단 상태로 만든다.
                        $editFormSliderCnt.data("isWorking", 'no');
                    }
                })($currentForm), 600);

                //현재 폼을 내려온 폼으로 교체한다.
                $currentForm = $('#' + thisId + "-form");

                //change 버튼과 post 될 form 데이터를 맵핑시킨다.
                $changeBtn.data('type', $currentForm.data('type'));
            } else {
                //현재 보이는 폼과 클릭한 버튼이 맵핑이 일치할 경우 슬라이더의 상태를 작동 중단으로 만들고 끝난다.
                $editFormSliderCnt.data('isWorking', 'no');
            }
    });

    ```


### 2. 패스워드 찾기

1. 패스워드를 찾고자 하는 사용자가 자신의 이메일을 입력한다.

![이메일 입력](/images/email_send.jpg)
    

2. 입력한 이메일 주소로 패스워드 변경에 필요한 URL을 보내준다. URL에 필요한 데이터는 사용자의 메일 주소와 md5로 암호화된 숫자, 알파벳 대소문자, 특수문자로 조합된 임의의 12문자이다.

    1. 랜덤으로 임의의 12문자를 만들어 낸다.(/model/UserModel.php)
        ```php
        public function geneartePwRecoverCd()  {
            //임의의 문자를 골라낼 배열
            $randomPickArr = [
                '1','2','3','4','5','6','7','8','9','0', 
                'A','B','C','D','E','F','G','H','I','J'.... 
            ];
            //실제 임의의 문자를 저장할 변수
            $randomPw = '';

            //for문을 통해 임의의 데이터를 randomPickArr로부터 뽑아내어 저장한다.
            for($i = 0; $i < 12; ++$i) {
                $randomPw .= $randomPickArr[rand(0, count($randomPickArr) - 1)];
            }
            //만들어진 임의의 문자들을 저장한다.
            $this->pw_recover_cd = $randomPw;

            //UserModel 객체 스스로를 반환 시킨다.
            return $this;
        }
        ```
    2. 생성된 12문자를 데이터베이스에 md5로 저장하고 해시된 임의의 숫자를 반환한다.(/model/UserModel.php)

        ```php
        public function  savePwRecoverCd($email) {

            //인자값으로 이메일을 받아 데이터를 저장한다.
            $sql = 'UPDATE USERS SET PW_RECOVER_CD = :PW_RECOVER_CD WHERE EMAIL = :EMAIL';

            $this->db->setSQL($sql);
            //저장된 pw_recover_cd 데이터를 md5로 해쉬한다.
            $pw_recover_cd_hashed = md5($this->pw_recover_cd);
            //geneartePwRecoverCd 에서 저장된 pw_recover_cd를 db에 저장한다.
            $this->db->bindValues([
                ':PW_RECOVER_CD' => $pw_recover_cd_hashed,
                ":EMAIL" => $email
            ]); 
            //헤쉬된 데이터를 반환한다.
            return $pw_recover_cd_hashed;
        } 
        ```

    3. controller의 findPassword.php에서 해시된 코드를 받아 이메일로 보낸다.

        ```php 
            $pw_recover_hashed = (new UserModel)
            ->geneartePwRecoverCd()
            ->savePwRecoverCd($toEmail);

            $mail = new PHPMailer(true);
            .... //데이터를 url로 만들어 메일로 보낸다.(네이버 stmp 사용)
        ```

3. 사용자가 자신의 메일로 로그인하고 들어가 전달된 url을 클릭하면, url에 있는 이메일 주소와 암호화된 md5 코드와 일치할 경우 패스워드를 변경할 수 있는 페이지를 출력한다.

    1. 사용자가 메일을 받는다.

    ![비밀번호 찾기 이메일](/images/find_password_mail.jpg)

    2. 사용자가 아래와 같은 URL을 클릭한다.

        http://pdestiny.xyz/controller/resetPassword.php?email=email@domain.com&pw_recover_cd=md5_arbitrary_cd

    3. email과 데이터베이스에 있는 PW_RECOVER_CD 컬럼에 있는 데이터가 일치하는지 확인한다.(controller/resetPassword.php)

        ```php
            if($pw_recover_cd === $user->getPwRecoverCdOf($email)['PW_RECOVER_CD']) {
                //pw_recover_cd와 데이터 베이스의 해당 이메일과 일치하는 PW_RECOVER_CD가 있다면

                //새로운 비밀번호를 생성하는 페이지를 보여준다.
                $m->setTemplate("reset_password")->render([
                'email' => $email,
                'pw_recover_cd' => $pw_recover_cd,
                "img" => '/view/img/home-bg.jpg'
                ]);

                exit;
            } 
        ```

    5. 패스워드 리셋 페이지를 출력한다.
        ![리셋 비밀번호](/images/reset_password.jpg)

    
4. 사용자가 새로운 패스워드를 클릭하면 url에 있는 이메일 주소와, 폼에서 작성한 패스워드, 그리고 암호 코드를 다시한번 서버로 보내어 확인하여 일치할경우 패스워드를 변경하고 사용자의 PW_RECOVER_CD 컬럼에 있는 암호화 데이터를 삭제한다.

    1. 새로운 비밀번호를 입력하여 보냈을 경우 다시한번 이메일과 pw_recover_cd가 맞는지를 확인한다. 맞지 않을 경우 에러메시지를 JSON 형식으로 보낸다.(controller/changePassword.php)

        ```php
            if($pw_recover_cd !== $user->getPwRecoverCdOf($email)['PW_RECOVER_CD']){
                UtilsHelper::sendAjaxMsg("danger", "Invalid Access. password recover codes don't match");

                exit; 
            }
        ```
    2. 모든 조건이 일치할 경우 데이터베이스에 있는 pw_recover_cd를 삭제하고 패스워드를 업데이트한다.(controller/changePass.php)

        ```php
            //pw_recover_cd 컬럼에 있는 데이터를 삭제한다.(UserModel.php)
            $user->deletePwRecoverCd($email);
            //새로 생성한 비밀번호를 데이터 베이스에 업데이트 한다.(UserModel.php)
            $user->updatePassword($password1, $email);
        ```
    3. 모든 절차가 정상적으로 끝났을 경우 로그인 모달을 보여준다.

        ![로그인](/images/login.png)

### 3. 무한계층 댓글 달기

뎁스가 무한대로 늘어날 수 있는 댓글을 만들기 위해서 트리구조를 이용했다.

1. 데이터 베이스를 트리구조로 가져오기 위해서 REPLY_ID 와 REPLY_TO 컬럼을 이용한다. 댓글을 달 경우 REPLY_TO에 댓글 단 상위 댓글의 REPLY_ID를 저장한다. 예를 들자면 아래의 표와 같다.

    <table>
        <thead>
            <th>REPLY_ID</th>
            <th>DASH_ID</th>
            <th>USER_ID</th>
            <th>CONTENT</th>
            <th>REPLY_TO</th>
            <th>REPLY_LEVEL</th>
            <th>REPLY_TO_USER</th>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>reply 1-1</td>
                <td>NULL</td>
                <td>1</td>
                <td>self</td>
            </tr>
            <tr>
                <td>2</td>
                <td>1</td>
                <td>2</td>
                <td>reply 2-1</td>
                <td>1</td>
                <td>2</td>
                <td>nickname1</td>
            </tr>
            <tr>
                <td>3</td>
                <td>1</td>
                <td>3</td>
                <td>reply 3-1</td>
                <td>2</td>
                <td>3</td>
                <td>nickname2</td>
            </tr>
            <tr>
                <td>4</td>
                <td>1</td>
                <td>4</td>
                <td>reply 1-2</td>
                <td>NULL</td>
                <td>1</td>
                <td>nickname3</td>
            </tr>
        </tbody>
    </table>

2. 데이터베이스에서 데이터를 가져와 트리구조로 데이터를 만들어 낸다. (/model/ReplyModel.php)

    1. 해당 DASHBOARD의 ID와 일치하는 REPLY_LEVEL이 1인 모든 데이터를 가져온다.(/model/ReplyModel.php)

        ```php
            public function getReplise($dashboard_id) {

                //트리구조의 데이터를 저장할 result 배열 선언
                $result = [];
                //REPLY_LEVEL 이 1인 인자값으로 받은 $dashboard_id와 DASH_ID 가 일치하는 데이터를 모두 가져온다.
                $sql = "SELECT R.REPLY_ID, R.DASH_ID, U.NICKNAME, R.CONTENT, R.REPLY_TO, R.REPLY_LEVEL, R.REPLY_TO_USER 
                    FROM REPLY AS R, USERS AS U 
                    WHERE R.REPLY_LEVEL='1' AND R.USER_ID = U.USER_ID AND R.DASH_ID = :DASH_ID";

                $this->db->setSQL($sql);

                $this->db->bindValues([
                    ':DASH_ID' => $dashboard_id
                ]);
                //가져온 댓글들에 대댓글이 달렸는지확인하는 while 문
                while($row = $this->db->getDataByRow(PDO::FETCH_ASSOC)) {
                    $obj = new stdClass;

                    //로우에 REPLY_REPLY라는 대댓글을 할당할 항목에 배열을 할당.
                    $row['REPLY_REPLY'] = [];

                    //가져온 row를 선언한 객체에 저장한다.
                    $obj->row = $row;

                    //result에 obj를 담는다.
                    $result[] = $obj;

                    //가져온 하나의 댓글에 댓글이 달렸는지 확인하기 위한 replyTraverse

                    $this->replyTraverse($obj, $row['REPLY_ID'], $dashboard_id);
                }
                //트리구조로 생성된 데이터 반환
                return $result;
            }
        ```

    2. 데이터베이스에 가져온 REPLY_LEVEL=1인 데이터들을 하나씩 순회하면서 댓에 댓글이 달렸는지 확인하고 데이터에 기록한다.

        ```php
            private function replyTraverse(stdClass $reply_obj, $reply_id, $dashboard_id) {

                //새로운 데이터베이스 객체를 가져온다. 그렇지 않으면 상위 트리에서 유지되어야 할 데이터베이스가 유지되지 않는다.
                $db = new DatabaseHelper;

                //인자값으로 넘긴 상위 댓글의 REPLY_ID가 REPLY_TO에 있는 댓글들을 가져온다.
                $sql = "SELECT R.REPLY_ID, R.DASH_ID, U.NICKNAME, R.CONTENT, R.REPLY_TO, R.REPLY_TO_USER, R.REPLY_LEVEL  
                    FROM REPLY AS R, USERS AS U 
                    WHERE R.REPLY_TO = :REPLY_TO AND R.USER_ID = U.USER_ID AND R.DASH_ID = :DASH_ID";
                
                $db->setSQL($sql);

                $db->bindValues([
                    ":REPLY_TO" => $reply_id,
                    ":DASH_ID" => $dashboard_id
                ]);
                //각각의 가져온 대댓글에서 다시 댓글이 있는지 확인하는 while문
                while($row = $db->getDataByRow(PDO::FETCH_ASSOC)) {
                    $newObj = new stdClass;
                    $newObj->row = $row;
                    //인자값으로 받은 상위 댓글의 REPLY_REPLY 배열에 새로운 객체를 넣는다.
                    $reply_obj->row['REPLY_REPLY'][] = $newObj;
                    $newObj->row['REPLY_REPLY'] = [];
                    
                    //다시 replyTraverse 메서드를 호출하여 동일한 방식으로 댓글이 있는지 여부를 확인하고 데이터를 추가한다.
                    $this->replyTraverse($newObj, $row['REPLY_ID'], $dashboard_id);
                }
            }
        ```

    3. 위의 두 메서드를 호출해 나온 결과 값은 다음과 같다.

        ```javascript
            [
                {
                    "row": {
                            "REPLY_ID": "1",
                            "DASH_ID": "1",
                            "NICKNAME": "self",
                            "CONTENT": "reply 1-1",
                            "REPLY_TO": "0",
                            "REPLY_LEVEL": "1",
                            "REPLY_TO_USER": "self",
                            "REPLY_REPLY": [
                                {
                                "row": {
                                    "REPLY_ID": "2",
                                    "DASH_ID": "1",
                                    "NICKNAME": "nickname2",
                                    "CONTENT": "reply 2-1",
                                    "REPLY_TO": "1",
                                    "REPLY_LEVEL": "2",
                                    "REPLY_TO_USER": "self",
                                    "REPLY_REPLY": [
                                            {
                                                "row": {
                                                    "REPLY_ID": "3",
                                                    "DASH_ID": "1",
                                                    "NICKNAME": "nickname3",
                                                    "CONTENT": "reply 3-1",
                                                    "REPLY_TO": "2",
                                                    "REPLY_LEVEL": "3",
                                                    "REPLY_TO_USER": "nickname2",
                                                    "REPLY_REPLY": []
                                                }
                                            }
                                        ]
                                    }
                                }            
                            ]
                    }
                },
                {
                    "row": {
                            "REPLY_ID": "4",
                            "DASH_ID": "1",
                            "NICKNAME": "nickname3",
                            "CONTENT": "reply 1-2",
                            "REPLY_TO": "0",
                            "REPLY_LEVEL": "1",
                            "REPLY_TO_USER": "nickname3",
                            "REPLY_REPLY": []
                        }
                }  
            ]
        ```

3. 위와 같이 트리 형식으로 계층화된 데이터를 클라이언트로 보내어 같은 트리의 순회 형식을 통해 순서대로 댓글을 동적으로 생성해 낸다.
        
    1. 재귀를 통해 가져온 트리구조의 데이터를 순회한다.
        ```javascript
            let addReplise = function(replise) {

                //첫 번째 Level에 있는 데이터를 하나씩 REPLY_REPLY 항목을 체크하여 배열에 다른 댓글이 있는지를 확인한다.
        
                for(let reply of replise) {
                    
                    
                    let replyFormat = new Reply;

                    replyFormat.replyId = reply.row.REPLY_ID;
                    replyFormat.dashboardId = reply.row.DASH_ID;
                    replyFormat.writer = reply.row.NICKNAME;
                    replyFormat.content = reply.row.CONTENT;
                    replyFormat.replyTo = reply.row.REPLY_TO;
                    replyFormat.replyLevel = reply.row.REPLY_LEVEL;
                    replyFormat.replyToUser = reply.row.REPLY_TO_USER;


                    //재귀를 통해 더이상 댓글이 없을 때까지 댓글의 레벨이 내려간다.
                    addReplise(reply.row.REPLY_REPLY);

                    //실제로 DOM에 댓글을 입히는 작업을 한다.
                    addReply(replyFormat);
                    
                }
            }
        ```
    2. 실제 DOM에 데이터를 입힌다.

        ```javascript
            let addReply = function(reply) {

                if(!reply instanceof Reply)  throw new Error("argument must be instance of Reply");
                //미리 만들어둔 댓글 템플릿을 복제한다.
                let $template = $('.reply.template').clone();

                //template 클래스가 display: none으로 설정되어 있어 template 클래스를 삭제한다.
                $template.removeClass("template");

                
                //템플릿에 가져온 데이터를 입힌다.
                $template.data("replyId", reply.replyId);
                $template.data('writer', reply.writer);
                $template.data('content', reply.content);
                $template.data("replyTo", reply.replyTo);
                $template.attr("data-reply-level", reply.replyLevel);
                $template.data('replyToUser', reply.replyToUser);

                //템플릿에 실제로 보여주는 데이터를 입힌다.
                $template.find(".view > .card-header > span:first-child").text(reply.writer);
                $template.find(".view > .card-header > span:last-child").text(reply.replyToUser);
                $template.find(".view > .card-body > .content").text(reply.content);

                //템플릿을 위에서부터 추가해나간다.
                $('.reply-list').prepend($template);
            }
        ```
4. 서버에서 댓글을 가져올 때 댓글 숫자단위로 쿼리를 날리는 것은 비 효율적이라 생각하여 전체 댓글을 가져온 후 가공하여 클라이언트로 보내는 업데이트를 할 예정이다.














