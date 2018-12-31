@extends('yxhd.turnplate.html5.layouts.app')

@section('content')
    <style type="text/css">
        html, body {
            background: white !important;
        }
    </style>

    <div style="padding-top: 60px;">
        <img src="{{URL::asset('/img/nodate_tip.png')}}" style="width: 60%;margin: auto;">
    </div>

    <div class="aui-text-center aui-padded-t-15 aui-padded-l-15 aui-padded-r-15">
        <span class="text-grey-999 aui-font-size-14" style="line-height: 22px;">系统出现故障，扫码联系组委会</span>
    </div>
    <div class="aui-text-center aui-padded-5 aui-padded-l-15 aui-padded-r-15">
        <span class="text-grey-999 aui-font-size-14" style="line-height: 22px;">{{$msg}}</span>
    </div>

    <div style="padding-top: 0px;">
        <img src="{{URL::asset('/img/isart_fwh.jpg')}}" style="width: 40%;margin: auto;">
    </div>


@endsection

@section('script')

    <script type="text/javascript">

    </script>
@endsection