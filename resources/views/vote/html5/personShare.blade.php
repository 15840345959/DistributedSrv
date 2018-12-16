@extends('vote.html5.layouts.app')

@section('content')

    <style type="text/css">


        html, body {
            background: white;
        }

    </style>

    <div>

    </div>

@endsection

@section('script')


    <script type="text/javascript">

        var vote_user_id = getQueryString("vote_user_id");
        console.log("vote_user_id:" + vote_user_id);

        window.location.href = "http://defwh.isart.me/vote/person?vote_user_id=" + vote_user_id;

    </script>
@endsection