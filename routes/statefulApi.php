<?php

/*
|--------------------------------------------------------------------------
| Stateful API Routes
|--------------------------------------------------------------------------
|
| These endpoints are meant to be used internally.
|
*/

use Illuminate\Support\Facades\Request;

Route::get('/auth/twitter', 'Auth\TwitterAuthController@redirectToProvider');
Route::get('/auth/twitter/callback', 'Auth\TwitterAuthController@handleProviderCallback');
Route::get('/auth/twitter/logout', 'Auth\TwitterAuthController@logout');

Route::get('/metadata', function (Request $request) {
    $user = $request::user();

    if ($user) {
        $user->addHidden(['created_at', 'updated_at']);
    }

    $schedules = \App\SalmonSchedule::whereRaw('TIMESTAMPADD(WEEK, -1, CURRENT_TIMESTAMP) < schedule_id')
        ->whereRaw('schedule_id < TIMESTAMPADD(WEEK, 1, CURRENT_TIMESTAMP)')
        ->get();

    $response = [
        'user' => $user,
        'schedules' => $schedules,
    ];

    return $response;
});

Route::post('/upload-results', 'SalmonResultController@store');

Route::get('/api-token', function (Request $request) {
    $user = $request::user();

    if (empty($user)) {
        abort(401);
    }

    $newApiToken = \App\Helpers\Helper::generateApiToken();
    $user->api_token = $newApiToken;
    $user->save();

    return ['api_token' => $newApiToken];
});