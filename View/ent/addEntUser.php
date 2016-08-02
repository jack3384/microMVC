            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">添加用户</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <input class="form-control" placeholder="手机号码（必填）" id="add_mobile" name="mobile" pattern="^1\d{10}$"
                               title="填写11位手机号码" required="required">
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="姓名（必填）" id="add_name" name="name" type="text" required="required">
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="电子邮件(可不填)" id="add_email" name="email" type="email">
                    </div>
                    <div class="form-group">
                        <input class="form-control" placeholder="微信号(可不填)" id="add_weixinid" name="weixinid" type="text">
                    </div>
            </div>
            <div class="form-group">
                <button class="form-control btn btn-primary" id="addUser" type="button">
                    添加
                </button>
            </div>

            </div>

<script>
    $(document).ready(function() {
        $('title').text("添加用户");
        /*创建新用户*/
        $("#addUser").click(function () {
            if ($("#add_mobile").val()==""||
                $("#add_name").val()==""
            ){
                alert("请填写完所有必填项");
                return false;
            }
            $("#addUser").attr('disabled',"true");
            $.post("http://"+window.location.host+window.location.pathname,
                { mobile: $("#add_mobile").val(),
                    name: $("#add_name").val(),
                    weixinid:$("#add_weixinid").val(),
                    email:$("#add_email").val()
                },function(data){
                    alert(data);
                    $('#addUser').removeAttr("disabled");
                    if(data.indexOf("成功") >= 0 )
                    {
                        location.reload();
                    }
                });
        });
    });
</script>