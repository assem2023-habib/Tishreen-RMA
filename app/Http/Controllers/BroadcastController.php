<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class BroadcastController extends Controller
{
    /**
     * Authenticate the incoming request.
     */
    public function authenticate(Request $request)
    {
        // التحقق من صحة token
        if (!Auth::guard('api')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::guard('api')->user();

        // التحقق من صحة القناة
        $channelName = $request->channel_name;

        if (str_starts_with($channelName, 'user.')) {
            $userId = explode('.', $channelName)[1];

            if ((int) $userId !== (int) $user->id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        }

        // إنشاء subscription
        $pusher = app('pusher');
        $response = $pusher->socket_auth($request->channel_name, $request->socket_id);

        return response($response);
    }

    /**
     * Get the channels the user can subscribe to.
     */
    public function channels(Request $request)
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'channels' => [
                'user.' . $user->id,
                'notifications',
                'user.' . $user->id . '.notifications'
            ]
        ]);
    }

    /**
     * Test WebSocket connection.
     */
    public function test(Request $request)
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'message' => 'WebSocket connection test successful',
            'user_id' => $user->id,
            'channels' => [
                'user.' . $user->id,
                'notifications'
            ]
        ]);
    }
}
