@php($notifications = Auth::user()->userDisplayableReceivedNotifications()->get())
@php($unreadCount = count(Auth::user()->unreadNotifications()->get()))

@if(count($notifications))
    <li class="dropdown notifications-dropdown">

        {{--Notifications bell icon--}}
        <a class="dropdown-toggle" data-toggle="dropdown" role="button"
           aria-expanded="false"
           {{--If there's unread notifications, mark them read once clicked--}}
           @if($unreadCount) onclick="markAllNotificationsRead('{{csrf_token()}}', '{{url('notifications/mark_all_read')}}');" @endif >
            <i id="notifications-icon"
               class="notifications-icon fa fa-bell{{($unreadCount)?' dark-red':'-o'}}"
               aria-hidden="true"></i>

            {{--Text option for small devices--}}
            <span class="notifications-text">Notifications</span>
        </a>

        {{--Notifications dropdown menu--}}
        <ul class="dropdown-menu notifications" role="menu">
            @foreach($notifications as $notification)
                @php
                    // Get resource model from notification
                    // Generate resource link, which the user gets to when clicking the notification
                    if($notification->type == Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_CONTEST]){
                        $resource = \App\Models\Contest::find($notification->resource_id);
                        $resourceLink = 'contest/'.$resource->id;
                    } elseif ($notification->type == Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_GROUP]){
                        $resource = \App\Models\Group::find($notification->resource_id);
                        $resourceLink = 'group/'.$resource->id;
                    }else if ($notification->type == Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_TEAM]){
                       //Add later
                    }
                @endphp
                <li class="notification-container {{($notification->status == \App\Utilities\Constants::NOTIFICATION_STATUS[\App\Utilities\Constants::NOTIFICATION_STATUS_UNREAD])?'unread':'read'}}">

                    <a href="{{url($resourceLink)}}">

                        {{--Notification icon--}}
                        <div class="notification-icon">
                            @if($notification->type == Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_CONTEST])
                                <i class="fa fa-flag-checkered" aria-hidden="true"></i>
                            @elseif($notification->type == Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_GROUP])
                                <i class="fa fa-users" aria-hidden="true"></i>
                            @endif
                        </div>

                        {{--Notification text and time--}}
                        <div class="notification-text">
                            <span>{{\App\Utilities\Constants::NOTIFICATION_TEXT[$notification->type]}}
                                <em class="notification-resource-name">{{$resource->name}}</em>
                            </span>
                            <p class="text-right small notification-time">{{\App\Utilities\Utilities::formatPastDateTime($notification->created_at)}}</p>
                        </div>
                    </a>

                    {{--Notification cancel icon--}}
                    <i class="fa fa-times notification-delete" aria-hidden="true"
                       onclick="cancelNotification(event, '{{csrf_token()}}', '{{url("notification/".$notification->id)}}', this);"></i>

                </li>
                @if(!$loop->last)
                    <li role="separator" class="divider">
                @endif
            @endforeach
        </ul>
    </li>
@endif