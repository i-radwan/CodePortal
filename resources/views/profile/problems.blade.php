<div class="content-tabs card">
    {{--Problems tabs--}}
    <ul class="nav nav-tabs" role="tablist">
        <li {{ ($type == "solved") ? 'class=active' : '' }}>
            <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_PROBLEMS_SOLVED, $username) }}">
                Solved problems
            </a>
        </li>
        <li {{ ($type == "unsolved") ? 'class=active' : '' }}>
            <a href="{{ route(\App\Utilities\Constants::ROUTES_PROFILE_PROBLEMS_UNSOLVED, $username) }}">
                Un-Solved problems
            </a>
        </li>
    </ul>

    {{--Problems tab content--}}
    <div class="tab-content">

        {{--Problems--}}
        <div role="tabpanel" class="fade in tab-pane active" id="part">
            <div class="panel-body problems-panel-body">
                @include("problems.table")
            </div>
        </div>
    </div>
</div>
