@extends('admin.layouts.app')

@section('content')

    <div class="page-container">
        <div style="text-align: center;">
            {!! QrCode::size(300)->generate(env('SYATC_CN_URL') . 'vote/index?activity_id=' . $id) !!}
        </div>

        {{--<p>{{env('APP_NAME')}}</p>--}}
        <p>{{env('SYATC_CN_URL')  . 'vote/index?activity_id=' . $id}}</p>
    </div>

@endsection

@section('script')
    <script type="text/javascript">


    </script>
@endsection