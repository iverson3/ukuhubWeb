{{-- @extends('dashboard') --}}
{{-- @section('content') --}}
<div class='row member-info'>
	<div class='col-md-6'>
		<h3>活動人員詳情  <a href="javascript:;" onclick="window.history.back()">返回</a></h3>

   		<p>微信號：{{ $member['wechat'] }}</p>
   		<p>名字：{{ $member['name'] }}</p>
   		<p>樂器類型：{{ $member['music_type'] }}</p>
   		<p>能力分類：{{ $member['level'] }}</p>

   		@if ($member['level'] == '萌新')
   			<p>琴照：</p>
   			<div>
   				<img style="max-width: 500px; max-height: 400px;" src="{{ $member['pic'] }}">
   			</div>
   		@else
   			<p>視頻鏈接： {{ $member['pic'] }}</p>
   		@endif
   		
   		<p>備註信息：{{ $member['remark'] }}</p>
   		<p>狀態：@if ($member['status'] == 1) 可用 @else 禁用 @endif</p>
   		<p>報名時間：{{ $member['created_at'] }}</p>
	</div><!-- /.col -->
</div><!-- /.row -->
{{-- @endsection --}}

<style type="text/css">
	.member-info {
		padding: 25px;
	}
</style>