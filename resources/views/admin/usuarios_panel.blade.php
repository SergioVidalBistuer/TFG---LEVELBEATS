@extends('layouts.master')

@section('title', 'Panel Admin Usuarios')

@section('content')

    <h1>Panel de Administración de Usuarios</h1>

    <hr>

    <ul>
        <li>
            <a href="{{ route('usuario.index') }}">
                Ver listado de usuarios
            </a>
        </li>

        <li>
            <a href="{{ route('usuario.create') }}">
                Crear nuevo usuario
            </a>
        </li>
    </ul>

@endsection
