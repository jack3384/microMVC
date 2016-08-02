<div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">添加用户</h3>
                </div>
                <div class="panel-body">
                    <form role="form" method="post">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="手机号码（必填）" name="mobile" pattern="^1\d{10}$"
                                       title="填写11位手机号码" required="required">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="姓名（必填）" name="name" type="text" required="required">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="电子邮件(可不填)" name="email" type="email">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="微信号(可不填)" name="weixinid" type="text">
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <input type="submit" class="btn btn-lg btn-success btn-block" value="添加">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
