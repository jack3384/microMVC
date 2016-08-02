    <table class="table table-striped table-bordered">
        <caption class="caption text-center text-danger"><h4><strong>用户信息管理</strong></h4>
            <button class="btn btn-success pull-right" style="cursor: pointer"  data-toggle="modal"
                    data-target="#addUserModal">创建用户</button></caption>
        <thead class="text-nowrap">
        <th>ID</th>
        <th>账号</th>
        <th>Email</th>
        <th>权限</th>
        <th>操作</th>
        </thead>
        <tbody>
        <?php if(isset($users)) foreach ($users as $user){; ?>
        <tr class="bg-success">
            <td><?php echo $user['id'];?></td>
            <td><?php echo $user['username'];?></td>
            <td><?php echo $user['email'];?></td>
            <td><?php echo $user['level'];?></td>
            <td> <a class="btn btn-warning"
                    <?php if($user['level']>=10){;?>

                        disabled="disabled"
                <?php }else{;?>

                    data-id="<?php echo $user['id'];?>"
                    data-username="<?php echo $user['username'];?>"
                    data-email="<?php echo $user['email'];?>"
                    data-level="<?php echo $user['level'];?>"
                    data-toggle="modal"
                    data-target="#editUserModal"
                 <?php };?>
                ">
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
                    <form>
                    <input class="form-group form-control" id="add_username" name="username" type="text" placeholder="账号">
                    <input  class="form-group form-control" id ="add_password" name='password' type="password"  placeholder="密码">
                    <input  class="form-group form-control" id="add_confirmPassword" name="confirmPassword" type="password"  placeholder="确认密码">
                    <input  class="form-group form-control" id="add_email" name='email' type="email"  placeholder="邮箱">
                    <input  class="form-group form-control" id="add_level" name='level' type="number"  min=0 max=10 placeholder="等级：10为管理员">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">关闭
                    </button>
                    <button id="addUser" type="button" class="btn btn-primary">
                        提交
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
                            <input class="form-group form-control" id="e_id" name="id" type="hidden" placeholder="ID" readonly>
                            <input class="form-group form-control" id="e_username" name="username" type="text" value="1" placeholder="账号" readonly>
                            <input  class="form-group form-control" id ="e_password" name='password' type="password"  placeholder="密码（不修改留空）">
                            <input  class="form-group form-control" id="e_confirmPass" name="confirmPass" type="password"  placeholder="确认密码（不修改留空）">
                            <input  class="form-group form-control" id="e_email" name='email' type="email"  placeholder="邮箱">
                            <input  class="form-group form-control" id="e_level" name='level' type="number"  min=0 max=10 placeholder="等级：10为管理员">
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
                    if ($("#add_username").val()==""||
                        $("#add_password").val()==""||
                        $("#add_confirmPassword").val()==""||
                        $("#add_level").val()==""
                    ){
                        alert("请填写完所有必填项");
                        return false;
                    }
                    if($("#add_password").val()!=$("#add_confirmPassword").val()){
                        alert("两次输入的新密码不一致");
                        return false;
                    }
                    $("#addUser").attr('disabled',"true");
                    $.post("<?php echo $this->root;?>/Admin/addUser",
                        { username: $("#add_username").val(),
                            password: $("#add_password").val(),
                            level:$("#add_level").val(),
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
                    $("#e_id").val($(event.relatedTarget).data('id'));
                    $("#e_username").val($(event.relatedTarget).data('username'));
                    $("#e_email").val($(event.relatedTarget).data('email'));
                    $("#e_level").val($(event.relatedTarget).data('level'));
                });

                $("#editUser").click(function () {
                    if ($("#e_id").val()==""||
                        $("#e_username").val()==""||
                        $("#e_level").val()==""
                    ){
                        alert("请填写完所有必填项");
                        return false;
                    }
                    if($("#e_password").val()!=$("#e_confirmPass").val()){
                        alert("两次输入的密码不一致");
                        return false;
                    }
                    $("#editUser").attr('disabled',"true");
                    $.post("<?php echo $this->root;?>/Admin/editUser",
                        { id:$("#e_id").val(),
                            username: $("#e_username").val(),
                            password: $("#e_password").val(),
                            level:$("#e_level").val(),
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
                    if($("#e_id").val==""){
                        alert("ID不能为空");
                        return false;
                    }
                    $.post("<?php echo $this->root;?>/Admin/deleteUser",
                        { id: $("#e_id").val()
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