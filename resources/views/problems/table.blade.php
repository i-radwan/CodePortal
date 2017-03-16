<!DOCTYPE html>
<html>
<head>
    <script> src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js" </script>
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script>
        $(document).ready(function(){
            $(".search-wrapper input").mouseenter(function(){
                $(".search-wrapper button").css("background-color", "#4aba10");


            });
            $(".search-wrapper input").mouseout(function(){
                $(".search-wrapper button").css("background-color", "" );
            });
        });
        // to be separated later
        function myFunction() {
            // Declare variables
            var input, filter, table, tr, td, i;
            input = document.getElementById("problem_search_box");
            filter = input.value.toUpperCase(); //filter for the input
            table = document.getElementById("problems_table"); //the id of the table
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (i = 0; i < tr.length; i++) {
                tableRowData = tr[i].getElementsByTagName("td")[1];  //get all the data of the second column
                if (tableRowData) { //if data is found
                    if (tableRowData.innerHTML.toUpperCase().indexOf(filter) > -1) { //(TODO YA SAMRA)
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</head>
<body>
<div class = "container table-container">
    <div>
            <span>
               <form>
                  <input class="filterTable-input" data-type="search" onkeyup="myFunction()" placeholder="Search in this page by name..." id = "problem_search_box">
               </form>
            </span>
    </div>
    <br>
    <table class = "table table-bordered  table-hover " id = "problems_table">
        <thead>
        <!-- We are gonna loop here for the head tags -->
        @foreach ($data->headings as $heading)
            <th class = 'table-head'> {{$heading}}</th>
        @endforeach
        </thead>
        <tbody>
        <!-- Iam here using JSON without decoding -->
        <!-- we are going to display the fetched problems -->
        @foreach ( $data->problems->data as $problem)
            <!-- We may here get the colour from a specific constant table -->
            @if(isset($problem->verdict ))
                @if($problem->verdict == "Accepted")
                    <tr  class = "success"  >
                @elseif($problem->verdict ==  "TLE")
                    <tr class = "warning"  >
                @elseif($problem->verdict == "Run-Time-Error")
                    <tr class = "tr-runtimeerror" >
                @elseif($problem->verdict == "CompilationError")
                    <tr class = "info" >
                @elseif($problem->verdict == "WrongAnswer")
                    <tr class = "success" >
                @else
                    <tr class = "tr-notsolved" >
                @endif
            @else
                <tr >
                    @endif
                    @foreach ($problem as $key => $value)
                        @if($key == "name")
                            <td class = "td-problems" ><a href = "http://codeforces.com/problemset/problem/782/B">  {{$value}} </a>
                            </td>
                        @elseif( $key ==  "tags")
                            <?php $tags = explode(',', $value); ?>
                            <td>
                                @foreach($tags as $tag)
                                    <span class="td-problems-badge"> {{$tag}} </span>
                                @endforeach
                            </td>
                        @else
                            <td class = "td-problems" >  {{$value}} </td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
        </tbody>

    </table>
</div>
</body>
</html>