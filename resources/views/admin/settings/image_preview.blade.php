@if(!empty($model->image))
    <img src="{{ $image_path.'/'.$model->image }}" style="width:60%; height:30%" alt="Avatar" class="img-avatar zoomable">
@else
    -
@endif
