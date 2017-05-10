{{--Display single contest participants info--}}
<table class="table table-bordered table-hover text-center">
    <thead>
    <tr>
        <th class="text-center" width="10%">ID</th>
        <th class="text-center" width="60%">Name</th>
        <th class="text-center"># of Problems</th>
        @if($isOwnerOrAdmin)
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

            {{--ID--}}
            <td> {{ $sheetID }} </td>

            {{--Name--}}
            <td>
                <a href="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_SHEET_DISPLAY, $sheetID) }}">{{ $sheetName }}</a>
            </td>

            {{--Problems Count--}}
            <td> {{ $sheetProblemsCount }}</td>

            {{--Actions--}}
            @if($isOwnerOrAdmin)
                <td class="text-center">

                    {{--Edit sheet--}}
                    <a href="{{ route(\App\Utilities\Constants::ROUTES_GROUPS_SHEET_EDIT, $sheetID) }}"
                       class="btn btn-link text-dark testing-edit-sheet">
                        Edit
                    </a>

                    {{--Delete sheet--}}
                    @include('components.action_form', ['halfWidth' => true, 'url' => route(\App\Utilities\Constants::ROUTES_GROUPS_SHEET_DELETE, $sheetID) , 'method' => 'DELETE', 'confirm' => true, 'confirmMsg' => "'Are you sure want to delete this sheet? This action cannot be undone!'", 'btnIDs' => "", 'btnClasses' => 'btn btn-link text-dark testing-delete-sheet', 'btnTxt' => 'Delete'])

                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
