<?php

function error($message = 'An error has occurred.', $code = 404): \Illuminate\Http\JsonResponse
{
    return response()->json(['success' => false, 'message' => $message], $code);
}

function success($message = 'Success', $code = 200): \Illuminate\Http\JsonResponse
{
    return response()->json(['success' => true, 'message' => $message], $code);
}
