@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                Edit {{ isset($group) ? $group[\App\Utilities\Constants::FLD_GROUPS_NAME]:'' }}
            </div>

            <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url($formAction) }}">

                    {{--Hidden fields--}}
                    {{ csrf_field() }}
                    <input type="hidden" id="admins-ids-hidden" name="admins"/>

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} has-feedback">
                        <label for="name" class="col-md-4 control-label">Name</label>

                        {{--Name--}}
                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" name="name"
                                   value="{{ (isset($group->name))?$group->name:old('name') }}"
                                   placeholder="Name" required autofocus>

                            {{--Errors--}}
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{--Admins--}}
                    <div class="form-group">
                        <label for="admins" class="col-md-4 control-label">Admins</label>

                        <div class="col-md-6">

                            {{--Admins Auto Complete--}}
                            @include('components.auto_complete', ['itemsType' => 'admins', 'itemName' => 'Admin', 'itemsLink' => route(\App\Utilities\Constants::ROUTES_GROUPS_ADMINS_AUTO_COMPLETE), 'hiddenID' => '', 'hiddenName' => ''])
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"
                                    onclick="app.moveGroupSessionDataToHiddenFields()">
                                {{$btnText}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <span class="page-distinguishing-element" id="add-edit-group-page-hidden-element"
          @if(isset($group))
          data-admins="{{$group->admins()->pluck(\App\Utilities\Constants::FLD_USERS_USERNAME)}}"
            @endif></span>

@endsection
