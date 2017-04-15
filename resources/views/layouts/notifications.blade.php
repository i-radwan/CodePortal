@php($notifications = Auth::user()->userDisplayableReceivedNotifications()->get())
@php($unreadCount = count(Auth::user()->unreadNotifications()->get()))

@if(count($notifications))
    <li class="dropdown notifications-dropdown">
        {{--Notifications bell icon--}}
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
           aria-expanded="false"
           @if($unreadCount) onclick="markAllNotificationsRead('{{csrf_token()}}');" @endif >
            <i id="notifications-icon"
               class="notifications-icon fa fa-bell{{($unreadCount)?' dark-red':'-o'}}"
               aria-hidden="true"></i>
            <span class="notifications-text">Notifications</span>
        </a>

        {{--Notifications dropdown menu--}}
        {{--ToDo : To be generalized for groups and teams not only contests --}}
        <ul class="dropdown-menu notifications" role="menu">
            @foreach($notifications as $notification)
                @php($contest = \App\Models\Contest::find($notification->resource_id))
                <li class="notification-container {{($notification->status == \App\Utilities\Constants::NOTIFICATION_STATUS[\App\Utilities\Constants::NOTIFICATION_STATUS_UNREAD])?'unread':'read'}}">

                    <a href="{{url('contest/'.$contest->id)}}">
                        <div class="notification-icon">
                            <i class="fa fa-flag-checkered" aria-hidden="true"></i>
                        </div>
                        <div class="notification-text">
                            <span>{{\App\Utilities\Constants::NOTIFICATION_TEXT[$notification->type]}}
                                <em class="notification-resource-name">{{$contest->name}}</em>
                            </span>
                            <p class="text-right small notification-time">{{\App\Utilities\Utilities::formatPastDateTime($notification->created_at)}}</p>
                        </div>
                    </a>
                    <i class="fa fa-times notification-delete" aria-hidden="true"
                       onclick="cancelNotification(event, '{{csrf_token()}}', '{{$notification->id}}', this);"></i>
                </li>
                @if(!$loop->last)
                    <li role="separator" class="divider">
                @endif
            @endforeach
        </ul>
    </li>
@endif