<div>
    {{--Organsisers AutoComplete--}}
    <div class="search-wrapper">
        <input id="organisersAuto" type="text" class="organisersAuto search-box" placeholder="Mention Organisers"
               autocomplete="off" onkeypress="return event.keyCode != 13;"/>
        <button class="close-icon" type="reset"></button>
    </div>
    <div class="container">
        <ul id="organisersList" class="tags-list">
            {{--Adding Previously Checked $Organisers--}}
            @if( isset($mOrganisers) )
                @foreach( $mOrganisers as $organiser)
                    <li name="organisers[]" value="{{$organiser}}">
                        <button class="organiser-close-icon "></button>{{$organiser}} </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>

{{--<script type="text/javascript">--}}
{{--//Tags AutoComplete parameters--}}
{{--var organisersList = document.getElementById("organisersList");--}}
{{--var organisersPath = "{{route('contest/add/organisersautocomplete')}}";--}}
{{--$('input.organisersAuto').typeahead(autoComplete(organisersPath, organisersList, "organisers[]", 1));--}}
{{--function applyOrganisers() {--}}
{{--var mOrganisers = getListInfo(organisersList);--}}
{{--$.ajax({--}}
{{--url: "{{Request::url()}}/OrganisersSync",--}}
{{--type: 'POST',--}}
{{--data: {--}}
{{--_token: "{{csrf_token()}}",--}}
{{--mOrganisers: mOrganisers,--}}
{{--},--}}
{{--success: function (data) {--}}
{{--}--}}
{{--});--}}
{{--}--}}
{{--//Wait for deletion key--}}
{{--$(document).on('mousedown', '.organiser-close-icon', function (item) {--}}
{{--$(this).parent().remove();--}}
{{--applyOrganisers();--}}
{{--});--}}
{{--</script>--}}

