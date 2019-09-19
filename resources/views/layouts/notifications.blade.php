@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4>Success</h4>
    @if(is_array($message))
        @if(count($message)==0)
            <ul>
                @endif
                @foreach ($message as $m)
                    <li>{{ $m }}</li>
                @endforeach
                @if(count($message)==0)
            </ul>
        @endif
    @else 
    {{ $message }} 
    @endif
</div>
@endif 
@if (count($errors) > 0)
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">&times;</button>

    @if(is_array($message))
        <h4>Error</h4>
        @if(count($message)==0)
            <ul>
                @endif
                @foreach ($message as $m)
                    <li>{{ $m }}</li>
                @endforeach
                @if(count($message)==0)
            </ul>
        @endif
    @else
    <h4>Error</h4>
    {{ $message }} 
    @endif
</div>
@endif 
@if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4>Info</h4>
    @if(is_array($message))
        @if(count($message)==0)
            <ul>
                @endif
                @foreach ($message as $m)
                    <li>{{ $m }}</li>
                @endforeach
                @if(count($message)==0)
            </ul>
        @endif
    @else 
    {{ $message }} 
    @endif
</div>
@endif
@if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-block">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4>Note</h4>
        @if(is_array($message))
            @if(count($message)==0)
                <ul>
                    @endif
                    @foreach ($message as $m)
                        <li>{{ $m }}</li>
                    @endforeach
                    @if(count($message)==0)
                </ul>
            @endif
        @else
            {{ $message }}
        @endif
    </div>
@endif
@if ($message = Session::get('errorBag'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">&times;</button>

        @if(is_array($message))
            <h4>Errors</h4>
            @if(count($message)==0)
                <ul>
            @endif
            @foreach ($message as $m)
                <li>{{ $m }}</li>
            @endforeach
            @if(count($message)==0)
                </ul>
            @endif
        @else
            <h4>Error</h4>
            {{ $message }}
        @endif
    </div>
@endif
