<div class="container problems-table-container">
    <table class="table table-bordered  problems-table-hover " id="problems_table">
        <thead>
        <tr>
            <!-- We are gonna loop here for the head tags -->
            @foreach ($data->headings as $heading)
                <th class={{($heading == 'Name')?"problems-table-name-head" : "problems-table-head"}}>
                    <a class="problems-table-head-link"
                       href="{{(Utilities::getURL("sortby", $heading, "/problems", Request::fullUrl()))}}&order={{($data->sortbyMode == 'desc')?'asc':'desc'}}">{{$heading}}
                    </a>
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
        <!-- we are going to display the fetched problems -->
        @foreach ( $data->problems->data as $problem)
            <!-- We may here get the colour from a specific constant table -->
            @if(isset($problem->verdict ))
                @if($problem->verdict == Constants::SUBMISSION_VERDICT["OK"])
                    <tr class="light-warning">
                @elseif($problem->verdict != Constants::SUBMISSION_VERDICT["OK"])
                    <tr class="light-danger">
                @else
                    <tr class="tr-notsolved">
                @endif
            @else
                <tr>
                    @endif
                    @foreach ($problem as $key => $value)
                        @if($key == "name")
                            <td class="td-problems"><a href="">  {{$value}} </a>
                            </td>
                        @elseif( $key ==  "tags")
                            <?php $tags = explode(',', $value); ?>
                            <td>
                                @foreach($tags as $tag)
                                    <span class="td-problems-badge"> {{$tag}} </span>
                                @endforeach
                            </td>
                        @elseif($key != "verdict")
                            <td class="td-problems">  {{$value}} </td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
        </tbody>
    </table>
    {{--Pagination--}}
    <nav aria-label="Page navigation example">
        <ul class="pagination" max-size='12'>
            <li class="page-item {{isset($data->problems->prev_page_url)? "":"disabled"}}">
                <a class="page-link"
                   href="{{isset($data->problems->prev_page_url) ? Utilities::getURL("page", $data->problems->current_page-1, "/problems", Request::fullUrl(), false) : ""}}"
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            @for ($i = $data->initialPage; ($i <= $data->pagesLimit) ; $i++)
                <li class= {{($data->problems->current_page == $i ? "active" : "")}}>
                    <a class="page-link"
                       href={{Utilities::getURL("page", $i, "/problems", Request::fullUrl(), false)}}>{{$i}}</a>
                </li>
            @endfor
            <li class="page-item {{(isset($data->problems->next_page_url) ? ("") : ("disabled"))}}">
                <a class="page-link"
                   href="{{(isset($data->problems->next_page_url) ? Utilities::getURL("page", $data->problems->current_page + 1, "/problems", Request::fullUrl(), false) : "")}}"
                   aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
