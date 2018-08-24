@extends('layouts.app')

@section('styles')
    <style type="text/css">

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="container-fluid container-group-select">
            <div class="row">
                <group-select
                    isEdit="{{ $isEdit }}"
                    uid="{{ $uid }}"
                    activity_id="{{ $activity_id  }}"
                    fetch_api_url="{{ route('api.activity.getGroupAndMember') }}"
                    save_api_url="{{ route('api.activity.saveGroupSetting') }}"
                    export_api_url="{{ route('api.activity.exportGroup') }}"
                ></group-select>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection