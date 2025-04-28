@if(!empty($image))
    <img src="{{ asset('storage/' . $image) }}" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
@else
    <img src="{{ asset('admin/default.png') }}" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
@endif