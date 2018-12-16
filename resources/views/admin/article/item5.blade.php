<form class="form form-horizontal" id="form-edit">
    {{csrf_field()}}
    <div class="row cl hidden">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>id：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="id" name="id" type="text" class="input-text"
                   value="{{ isset($data->id) ? $data->id : '' }}" placeholder="作品id">
        </div>
    </div>

    <div class="va-m">
        <div style="width: 500px;margin: auto;" class="text-c">
            @foreach($data->twSteps as $twStep)
                {{--如果有图片--}}
                @if($twStep->img)
                    <img src="{{$twStep->img}}" style="width: 500px;" class="aui-padded-10">
                @endif
                <span style="margin-top: 30px;line-height: 22px;">{{$twStep->text}}</span>
            @endforeach
        </div>
    </div>


</form>

@include('vendor.ueditor.assets')

<script type="text/javascript">


    $(function () {

    });

</script>
