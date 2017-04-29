@php
    $user = Auth::user();
    $notifications = $user->displayableNotifications()->get();
    $unreadCount = $user->unreadNotifications()->count();
@endphp

@if(count($notifications))
    <li class="dropdown notifications-dropdown">

        {{--Notifications bell icon--}}
        <a class="dropdown-toggle" id="testing-notification-link" data-toggle="dropdown" role="button"
           aria-expanded="false"
           {{--If there's unread notifications, mark them read once clicked--}}
           @if($unreadCount) onclick="app.markAllNotificationsRead('{{ csrf_token() }}', '{{ route(\App\Utilities\Constants::ROUTES_NOTIFICATIONS_MARK_ALL_READ) }}');" @endif >
            <i id="notifications-icon"
               class="notifications-icon fa fa-bell{{($unreadCount)?' dark-red':'-o'}}"
               aria-hidden="true">
            </i>

            {{--Text option for small devices--}}
            <span class="notifications-text">Notifications</span>
        </a>

        {{--Notifications dropdown menu--}}
        <ul class="dropdown-menu notifications" role="menu">
            @foreach($notifications as $notification)
                @php
                    switch ($notification->type) {
                        // Get resource model from notification
                        // Generate resource link, which the user gets to when clicking the notification
                        case \App\Utilities\Constants::NOTIFICATION_TYPE_CONTEST:
                            $resource = \App\Models\Contest::find($notification->resource_id);
                            $resourceLink = 'contest/' . $resource->id;
                            $icon = 'fa-flag-checkered';
                            break;
                        case \App\Utilities\Constants::NOTIFICATION_TYPE_GROUP:
                            $resource = \App\Models\Group::find($notification->resource_id);
                            $resourceLink = 'groups/' . $resource->id;
                            $icon = 'fa-users';
                            break;
                        case \App\Utilities\Constants::NOTIFICATION_TYPE_TEAM:
                            $resource = \App\Models\Team::find($notification->resource_id);
                            $resourceLink = route(\App\Utilities\Constants::ROUTES_PROFILE_TEAMS, $notification->sender_id);
                            $icon = 'fa-users'; //TODO: get icon
                            break;
                    }

                    $isRead = ($notification->status == \App\Utilities\Constants::NOTIFICATION_STATUS_READ);
                    $message = \App\Utilities\Constants::NOTIFICATION_TEXT_MESSAGE[$notification->type];
                    $date = \App\Utilities\Utilities::formatPastDateTime($notification->created_at);
                @endphp

                <li class="notification-container {{ $isRead ? 'read' : 'unread' }}">
                    <a href="{{ url($resourceLink) }}">
                        {{--Notification icon--}}
                        <div class="notification-icon">
                            <i class="fa {{ $icon }}" aria-hidden="true"></i>
                        </div>

                        {{--Notification text and time--}}
                        <div class="notification-text">
                            <span>{{ $message }}
                                <em class="notification-resource-name">{{ $resource->name }}</em>
                            </span>
                            <p class="text-right small notification-time">{{ $date }}</p>
                        </div>
                    </a>

                    {{--Notification cancel icon--}}
                    <i class="fa fa-times notification-delete"
                       aria-hidden="true"
                       onclick="app.cancelNotification(event, '{{ csrf_token() }}', '{{ route(\App\Utilities\Constants::ROUTES_NOTIFICATIONS_DELETE, $notification[\App\Utilities\Constants::FLD_NOTIFICATIONS_ID]) }}', this);">
                    </i>
                </li>

                @if(!$loop->last)
                    <li role="separator" class="divider">
                @endif
            @endforeach
        </ul>
    </li>
@endif