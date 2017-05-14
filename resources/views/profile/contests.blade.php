<div class="content-tabs card">

  <ul class="nav nav-tabs" role="tablist">

     <li class="nav-item active" role="presentation">
          <a href="#part" role="tab" data-toggle="tab">Participated Contests</a>
     </li>

     <li class="nav-item " role="presentation">
           <a href="#owned" role="tab" data-toggle="tab">Owned Contests</a>
     </li>

     <li class="nav-item " role="presentation">
          <a href="#admin" role="tab" data-toggle="tab">Administrating Contests</a>
     </li>

  </ul>


   <div class="tab-content">

      <!-- patricipated contests tab-->
      <div role="tabpanel" class="fade in tab-pane active" id="part">
          <div class="panel-body problems-panel-body">
           @include('contests.contest_views.contests_table', ['contests' => $participatedContests, 'fragment' => ''])
          </div>
      </div>


      <!-- owned contests tab-->
      <div role="tabpanel" class="fade tab-pane" id="owned">
          @include('contests.contest_views.contests_table', ['contests' => $owningContests, 'fragment' => ''])
      </div>


      <!-- admin in contests tab-->
      <div role="tabpanel" class="fade tab-pane" id="admin">
          @include('contests.contest_views.contests_table', ['contests' => $organizingContests, 'fragment' => ''])
      </div>

   </div>
</div>