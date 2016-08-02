<style>
    tr th,td{
        vertical-align: middle!important;
    }
</style>

<table class="table table-striped table-bordered">
    <caption class="caption text-center text-danger"><h4><strong>企业号用户管理</strong></h4>
        <button class="btn btn-success pull-right" style="cursor: pointer"  data-toggle="modal"
                data-target="#addUserModal">创建用户</button></caption>
<!--    <thead class="text-nowrap">-->
<!--    <th>ID</th>-->
    <th style="width: 40px;height: 40px">头像</th>
    <th>姓名</th>
    <th>手机</th>
    <th>邮箱</th>
    <th>微信号</th>
    <th>部门ID</th>
    <th>关注状态</th>
    <th>操作</th>
    </thead>
    <tbody>
    <?php if(isset($users))foreach ($users as $user){; ?>
        <tr class="bg-success">
<!--            <td>--><?php //echo isset($user['userid'])?$user['userid']:"";?><!--</td>-->
            <td><?php echo isset($user['avatar'])?"<img width='40px' height='40px' src='".$user['avatar']."64'>":"";?></td>
            <td><?php echo isset($user['name'])?$user['name']:"";?></td>
            <td><?php echo isset($user['mobile'])?$user['mobile']:"";?></td>
            <td><?php echo isset($user['email'])?$user['email']:"";?></td>
            <td><?php echo isset($user['weixinid'])?$user['weixinid']:"";?></td>
            <td><?php echo isset($user['department'])?json_encode($user['department']):"";?></td>
            <td><?php echo isset($user['status'])?$user['status']:"";?></td>
            <td> <a class="btn btn-warning"
                    data-userid="<?php echo isset($user['mobile'])?$user['mobile']:"";?>"
                    data-name="<?php echo isset($user['name'])?$user['name']:"";?>"
                    data-email="<?php echo isset($user['email'])?$user['email']:"";?>"
                    data-weixinid="<?php echo isset($user['weixinid'])?$user['weixinid']:"";?>"
                    data-toggle="modal"
                    data-target="#editUserModal">
                修改
                </a></td>
        </tr>
    <?php };?>
    </tbody>
</table>

<!-- 模态框ADD新用户（Modal） -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog"
     aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="addUserLabel">
                    创建新用户
                </h4>
            </div>
            <div class="modal-body">
                <fieldset>
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
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left"
                        data-dismiss="modal">关闭
                </button>
                <button id="addUser" type="button" class="btn btn-primary">
                    添加
                </button>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal -->


<!-- 模态框修改用户信息（Modal） -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog"
     aria-labelledby="editUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="editUserLabel">
                    修改用户
                </h4>
            </div>
            <div class="modal-body">
                <form>
                    <input class="form-group form-control" id="e_userid" name="userid" type="hidden" readonly>
                    <input class="form-group form-control" id="e_name" name="name" type="text" placeholder="姓名">
                    <input  class="form-group form-control" id="e_email" name='email' type="email"  placeholder="邮箱">
                    <input  class="form-group form-control" id="e_weixinid" name='weixinid' type="text"  placeholder="微信号">
                </form>
            </div>
            <div class="modal-footer">
                <button id="deleteUser" class="btn btn-danger pull-left">删除用户</button>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">关闭
                </button>
                <button id="editUser" type="button" class="btn btn-primary">
                    修改
                </button>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal -->

<script>
    $(document).ready(function() {
        /*创建新用户*/
        $("#addUser").click(function () {
            if ($("#add_mobile").val()==""||
                $("#add_name").val()==""
            ){
                alert("请填写完所有必填项");
                return false;
            }
            $("#addUser").attr('disabled',"true");
            $.post("<?php echo $this->root;?>/EntUserAdmin/addUser",
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

//               /*修改用户*/
        $('#editUserModal').on('show.bs.modal', function(event) {
            $("#e_userid").val($(event.relatedTarget).data('userid'));
            $("#e_name").val($(event.relatedTarget).data('name'));
            $("#e_email").val($(event.relatedTarget).data('email'));
            $("#e_weixinid").val($(event.relatedTarget).data('weixinid'));
        });

        $("#editUser").click(function () {
            if ($("#e_userid").val()==""||
                $("#e_name").val()==""
            ){
                alert("请填写完所有必填项");
                return false;
            }
            $("#editUser").attr('disabled',"true");
            $.post("<?php echo $this->root;?>/EntUserAdmin/editUser",
                { userid:$("#e_userid").val(),
                    name: $("#e_name").val(),
                    weixinid:$("#e_weixinid").val(),
                    email:$("#e_email").val()
                },function(data){
                    alert(data);
                    $('#editUser').removeAttr("disabled");
                    if(data.indexOf("成功") >= 0 )
                    {
                        location.reload();
                    }

                });
        });



        /*删除用户*/
        $("#deleteUser").click(function(){
            if($("#e_userid").val==""){
                alert("userid不能为空");
                return false;
            }
            $.post("<?php echo $this->root;?>/EntUserAdmin/deleteUser",
                { userid: $("#e_userid").val()
                },function(data){
                    alert(data);
                    if(data.indexOf("成功") >= 0 )
                    {
                        location.reload();
                    }

                });
        });

    });
</script>