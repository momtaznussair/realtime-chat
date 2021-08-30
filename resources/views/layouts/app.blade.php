<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67cdfea5ae.js" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chat.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://js.pusher.com/7.0.3/pusher.min.js"></script>
    <script src="js/app.js"></script>

    <script>
        let receiver_id = '';
        let my_id = "{{ Auth::id() }}";

        $(document).ready(function () {

            // ajax setup form csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            Echo.private('chat')
            .listen('MessageSent', (data) => {
                if (receiver_id == data.message.from) {
                    // if receiver is selected, reload the selected user ...
                    $('#' + data.message.from).click();
                } else {
                    // if receiver is not seleted, add notification for that user
                    var pending = parseInt($('#' + data.message.from).find('.pending').html());
                    if (pending) {
                        $('#' + data.message.from).find('.pending').html(pending + 1);
                    } else {
                        $('#' + data.message.from).prepend('<span class="badge bg-success float-right pending">1</span>');
                    }
                }
            });


            // (typing indicator)

            // will be called when keydown event is fired on send a message  field
            let user;
            function isTyping() {
                let channel = Echo.private('chat');
                
                setTimeout(function() {
                    channel.whisper('typing', {
                    from: my_id,
                    to: receiver_id,
                    typing: true
                    });
                }, 300);
            }

            //listenig for typing indicator
            Echo.private('chat')
            .listenForWhisper('typing', (e) => {
            // show typing indicator
            if(e.from == receiver_id && e.to == my_id)
            {
                $('#typing').show();
            }
            // remove is typing indicator after 0.9s
            setTimeout(function() {
               $('#typing').hide();
            }, 900);
            });

            // trigger typing indicator onkeydown on send_message_input
            $('#messages-container').keydown(function (e) {
                if(e.target.id == 'send_message_input')
                {
                    isTyping();
                }
            });

            // (end of typing indicator)
            

            // // get user messages

            $('.user').click(function () {

                $(this).find('.pending').remove();

                receiver_id = $(this).attr("id");

                $.ajax({
                type: "get",
                url: "message/" + receiver_id, // need to create this route
                data: "",
                cache: false,
                success: function (data) {
                    $('#messages-container').html(data);
                    scrollToBottomFunc();
                }
            });
            });// end of click on user event
            
            //send on click  on send button
            $('#messages-container').click(function (e) {
                let message = $('.send_message_input').val();
                if(e.target.id == 'send' && message != '' && receiver_id != '')
                {
                    sentMessage(message);
                }
            });
            // send on enter key pressed
            $('#messages-container').keyup(function (e) {
                let message = $('.send_message_input').val();
                if(e.target.id == 'send_message_input' && e.keyCode == 13 && message != '' && receiver_id != '')
                {
                    sentMessage(message);
                }
            });

            // send a message to the selected user
            function sentMessage(message) {

                    let dataStr = {'receiver_id': receiver_id, 'message' : message}
                    $('.send_message_input').val('');
                    $.ajax({
                        type: "post",
                        url: "message",
                        data: dataStr,
                        cache:false,
                        success: function (response) {
                            console.log(response);
                            $('#' + response.to).click();
                        },
                        error: function(jqXHR, status, err)
                        {
                            console.log(err);
                        },
                        complete: function () {
                            
                        }
                    });
            }
            
            // make a function to scroll down auto
            function scrollToBottomFunc() {
                    $('.chat-messages').animate({
                        scrollTop: $('.chat-messages').get(0).scrollHeight
                    }, 50);
                }
        });// end of ready event
    </script>
    
</body>
</html>
