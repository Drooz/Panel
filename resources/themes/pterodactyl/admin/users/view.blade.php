{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- Permission is hereby granted, free of charge, to any person obtaining a copy --}}
{{-- of this software and associated documentation files (the "Software"), to deal --}}
{{-- in the Software without restriction, including without limitation the rights --}}
{{-- to use, copy, modify, merge, publish, distribute, sublicense, and/or sell --}}
{{-- copies of the Software, and to permit persons to whom the Software is --}}
{{-- furnished to do so, subject to the following conditions: --}}

{{-- The above copyright notice and this permission notice shall be included in all --}}
{{-- copies or substantial portions of the Software. --}}

{{-- THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR --}}
{{-- IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, --}}
{{-- FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE --}}
{{-- AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER --}}
{{-- LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, --}}
{{-- OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE --}}
{{-- SOFTWARE. --}}
@extends('layouts.admin')

@section('title')
    Manager User: {{ $user->username }}
@endsection

@section('content-header')
    <h1>{{ $user->name_first }} {{ $user->name_last}}<small>{{ $user->username }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.users') }}">Users</a></li>
        <li class="active">{{ $user->username }}</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <form action="{{ route('admin.users.view', $user->id) }}" method="post">
        <div class="col-xs-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Identity</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <div>
                            <input type="text" name="email" value="{{ $user->email }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="registered" class="control-label">Username</label>
                        <div>
                            <input type="text" name="username" value="{{ $user->username }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="registered" class="control-label">Client First Name</label>
                        <div>
                            <input type="text" name="name_first" value="{{ $user->name_first }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="registered" class="control-label">Client Last Name</label>
                        <div>
                            <input type="text" name="name_last" value="{{ $user->name_last }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    {!! csrf_field() !!}
                    <input type="submit" value="Update User" class="btn btn-primary btn-sm">
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Password</h3>
                </div>
                <div class="box-body">
                    <div class="alert alert-success" style="display:none;margin-bottom:10px;" id="gen_pass"></div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password</label>
                        <div>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default btn-sm" id="gen_pass_bttn" type="button">Generate Password</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Permissions</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="root_admin" class="control-label">Administrator</label>
                        <div>
                            <select name="root_admin" class="form-control">
                                <option value="0">{{ trans('strings.no') }}</option>
                                <option value="1" {{ $user->root_admin ? 'selected="selected"' : '' }}>{{ trans('strings.yes') }}</option>
                            </select>
                            <p class="text-muted"><small>Setting this to 'Yes' gives a user full administrative access.</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Associated Servers</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width:2%;"></th>
                            <th>Identifier</th>
                            <th>Server Name</th>
                            <th>Access</th>
                            <th>Node</th>
                            <th style="width:10%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->access()->get() as $server)
                            <tr>
                                <td><a href="{{ route('server.index', $server->uuidShort) }}/"><i class="fa fa-tachometer"></i></a></td>
                                <td><code>{{ $server->uuidShort }}</code></td>
                                <td><a href="{{ route('admin.servers.view', $server->id) }}">{{ $server->name }}</a></td>
                                <td>
                                    @if($server->owner_id === $user->id)
                                        @lang('strings.owner')
                                    @else
                                        @lang('strings.subuser')
                                    @endif
                                </td>
                                <td><a href="{{ route('admin.nodes.view', $server->node->id) }}">{{ $server->node->name }}</a></td>
                                <td class="centered">@if($server->suspended === 0)<span class="label muted muted-hover label-success">Active</span>@else<span class="label label-warning">Suspended</span>@endif</td>
                            </td>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </form>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Delete User</h3>
            </div>
            <div class="box-body">
                <p class="no-margin">There must be no servers associated with this account in order for it to be deleted.</p>
            </div>
            <div class="box-footer">
                <form action="{{ route('admin.users.view', $user->id) }}" method="POST">
                    {!! csrf_field() !!}
                    {!! method_field('DELETE') !!}
                    <input id="delete" type="submit" class="btn btn-sm btn-danger pull-right" {{ $user->servers->count() < 1 ?: 'disabled' }} value="Delete User" />
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>$("#gen_pass_bttn").click(function (event) {
            event.preventDefault();
            $.ajax({
                type: "GET",
                url: "/password-gen/12",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
               },
                success: function(data) {
                    $("#gen_pass").html('You must click <em>Update Password</em> below for this password to be applied.<br /><br /><strong>Generated Password:</strong> ' + data).slideDown();
                    $('input[name="password"], input[name="password_confirmation"]').val(data);
                    return false;
                }
            });
            return false;
        });
    </script>
@endsection
