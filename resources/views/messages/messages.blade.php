{{-- meesages header --}}
    <div class="py-2 px-4 border-bottom d-none d-sm-block">
        <div class="d-flex align-items-center py-1">
            <div class="position-relative">
                <img src="{{ $receiver->avatar }}" class="rounded-circle mr-1" alt="Sharon Lessman" width="40" height="40">
            </div>
            <div class="flex-grow-1 pl-3">
                <strong>{{$receiver->name}}</strong>
                <div class="text-muted small" id="typing"><em>Typing...</em></div>
            </div>
            
            <div>
                <div class="input-group rounded">
                    <input type="search" class="form-control rounded" placeholder="Search this conversation" aria-label="Search" />
                        <button class="btn  btn-lg px-3"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-lg"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg></button>
                  </div>
            </div>
        </div>
    </div>
{{-- end of meesages header --}}

{{-- meesages--}}
    <div class="position-relative">
        <div class="chat-messages p-4">
            @foreach ($messages as $message)
                @if ($message->from == Auth::id())
                    {{-- your messaga (if message from = auth->id) --}}
                    <div class="chat-message-right pb-4">
                        <div>
                            <img src="{{ Auth::user()->avatar }}" class="rounded-circle mr-1" alt="You" width="40" height="40">
                            <div class="text-muted small text-nowrap mt-2">
                                {{$message->created_at->diffForHumans()}} 
                            </div>
                        </div>
                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                            <div class="font-weight-bold mb-1">You</div>
                            {{$message->message}}
                        </div>
                    </div>

                @else
                {{-- receiver message if (message to = receiver id) --}}
                <div class="chat-message-left pb-4">
                    <div>
                        <img src="{{ $receiver->avatar }}" class="rounded-circle mr-1" alt="{{$receiver->name}}" width="40" height="40">
                        <div class="text-muted small text-nowrap mt-2">{{$message->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
                        <div class="font-weight-bold mb-1">{{$receiver->name}}</div>
                        {{$message->message}}
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>

{{-- end of meesages--}}

{{-- send a message field --}}
    <div class="flex-grow-0 py-3 px-4 border-top">
            <div class="input-group">
                <input type="text" class="form-control send_message_input" id="send_message_input"  placeholder="Type your message">
                <button class="btn btn-primary" id="send">Send</button>
            </div>
    </div>
{{-- end of send a message field --}}