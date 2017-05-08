<meta charset="utf-8"/>
<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="./js/verify.js"></script>


<a id="ok">验证码</a>

<input type="button" value="提交" onclick="post() "/>
<script type="text/javascript" >
    function post()
    {
        var onlyAuthentication = $("#onlyAuthentication").val();
        $.post('/sec/verify/test1.php', {'onlyAuthentication': onlyAuthentication},
                    function(msg) {
                           alert(msg)
                    }
        );
    }
    function ab(){
    //  alert(1)
    }
    $("#ok").click(function(){

        _verfiyHtml('ab',1);
    })
</script>