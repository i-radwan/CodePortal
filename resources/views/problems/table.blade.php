<div class="container problems-table-container">
    <table class="table table-bordered  problems-table-hover problems-talbe" id="problems_table">
        <thead>
        <tr>
            <!-- We are gonna loop here for the head tags -->
            @foreach ($data->headings as $heading)
                <th class={{($heading == 'Name')?"problems-table-name-head" : (($heading == 'Tags')?"problems-table-tags-head":"problems-table-head")}}>
                    @if($heading != "Tags")
                        <a class="problems-table-head-link"
                           href="{{(Utilities::getURL("sortby", $heading, url()->current(), Request::fullUrl()))}}&order={{($data->sortbyMode == 'desc')?'asc':'desc'}}">{{$heading}}
                        </a>
                    @else
                        <span class="table-head">{{$heading}}</span>
                    @endif
                    @if( $data->sortbyParam != null && $data->sortbyParam == $heading)
                        <i class="fa
                            {{($data->sortbyMode == 'desc') ?
                             'fa-sort-desc problems-table-sorting-arrow-desc' :
                             'fa-sort-asc problems-table-sorting-arrow-asc'}}
                                pull-right" aria-hidden="true">
                        </i>
                    @endif
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <?php
        //TODO (Samir) Adding CheckBoxes to be used in Adding New Contest
        //ToDO Removing any php code here from the view if applicable
        //ToDo (Samir) ReCheck for any field specific for the problem page
        ?>
        <!-- we are going to display the fetched problems -->
        @foreach ( $data->problems->data as $problem)
            <!-- We may here get the colour from a specific constant table -->
            @if(isset($problem->verdict) && count(explode(',', $problem->verdict ))>0)
                @if(in_array(Constants::CODEFORCES_SUBMISSION_VERDICTS["OK"], explode(',', $problem->verdict)))
                    <tr class="bg-success">
                @else($problem->verdict != Constants::CODEFORCES_SUBMISSION_VERDICTS["OK"])
                    <tr class="bg-warning">
                @endif
            @else
                <tr>
                    @endif
                    @foreach ($problem as $key => $value)
                        @if($key == Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY)
                            <td class="td-problems">
                                {{ Utilities::generateProblemNumber($problem) }}
                            </td>
                        @elseif($key == "name")
                            <td class="td-problems">
                                <a target="_blank" href="{{Utilities::generateProblemLink($problem)}}">  {{$value}} </a>
                            </td>
                        @elseif(in_array($key, \App\Models\Problem::$displayable))
                            <td class="td-problems">  {{$value}} </td>
                        @endif
                    @endforeach
                    <td class="td-problems">
                        @if(count(explode(',', $problem->tags_ids)) > 0 && strlen(trim(explode(',', $problem->tags_ids)[0]))>0)
                            @foreach(explode(',', $problem->tags_ids) as $tagID)
                                <a href="{{url()->current()}}?tag={{$tagID}}" class="problems-table-tags-links"><span>
                                {{ ((App\Models\Tag::find($tagID))?(App\Models\Tag::find($tagID)->getAttributes()[Constants::FLD_TAGS_NAME]):'')}}
                                </span>
                                </a>
                            @endforeach
                        @else
                            <p class="text-center">-</p>
                        @endif
                    </td>
                </tr>
                @endforeach
        </tbody>
    </table>
    {{--Pagination--}}
    <nav aria-label="Page navigation example">
        <ul class="pagination" max-size='12'>
            <li class="page-item {{isset($data->problems->prev_page_url)? "":"disabled"}}">
                <a class="page-link"
                   href="{{isset($data->problems->prev_page_url) ? Utilities::getURL("page", $data->problems->current_page-1, url()->current(), Request::fullUrl(), false) : ""}}"
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            @for ($i = $data->initialPage; ($i <= $data->pagesLimit) ; $i++)
                <li class= {{($data->problems->current_page == $i ? "active" : "")}}>
                    <a class="page-link"
                       href={{Utilities::getURL("page", $i, url()->current(), Request::fullUrl(), false)}}>{{$i}}</a>
                </li>
            @endfor
            <li class="page-item {{(isset($data->problems->next_page_url) ? ("") : ("disabled"))}}">
                <a class="page-link"
                   href="{{(isset($data->problems->next_page_url) ? Utilities::getURL("page", $data->problems->current_page + 1, url()->current(), Request::fullUrl(), false) : "")}}"
                   aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
