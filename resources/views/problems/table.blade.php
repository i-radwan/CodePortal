<!DOCTYPE html>
<html>
<head>
    <script> src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js" </script>
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <?php
    // This function adds a new query to the saved ones and overwrites if needed
    function getURl($key, $value,$defaultURL){
        $url_parts = parse_url(Request::fullUrl());
        if(isset($url_parts['query'])){
            parse_str($url_parts['query'], $params);
            if( $key == 'sortby')
                $params['page'] = 1;
            $params[$key] = $value; //overwriting if page parameter exists
            $url_parts['query'] = http_build_query($params);
//            I commented cause port gives an error (I am working with valet)
//            $url =  $url_parts['scheme'] . '://' . $url_parts['host'] . ':' . $url_parts['port'] . $url_parts['path'] . '?' . $url_parts['query'];
            $url =  $url_parts['scheme'] . '://' . $url_parts['host'] . ':' . $url_parts['path'] . '?' . $url_parts['query'];
        }
        else{
            $url = $defaultURL."?".$key. "=". $value;
        }
        return $url;
    }
    ?>
</head>
<body>
<div class = "container problems-table-container">
    <table   class = "table table-bordered  problems-table-hover " id = "problems_table">
        <thead>
        <tr>
            <!-- We are gonna loop here for the head tags -->
            @foreach ($data->headings as $heading)
                <th class = <?php echo($heading == 'Name' ? 'problems-table-name-head': 'problems-table-head'); ?> > <a class="problems-table-head-link" href=<?php echo(getURl("sortby",$heading,"/problems")) ?> >{{$heading}} </a>
                    @if( old('sortby') != null && old('sortby') == $heading)
                        <i class="fa fa-sort-desc" aria-hidden="true"></i>
                        <?php old('sortby', null); ?>
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
                @if($problem->verdict == \App\Utilities\Constants::SUBMISSION_VERDICT["OK"])
                    <tr  class = "success"  >
                @elseif($problem->verdict !=  \App\Utilities\Constants::SUBMISSION_VERDICT["OK"])
                    <tr class = "warning"  >
                @else
                    <tr class = "tr-notsolved" >
                @endif
            @else
                <tr >
                    @endif
                    @foreach ($problem as $key => $value)
                        @if($key == "name")
                            <td class = "td-problems"  ><a href = "">  {{$value}} </a>
                            </td>
                        @elseif( $key ==  "tags")
                            <?php $tags = explode(',', $value); ?>
                            <td>
                                @foreach($tags as $tag)
                                    <span class="td-problems-badge"> {{$tag}} </span>
                                @endforeach
                            </td>
                        @elseif($key != "verdict")
                            <td class = "td-problems" >  {{$value}} </td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
        </tbody>
    </table>
    {{--Pagination--}}
<!--    --><?php //dd($data); ?>
    <nav aria-label="Page navigation example">
        <ul class="pagination" max-size='12'>
            <li class="page-item <?php echo((isset($data->problems->prev_page_url)  ? (""): ("disabled"))); ?>">
                <a class="page-link" href="<?php echo(isset($data->problems->prev_page_url) ? getURl("page",$data->problems->current_page-1,"/problems") : ""); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
             <?php $j = $data->problems->current_page;
             if( $j < 7){
            $forcedLimit = 12;
            $forcedLimit = ($forcedLimit > $data->problems->last_page) ? $data->problems->last_page:$forcedLimit;
                 $i = 1;
            }
            else{
                 $i = $j-6;
            $forcedLimit = $j+6;
            $forcedLimit = ($forcedLimit > $data->problems->last_page) ? $data->problems->last_page:$forcedLimit;
            }
             ?>
            @for (; ($i <= $forcedLimit) ; $i++)
                <li  class = <?php echo($data->problems->current_page == $i ? "active" : "");?>><a class="page-link" href= <?php echo(getURl("page", $i, "/problems")); ?>>{{$i}}</a></li>
            @endfor
            <li class="page-item <?php echo((isset($data->problems->next_page_url)  ? (""): ("disabled"))); ?>">
                <a class="page-link" href="<?php echo(isset($data->problems->next_page_url) ? getURl("page",$data->problems->current_page+1,"/problems") : ""); ?>"  aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
</body>
</html>