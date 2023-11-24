@include('navbar')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">

<div>
    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
    <h1>Welcome to The App, {{ $user }}</h1>


    <div class="boxing">
        <h3>Incoming Requests</h3>
        <hr>
        Requests here
    </div>

    <div class="boxing">
        <h3>Sent Requests</h3>
        <hr>
        <table class="table table-bordered table-wrap">
            <thead>
                <tr>
                    <th>To</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($sent as $item)
                        <td> {{ $item->to }} </td>
                        <td> {{ $item->state }} </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        
    </div>
</div>
