@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('main')
@include('student.home')
    @include('student.profile')
    @include('student.notification')
    @include('student.chairmanvideo')
@endsection




