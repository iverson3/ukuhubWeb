<div class="row">
    <div class="col-md-12">

        <div class="box box-default">
            <div class="box-header with-border group-header">

                <div class="box-tools">
                    <div class="btn-group pull-right" style="margin-right: 10px">
                        <a class="btn btn-sm btn-default form-history-back" href="javascript:;" onclick="window.history.back()"><i class="fa fa-arrow-left"></i>&nbsp;返回</a>
                    </div>
                </div>
            </div>

            <!-- form start -->
            <div class="form-horizontal">
                <div class="box-body">

                    <div class="fields-group">

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">活动标题</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $data['activity'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">组长</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $data['leader'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">组员列表</label>
                            <div class="col-sm-8">
                                @foreach($data['members'] as $member)
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $member['name'] }} ({{ $member['wechat'] }})
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">乐器类型</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $data['music_type'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">能力级别</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $data['level'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">备注信息</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $data['remark'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">分组者信息</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $data['user']->name }} ({{ $data['user']->username }})
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">分组时间</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $data['created_at'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">更新时间</label>
                            <div class="col-sm-8">
                                <div class="box box-solid box-default no-margin box-show">
                                    <div class="box-body">
                                        {{ $data['updated_at'] }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .control-label {
        margin-top: 5px;
    }
    .group-header {
        height: 43px;
    }
</style>