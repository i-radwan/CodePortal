<div class="content-tabs card">

  <ul class="nav nav-tabs" role="tablist">

     <li class="nav-item active" role="presentation">
          <a href="#joined" role="tab" data-toggle="tab">Joined Groups</a>
     </li>

     <li class="nav-item " role="presentation">
           <a href="#owned" role="tab" data-toggle="tab">Owning Groups</a>
     </li>

     <li class="nav-item " role="presentation">
          <a href="#admin" role="tab" data-toggle="tab">Administrating Groups</a>
     </li>

  </ul>


   <div class="tab-content">

       {{--Joined Groups--}}
      <div role="tabpanel" class="fade in tab-pane active" id="joined">
          <div class="panel-body problems-panel-body">
           @include('groups.groups_table', ['groups' => $joiningGroups])
          </div>
      </div>


       {{--Owned Groups--}}
      <div role="tabpanel" class="fade tab-pane" id="owned">
          @include('groups.groups_table', ['groups' => $owningGroups])
      </div>


       {{--Administrating Groups--}}
       <div role="tabpanel" class="fade tab-pane" id="admin">
          @include('groups.groups_table', ['groups' => $administratingGroups])
      </div>

   </div>
</div>