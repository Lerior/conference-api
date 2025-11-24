<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    public function addTopic (Request $request) {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:255',
            'description' => 'required|string|max:5000',
            'speaker_name' => 'required_without:user_id|string|max:120',
            'user_id'=> 'required_without:speaker_name|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()],422);
        }

        $validated = $validator->validate();
        
        Topic::created($validated);
        
        return response()->json(['message' => 'Topic created successfully'], 201);
    }
}
