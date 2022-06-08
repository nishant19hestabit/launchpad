@extends('layouts.app')
@section('content')
<h1 class="bg-info text-center">Student List</h1>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Profile Pic</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i)
                    <tr>
                        <th scope="row">{{$loop->iteration}}</th>
                        <td>{{$i->name}}</td>
                        <td>{{$i->email}}</td>
                        <td><img src="{{asset($i->profile_picture)}}" alt="" width="75" height="75"></td>
                        <td>
                            <a href="{{url('/student-edit/'.$i->id)}}" class="btn btn-sm btn-warning">Edit</a>
                            <a href="{{url('/student-delete/'.$i->id)}}" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>


        </div>
    </div>
</div>
@endsection