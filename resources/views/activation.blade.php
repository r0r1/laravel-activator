@extends('activator::layout')

@section('content')
    <h1>Hi {{ $user->name }}</h1>
    
    Please activate your account with click this <a href="url(config('activation_link'))">link</a>
@endsection