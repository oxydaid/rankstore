@extends('errors::minimal')

@section('title', __('Dilarang Akses'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Dilarang Akses'))
