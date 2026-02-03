@if(session($type))
<div class="alert alert-{{ $type }} alert-dismissible fade show">
    {{ session($type) }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif