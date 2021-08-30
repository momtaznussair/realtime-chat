@extends('layouts.app')

@section('content')
<main class="content">
    <div class="container p-0">
       
        <div class="card">
            <div class="row g-0">
                <div class="col-12 col-lg-5 col-xl-3 border-right">

                    <div class="px-4 d-none d-md-block">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <input type="text" class="form-control my-3" placeholder="Search...">
                            </div>
                        </div>
                    </div>

                    <!-- end of search -->
                    
                    <a href="#" class="list-group-item list-group-item-action border-0" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <div class="d-flex align-items-start">

                            <button class="btn btn-link"type="button" data-toggle="collapse" data-target="#friendsList" aria-expanded="true" aria-controls="friendsList">
                                <i class="far fa-address-book" style="font-size:2rem;"></i>
                            </button>
                            
                            <div class="flex-grow-1 ml-3">
                                My Contacts
                                <div class="small"><span class="fas fa-circle chat-online"></span> Online 3</div>
                            </div>
                        </div>
                    </a>
                           
                    <div class="friends-list collapse show" id="friendsList">
                        @foreach ($users as $user)
                        <a href="#" class="list-group-item list-group-item-action border-0 user" id="{{$user->id}}">
                            @if ($user->unread)
                                <div class="badge bg-success float-right pending">{{$user->unread}}</div>
                            @endif
                            <div class="d-flex align-items-start">
                                <img src="{{$user->avatar}}" class="rounded-circle mr-1" alt="Jennifer Chang" width="40" height="40">
                                <div class="flex-grow-1 ml-3">
                                    {{$user->name}}
                                    <div class="small"><span class="fas fa-circle chat-offline"></span> Offline</div>
                                </div>
                            </div>
                        </a>
                        <hr class="d-block d-lg-none mt-1 mb-0">
                        @endforeach                    
                    </div>
                    {{-- end of friends list --}}

                </div>
                
                <div id="messages-container" class="col-12 col-lg-7 col-xl-9">
                    <p>No messages</p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
