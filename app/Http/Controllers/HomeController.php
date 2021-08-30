<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $users = User::where('id', '!=', Auth::id())->get();

        // count how many message are unread from the selected user
        $users = DB::select("select users.id, users.name, users.avatar, users.email, count(is_read) as unread 
        from users LEFT  JOIN  messages ON users.id = messages.from and is_read = 0 and messages.to = " . Auth::id() . "
        where users.id != " . Auth::id() . " 
        group by users.id, users.name, users.avatar, users.email");

        return view('home', ['users' => $users]);
    }

    public function getMessage($user_id)
    {
        $receiver = User::find($user_id);
        $myId = Auth::id();
        // Make read all unread message
        Message::where(['from' => $user_id, 'to' => $myId])->update(['is_read' => 1]);

        $messages = Message::where(function ($query) use ($user_id, $myId){
            $query->where('from', $myId)->where('to', $user_id);
        })->orwhere(function ($query) use ($user_id, $myId){
            $query->where('from', $user_id)->where('to', $myId);
        })->get();    
        
        return view('messages.messages', ['messages' => $messages, 'receiver' => $receiver]);
    }

    public function sendMessage(Request $request)
    {
        $from = Auth::id();
        $to = $request->receiver_id;
        $message = $request->message;

        $newMessage = new Message();

        $newMessage->from = $from;
        $newMessage->to = $to;
        $newMessage->message = $message;
        $newMessage->is_read = 0;

        $newMessage->save();


        if ($newMessage)
        {
            broadcast(new MessageSent($newMessage))->toOthers();
            return response($newMessage, 200);
        }
        else{
            return response('Message Not Sent.', 200);
        }
    }
}
