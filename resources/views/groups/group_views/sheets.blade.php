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
            <td> {{ $sheet->problems()->count() }}</td>
            @if($isOwner)
                <td class="text-center">

                    {{--Edit sheet--}}
                    <a href="{{url('sheet/edit/'.$sheet[Constants::FLD_SHEETS_ID])}}}"
                       class="btn btn-link text-dark testing-edit-sheet">
                        Edit
                    </a>

                    {{--Delete sheet--}}
                    @include('components.action_form', ['halfWidth' => true, 'url' => url('sheet/' . $sheet[Constants::FLD_SHEETS_ID]), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this sheet? This action cannot be undone!'", 'btnIDs' => "", 'btnClasses' => 'btn btn-link text-dark testing-delete-sheet', 'btnTxt' => 'Delete'])

                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
