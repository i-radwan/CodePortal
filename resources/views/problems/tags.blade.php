<!DOCTYPE html>
<html>
<head></head>
<body>
<div class="container tags-table-container">
    @if(isset($data->tags))
        @foreach ( $data->tags as $tag)
            {{--TODOSAMRA: --}}{{--get the query then add the tags--}}
            <a href="/problems/?tag={{$tag->id}}"> <span class="badge .td-problems-badge"> {{$tag->name}}</span></a>
        @endforeach
    @endif
</div>
</body>
</html>