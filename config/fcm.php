<?php

return [

    'enabled' => env('FCM_ENABLED', true),

    'android_package' => env('FCM_ANDROID_PACKAGE', 'id.ac.unsulbar.portalfmipa'),

    'android_channel_id' => env('FCM_ANDROID_CHANNEL_ID', 'high_importance_channel'),

    'deadline_reminder_hours' => array_map(
        'intval',
        array_filter(explode(',', env('FCM_DEADLINE_REMINDER_HOURS', '24,72')))
    ),

];
