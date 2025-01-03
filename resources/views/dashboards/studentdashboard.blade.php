@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('main')
@include('student.home')
    @include('student.profile')
    @include('student.notification')
@endsection




