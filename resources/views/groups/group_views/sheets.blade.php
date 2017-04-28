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
        @php
            $sheetID = $sheet[\App\Utilities\Constants::FLD_SHEETS_ID];
            $sheetProblemsCount = $sheet->problems()->count();
            $sheetName = $sheet[\App\Utilities\Constants::FLD_SHEETS_NAME];
        @endphp
        <tr>
            <td> {{ $sheetID }} </td>
            <td>
                <a href="{{url('sheet/' . $sheetID)}}">{{ $sheetName }}</a>
            </td>
            <td> {{ $sheetProblemsCount }}</td>
            @if($isOwner)
                <td class="text-center">

                    {{--Edit sheet--}}
                    <a href="{{url('sheet/edit/' . $sheetID)}}}"
                       class="btn btn-link text-dark testing-edit-sheet">
                        Edit
                    </a>

                    {{--Delete sheet--}}
                    @include('components.action_form', ['halfWidth' => true, 'url' => url('sheet/' . $sheetID), 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this sheet? This action cannot be undone!'", 'btnIDs' => "", 'btnClasses' => 'btn btn-link text-dark testing-delete-sheet', 'btnTxt' => 'Delete'])

                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
