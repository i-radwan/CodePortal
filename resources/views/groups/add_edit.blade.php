@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Edit {{isset($group[\App\Utilities\Constants::FLD_GROUPS_NAME]) ? $group[\App\Utilities\Constants::FLD_GROUPS_NAME]:''}}
            </div>

            <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url($formAction) }}">
                    {{ csrf_field() }}
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
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{$btnText}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <span class="page-distinguishing-element" id="add-edit-group-page-hidden-element"></span>

@endsection
