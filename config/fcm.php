<?php

return [

    'enabled' => env('FCM_ENABLED', true),

    'android_package' => env('FCM_ANDROID_PACKAGE', 'id.ac.unsulbar.portalfmipa'),

    'deadline_reminder_hours' => array_map(
        'intval',
        array_filter(explode(',', env('FCM_DEADLINE_REMINDER_HOURS', '24,72')))
    ),

];
