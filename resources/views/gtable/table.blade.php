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
                <tr>
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
                                            <a
                                                    href={{""}}> {{$ColumnMiniData[Constants::TABLE_DATA_KEY]}}
                                            </a>
                                        @endif
                                    @endforeach
                                @else
                                        <a
                                                href={{""}}> {{$columnData[Constants::TABLE_DATA_KEY]}}
                                        </a>
                                @endif
                            @endif
                        </td>

                    @endforeach
                </tr>
                @endforeach


                    {{--@foreach ($problem[TABLE_DATA_KEY] as $key => $value)--}}
                        {{--@if($key == Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY)--}}
                            {{--<td class="td-problems">--}}
                                {{--{{ Utilities::generateProblemNumber($problem) }}--}}
                            {{--</td>--}}
                        {{--@elseif($key == "name")--}}
                            {{--<td class="td-problems">--}}
                                {{--<a target="_blank" href="{{Utilities::generateProblemLink($problem)}}">  {{$value}} </a>--}}
                            {{--</td>--}}
                        {{--@elseif(in_array($key, \App\Models\Problem::$displayable))--}}
                            {{--<td class="td-problems">  {{$value}} </td>--}}
                        {{--@endif--}}
                    {{--@endforeach--}}
                    {{--<td class="td-problems">--}}
                        {{--@if(count(explode(',', $problem->tags_ids)) > 0 && strlen(trim(explode(',', $problem->tags_ids)[0]))>0)--}}
                            {{--@foreach(explode(',', $problem->tags_ids) as $tagID)--}}
                                {{--<a href="{{url()->current()}}?tag={{$tagID}}" class="problems-table-tags-links"><span>--}}
                                {{--{{ ((App\Models\Tag::find($tagID))?(App\Models\Tag::find($tagID)->getAttributes()[Constants::FLD_TAGS_NAME]):'')}}--}}
                                {{--</span>--}}
                                {{--</a>--}}
                            {{--@endforeach--}}
                        {{--@else--}}
                            {{--<p class="text-center">-</p>--}}
                        {{--@endif--}}
                    {{--</td>--}}
                {{--</tr>--}}
                {{--@endforeach--}}
        </tbody>
    </table>
    {{--Pagination--}}
    {{--<nav aria-label="Page navigation example">--}}
        {{--<ul class="pagination" max-size='12'>--}}
            {{--<li class="page-item {{isset($data->rows->prev_page_url)? "":"disabled"}}">--}}
                {{--<a class="page-link"--}}
                   {{--href="{{isset($data->rows->prev_page_url) ? Utilities::getURL("page", $data->rows->current_page-1, url()->current(), Request::fullUrl(), false) : ""}}"--}}
                   {{--aria-label="Previous">--}}
                    {{--<span aria-hidden="true">&laquo;</span>--}}
                    {{--<span class="sr-only">Previous</span>--}}
                {{--</a>--}}
            {{--</li>--}}
            {{--@for ($i = $data->initialPage; ($i <= $data->pagesLimit) ; $i++)--}}
                {{--<li class= {{($data->rows->current_page == $i ? "active" : "")}}>--}}
                    {{--<a class="page-link"--}}
                       {{--href={{Utilities::getURL("page", $i, url()->current(), Request::fullUrl(), false)}}>{{$i}}</a>--}}
                {{--</li>--}}
            {{--@endfor--}}
            {{--<li class="page-item {{(isset($data->rows->next_page_url) ? ("") : ("disabled"))}}">--}}
                {{--<a class="page-link"--}}
                   {{--href="{{(isset($data->rows->next_page_url) ? Utilities::getURL("page", $data->rows->current_page + 1, url()->current(), Request::fullUrl(), false) : "")}}"--}}
                   {{--aria-label="Next">--}}
                    {{--<span aria-hidden="true">&raquo;</span>--}}
                    {{--<span class="sr-only">Next</span>--}}
                {{--</a>--}}
            {{--</li>--}}
        {{--</ul>--}}
    {{--</nav>--}}
</div>
