@extends('layouts.app')

@section('content')

    {{-- Add Modal --}}
    <div class="modal fade" id="AddUserModal" tabindex="-1" aria-labelledby="AddUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddUserModalLabel">Создать пользователя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">

                    <ul id="save_msgList"></ul>

                    <div class="form-group mb-3">
                        <label for="">Имя</label>
                        <input type="text" required class="name form-control">
                        <span class="text-danger error-text name_error"></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Пароль</label>
                        <input type="text" required class="password form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Фото</label>
                        <input type="file" name="photo" class="photo form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary add_user">Сохранить</button>
                </div>

            </div>
        </div>
    </div>


    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Редактировать пользователя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>

                <div class="modal-body">

                    <ul id="update_msgList"></ul>

                    <input type="hidden" id="user_id" />

                    <div class="form-group mb-3">
                        <label for="">Имя</label>
                        <input type="text" id="name" required class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Пароль</label>
                        <input type="text" id="password" required class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Фото</label>
                        <input type="file" id="photo" class="form-control">
                        <div>
                            <img class="imgPreview img img-circle"
                                 width="80" src=""/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary update_user">Сохранить</button>
                </div>

            </div>
        </div>
    </div>
    {{-- Edn- Edit Modal --}}


    {{-- Delete Modal --}}
    <div class="modal fade" id="DeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Удалить</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <h4>Удалить пользователя?</h4>
                    <input type="hidden" id="delete_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary delete_user">Удалить</button>
                </div>
            </div>
        </div>
    </div>
    {{-- End - Delete Modal --}}

    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">

                <div id="success_message"></div>

                <div class="card">
                    <div class="card-header">
                        <h4>
                            Пользователи
                            <button id="add" type="button" class="btn btn-primary float-end" data-bs-toggle="modal"
                                    data-bs-target="#AddUserModal">Добавить пользователя</button>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Пароль</th>
                                <th>Редактировать</th>
                                <th>Удалить</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                {!! $users->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function () {

            var page=1;
            getUsers();

            function getUsers(page) {
                $.ajax({
                    type: "GET",
                    url: "/?page="+page,
                    dataType: "json",
                    success: function (response) {
                        // console.log(response);
                        $('tbody').html("");
                        $.each(response.users.data, function (key, item) {
                            $('tbody').append('<tr>\
                            <td>' + item.id + '</td>\
                            <td>' + item.name + '</td>\
                            <td>' + item.password + '</td>\
                            <td><button type="button" value="' + item.id + '" class="btn btn-primary edit-btn btn-sm">Редактировать</button></td>\
                            <td><button type="button" value="' + item.id + '" class="btn btn-danger delete-btn btn-sm">Удалить</button></td>\
                        \</tr>');
                        });
                        $('.pagination').html(response.pagination);
                    }
                });
            }

            $('.add_user').on('click', function (e) {
                e.preventDefault();

                $(this).text('Отправка...');

                var data = new FormData();
                data.append('name', $('.name').val());
                data.append('password', $('.password').val());
                data.append('photo', $('.photo').prop('files')[0]);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/",
                    data: data,
                    processData: false,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    cache: false,
                    success: function (response) {
                        // console.log(response);
                        $('#save_msgList').html("");
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('#AddUserModal').find('input').val('');
                        $('.add_user').text('Сохранить');
                        $('#AddUserModal').modal('hide');

                        getUsers(page);
                    },
                    error: function (response) {
                        $('#save_msgList').html("");
                        $('#save_msgList').addClass('alert alert-danger');
                        $.each(response.responseJSON.errors, function (key, err_value) {
                            $('#save_msgList').append('<li>' + err_value + '</li>');
                        });
                        $('.add_user').text('Сохранить');
                    }
                });
            });

            $(document).on('click', '.edit-btn', function (e) {
                e.preventDefault();
                var user_id = $(this).val();
                // alert(user_id);
                $('#editModal').modal('show');
                $.ajax({
                    type: "GET",
                    url: "/" + user_id,
                    success: function (response) {
                        // console.log(response.user.name);
                        $('#name').val(response.name);
                        $('#password').val(response.password);
                        $('#user_id').val(user_id);
                    },
                    error: function (response) {
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('#editModal').modal('hide');
                    }
                });
                //$('.btn-close').find('input').val('');
            });

            $(document).on('click', '.update_user', function (e) {
                e.preventDefault();

                $(this).text('Отправка...');
                var id = $('#user_id').val();
                // alert(id);

                var data = {
                    'name': $('#name').val(),
                    'password': $('#password').val(),
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "PUT",
                    url: "/" + id,
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        // console.log(response);

                        $('#update_msgList').html("");

                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('#editModal').find('input').val('');
                        $('.update_user').text('Update');
                        $('#editModal').modal('hide');

                        getUsers(page);
                    },
                    error: function (response) {
                        $('#update_msgList').html("");
                        $('#update_msgList').addClass('alert alert-danger');
                        $.each(response.responseJSON.errors, function (key, err_value) {
                            $('#update_msgList').append('<li>' + err_value + '</li>');
                        });
                        $('.update_user').text('Сохранить');
                    }
                });
            });

            $(document).on('click', '.delete-btn', function () {
                var user_id = $(this).val();
                $('#DeleteModal').modal('show');
                $('#delete_id').val(user_id);
            });

            $(document).on('click', '.delete_user', function (e) {
                e.preventDefault();

                $(this).text('Отправка...');
                var id = $('#delete_id').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "DELETE",
                    url: "/" + id,
                    dataType: "json",
                    success: function (response) {
                        // console.log(response);
                        $('#success_message').html("");
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('.delete_user').text('Удалено');
                        $('#DeleteModal').modal('hide');

                        getUsers(page);
                    },
                    error: function (response) {
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('.delete_user').text('Удалить');
                    }

                });
            });
            $(document).on('click', '.pagination a',function(event)
            {
                $('li').removeClass('active');
                $(this).parent('li').addClass('active');
                event.preventDefault();

                page=$(this).attr('href').split('page=')[1];

                getUsers(page);
            });
        });

    </script>

@endsection
