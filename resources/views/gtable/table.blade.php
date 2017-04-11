<div class="container problems-table-container">
    <table class="table table-bordered  problems-table-hover problems-talbe" id="problems_table">
        <thead>
        <tr>
            <!-- We are gonna loop here for the head tags -->
            @foreach ($data[Constants::TABLE_HEADINGS_KEY] as $heading) {{-- Change class of th if it's a tags heading--}}
                <th class={{(($heading == Constants::PROBLEMS_TABLE_HEADINGS[4][Constants::TABLE_DATA_KEY]) ? "gtable-tags-head":"gtable-head")}}>
                    {{-- Check if it's a tags heading or not to add the sortby option to that heading--}}
                    @if($heading != Constants::PROBLEMS_TABLE_HEADINGS[4][Constants::TABLE_DATA_KEY])
                        <a class="problems-table-head-link" {{-- ToDO: getURL to be modified later --}}
                           href=""> {{$heading[Constants::TABLE_DATA_KEY]}}
                        </a>
                    @else
                        <span class="table-head">{{$heading[Constants::TABLE_DATA_KEY]}}</span>
                    @endif
                    {{-- ToDo: to be changed later --}}
                    {{--@if( $data->sortbyParam != null && $data->sortbyParam == $heading)--}}
                        {{--<i class="fa--}}
                            {{--{{($data->sortbyMode == 'desc') ?--}}
                             {{--'fa-sort-desc problems-table-sorting-arrow-desc' :--}}
                             {{--'fa-sort-asc problems-table-sorting-arrow-asc'}}--}}
                                {{--pull-right" aria-hidden="true">--}}
                        {{--</i>--}}
                    {{--@endif--}}
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <?php
        //TODO (Samir) Adding CheckBoxes to be used in Adding New Contest
        //ToDO Removing any php code here from the view if applicable
        //ToDo (Samir) ReCheck for any field specific for the problem page
        // array key exists TABLE_CELL_LINK
//        dd($data);
        ?>
        <!-- we are going to display the fetched rows -->
        @foreach ( $data[Constants::TABLE_ROWS_KEY] as $row)
            <!-- We may here get the colour from a specific constant table -->
            @if($row[Constants::TABLE_META_DATA_KEY][Constants::TABLE_ROW_STATE_KEY] == Constants::TABLE_ROW_STATE_SUCCESS)
                <tr class="bg-success">
            @elseif ($row[Constants::TABLE_META_DATA_KEY][Constants::TABLE_ROW_STATE_KEY] == Constants::TABLE_ROW_STATE_DANGER)
                <tr class="bg-warning">
            @else
                <tr >
            @endif
                    @foreach($row[Constants::TABLE_DATA_KEY] as $columnData)
                        <td>
                            @if(array_key_exists(Constants::TABLE_LINK_KEY, $columnData ))
                                <a
                                        href= {{$columnData[Constants::TABLE_LINK_KEY]}}> {{$columnData[Constants::TABLE_DATA_KEY]}}
                                </a>
                            @elseif(array_key_exists(Constants::TABLE_EXTERNAL_LINK_KEY, $columnData))
                                <a
                                        href={{$columnData[Constants::TABLE_EXTERNAL_LINK_KEY]}}> {{$columnData[Constants::TABLE_DATA_KEY]}}
                                </a>
                            @else
                                @if (is_array($columnData[Constants::TABLE_DATA_KEY]))
                                    @foreach($columnData[Constants::TABLE_DATA_KEY] as $ColumnMiniData)
                                        @if(array_key_exists(Constants::TABLE_LINK_KEY, $ColumnMiniData ))
                                            <a
                                                    href= {{$ColumnMiniData[Constants::TABLE_LINK_KEY]}}> {{$ColumnMiniData[Constants::TABLE_DATA_KEY]}}
                                            </a>
                                        @elseif(array_key_exists(Constants::TABLE_EXTERNAL_LINK_KEY, $ColumnMiniData))
                                            <a
                                                    href={{$ColumnMiniData[Constants::TABLE_EXTERNAL_LINK_KEY]}}> {{$ColumnMiniData[Constants::TABLE_DATA_KEY]}}
                                            </a>
                                        @else
                                            {{$ColumnMiniData[Constants::TABLE_DATA_KEY]}}
                                        @endif
                                    @endforeach
                                @else
                                    {{$columnData[Constants::TABLE_DATA_KEY]}}
                                @endif
                            @endif
                        </td>

                    @endforeach
                </tr>
                @endforeach
        </tbody>
    </table>
    {{--Pagination--}}
    <nav aria-label="Page navigation example">
        <ul class="pagination" max-size='12'>
            <li class="page-item {{isset($data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_PREV_URL])? "":"disabled"}}">
                <a class="page-link"
                   href="{{isset($data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_PREV_URL]) ? $data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_PREV_URL] : ""}}"
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            @for ($i = $data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_START_LIMIT]; ($i <= $data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_END_LIMIT]) ; $i++)
                <li class= {{($data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_CURRENT_PAGE] == $i ? "active" : "")}}>
                    <a class="page-link"
                       href={{Utilities::getURL("page", $i, url()->current(), Request::fullUrl(), false)}}>{{$i}}</a>
                </li>
            @endfor
            <li class="page-item {{(isset($data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_NEXT_URL]) ? ("") : ("disabled"))}}">
                <a class="page-link"
                   href="{{(isset($data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_NEXT_URL]) ? $data[Constants::TABLE_PAGINATION_KEY][Constants::PAGINATOR_NEXT_URL] : "")}}"
                   aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
