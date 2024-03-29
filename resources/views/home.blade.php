@include('navbar')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">

<head>
    <title>Home</title>
</head>
<div>
    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
    <h1>Welcome to The App, {{ $user }}</h1>
    @if(session()->has('success'))
        <div class="alert alert-success" style="margin-top: 5vh;">
            {{ session('success') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger" style="margin-top: 5vh;">
            {{ $errors->first() }}
        </div>
    @endif


    <div class="boxing">
        <h3>Incoming Requests</h3>
        <hr>
        <table class="table table-bordered table-wrap">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Description</th>
                    <th>Current Response</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($incoming as $item)
                        <td> {{ $item->from }} </td>
                        <td>{{ $item->description }}</td>
                        <td> {{ $item->state }} </td>
                        <td>
                            <div class="row res">
                                @if($item->state != 'accepted')
                                <form action="{{ route('respond') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="state" value="accepted">
                                    <input type="hidden" name="from" value="{{ $item->from }}">
                                    <input type="hidden" name="to" value="{{ $item->to }}">
                                    <div class="col">
                                    <button type="submit" class="btn btn-primary req">Accept</button>

                                    </div>
                                </form>
                                @endif

                                @if($item->state != 'rejected')
                                <form action="{{ route('respond') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="state" value="rejected">
                                    <input type="hidden" name="from" value="{{ $item->from }}">
                                    <input type="hidden" name="to" value="{{ $item->to }}">
                                    <div class="col">
                                    <button type="submit" class="btn btn-primary req">Reject</button>

                                    </div>
                                </form>
                                @endif

                                @if($item->state == 'accepted')
                                <form action="{{ route('send') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="state" value="email">
                                    <input type="hidden" name="from" value="{{ $item->from }}">
                                    <input type="hidden" name="to" value="{{ $item->to }}">
                                    <label for="orang">File to share :</label>

                                    <div class="col"><select class="form-control" id="orang" name="orang" style="margin-bottom:2vh">
                                        @foreach($orangs as $orang)
                                        <option value="{{$orang->id}}">{{$orang->nama . ' - ' . $orang->nomor_telepon}}</option>
                                        @endforeach
                                    </select></div>
                                    
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary req">Send Email</button>
                                    </div>
                                </form>
                            </div>
                            @endif
                            <form action="{{ route('respond') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="state" value="cancelled">
                                    <input type="hidden" name="from" value="{{ $item->from }}">
                                    <input type="hidden" name="to" value="{{ $item->to }}">
                                    <div class="col">
                                    <button type="submit" class="btn btn-primary req">Cancel</button>

                                    </div>
                                </form>
                            
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div class="boxing">
        <h3>Sent Requests</h3>
        <hr>
        <table class="table table-bordered table-wrap">
            <thead>
                <tr>
                    <th>To</th>
                    <th>Description</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($sent as $item)
                        <td> {{ $item->to }} </td>
                        <td>{{ $item->description }}</td>
                        <td> {{ $item->state }} </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        
    </div>
</div>
