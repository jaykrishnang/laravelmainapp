@include('header')

@if(session()->has('success'))
<div class="container container--narrow">
    <div class="alert alert-success text-center">
        {{session('success')}}
    </div>
</div>
@endif

@if(session()->has('failure'))
<div class="container container--narrow">
    <div class="alert alert-danger text-center">
        {{session('failure')}}
    </div>
</div>
@endif

{{$slot}}

@include('footer')