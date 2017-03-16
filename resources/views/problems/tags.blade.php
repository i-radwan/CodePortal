<!DOCTYPE html>
<html>
<head></head>
<body>
<div class="container">
            @if(isset($data->tags))
                @foreach ( $data->tags as $tag)
                   {{--TODOSAMRA: --}}{{--get the query and add then add the tag--}}
                   <a href="/problems/?tag={{$tag->id}}"> <span class="badge .td-problems-badge"> {{$tag->name}}</span></a>
                @endforeach
            @endif

</div>
</body>
</html>