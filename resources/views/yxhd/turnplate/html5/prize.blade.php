@extends('yxhd.turnplate.html5.layouts.app')

@section('content')

    <title>{{$prize->name}}</title>

    <!--奖品介绍-->
    <div class="aui-margin-t-5">
        <div class="aui-row aui-text-center" style="width: 90%;margin: auto;">
            <!--奖品介绍-->
            <div class="aui-margin-t-15" style="background: #FF715A;">
                <div class="aui-padded-t-10">
                    <div style="display: inline-block;background: white;width: 80px;height: 26px;line-height: 26px;color: #FF402C;border-radius: 13px;"
                         class="aui-font-size-12">奖品介绍
                    </div>

                    <div style="height: 10px;"></div>
                    <div class="aui-margin-10">
                        {!! $prize->intro_html !!}
                    </div>

                    <div style="height: 30px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 60px;"></div>
    <!--页脚-->
    <footer class="aui-bar aui-bar-tab" id="footer"
            style="border-top: 1px solid #f1f1f1;background: #FF402C !important;">
        <div class="aui-bar-tab-item" tapmode onclick="clickBack();">
            <div class="aui-bar-tab-label aui-font-size-16 aui-text-white">继续抽奖</div>
        </div>
    </footer>

@endsection

@section('script')

    <script type="text/javascript">


    </script>
@endsection