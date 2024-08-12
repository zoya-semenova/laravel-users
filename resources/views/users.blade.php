@extends('layouts.app')

@section('content')

    {{-- Store Modal --}}
    <div class="modal fade" id="storeUserModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="storeModalLabel">Создать/Редактировать пользователя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>

                <div class="modal-body">

                    <ul id="modal_msgList"></ul>

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

                        <div class="mt-3">
                            <img class="imgPhoto img img-circle"
                                 width="80" src=""/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary store_user">Сохранить</button>
                </div>

            </div>
        </div>
    </div>
    {{-- Edn- Store Modal --}}

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

                <div id="list_message"></div>

                <div class="card">
                    <div class="card-header">
                        <h4>
                            Пользователи
                            <button id="add" type="button" class="btn btn-primary float-end store-btn" data-bs-toggle="modal"
                                    data-bs-target="#storeUserModal">Добавить пользователя</button>
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
                            <td><button type="button" value="' + item.id + '" class="btn btn-primary store-btn btn-sm">Редактировать</button></td>\
                            <td><button type="button" value="' + item.id + '" class="btn btn-danger delete-btn btn-sm">Удалить</button></td>\
                        \</tr>');
                        });
                        $('.pagination').html(response.pagination);
                    }
                });
            }

            $(document).on('click', '.store-btn', function (e) {
                e.preventDefault();

                var user_id = $(this).val();
                // alert(user_id);
                $('#modal_msgList').html("");
                $('#modal_msgList').removeClass("alert alert-success alert-danger");
                $('#storeUserModal').find('input').val('');
                $('#storeUserModal').find('img').attr('src', '');
                $('#storeUserModal').modal('show');

                if (user_id) {
                    $.ajax({
                        type: "GET",
                        url: "/" + user_id,
                        success: function (response) {
                            // console.log(response.user.name);
                            $('#name').val(response.name);
                            $('#password').val(response.password);
                            $('.imgPhoto').attr('src', response.photo);
                            $('#user_id').val(user_id);
                        },
                        error: function (response) {
                            $('#list_message').addClass('alert alert-success');
                            $('#list_message').text(response.message);
                            $('#storeUserModal').modal('hide');
                        }
                    });
                }
            });

            $(document).on('click', '.store_user', function (e) {
                e.preventDefault();

                var data = new FormData();
                data.append('name', $('#name').val());
                data.append('password', $('#password').val());
                if ($('#photo').prop('files')[0]) {
                    data.append('photo', $('#photo').prop('files')[0]);
                }

                var id = $('#user_id').val();
                // alert(id);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/" + (id ? id : ''),
                    data: data,
                    processData: false,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    cache: false,
                    success: function (response) {
                        // console.log(response);

                        $('#list_message').addClass('alert alert-success');
                        $('#list_message').text(response.message);
                        $('#storeUserModal').modal('hide');

                        getUsers(page);
                    },
                    error: function (response) {

                        $('#modal_msgList').addClass('alert alert-danger');
                        $.each(response.responseJSON.errors, function (key, err_value) {
                            $('#modal_msgList').append('<li>' + err_value + '</li>');
                        });
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
                        $('#list_message').html("");
                        $('#list_message').addClass('alert alert-success');
                        $('#list_message').text(response.message);
                        $('.delete_user').text('Удалено');
                        $('#DeleteModal').modal('hide');

                        getUsers(page);
                    },
                    error: function (response) {
                        $('#list_message').addClass('alert alert-success');
                        $('#list_message').text(response.message);
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
