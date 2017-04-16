{{--Display single contest participants info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center" width="10%">ID</th>
        <th class="text-center" width="60%">Name</th>
        <th class="text-center"># of Problems</th>
        @if($isOwner)
            <th class="text-center">Actions</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($sheets as $sheet)
        <tr>
            <td> {{ $sheet[Constants::FLD_SHEETS_ID] }} </td>
            <td>
                <a href="{{url('sheet/'.$sheet[Constants::FLD_SHEETS_ID])}}">{{ $sheet[Constants::FLD_SHEETS_NAME] }}</a>
            </td>
            <td> {{ count($sheet->problems()->get())}}</td>
            @if($isOwner)
                <td class="text-center">
                    <a href="{{url('sheet/edit/'.$sheet[Constants::FLD_SHEETS_ID])}}}" class="btn btn-link text-dark">
                        Edit
                    </a>
                    <form action="{{url('sheet/'.$sheet[Constants::FLD_SHEETS_ID])}}"
                          method="post" class="action">
                        {{method_field('DELETE')}}
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-link text-dark"
                                onclick="return confirm('Are you sure want to delete the sheet?\nThis cannot be undone')">
                            Delete
                        </button>
                    </form>
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
