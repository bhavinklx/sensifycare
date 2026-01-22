@extends("admin.layouts.app")
@section('content')
    <section id="wrapper">
        <div class="login-register" style="padding: 0">

            <p>User Name: {{ $userDetail->name }}</p>
            <p>User Mobile: {{ $userDetail->phone }}</p>
            <div class="login-box card">

                <div class="alert alert-success alert-rounded">
                    <p>User Name: {{ $userDetail->name }}</p>
                    <p>User Mobile: {{ $userDetail->phone }}</p>
                </div>

                <div class="card-body">
                    <form id="userFrm" class="floating-labels" method="post" action="{{ route("user-changepassword-update") }}">
                        <input type="hidden" name="user_id" value="{{ $userDetail->id }}">
                        @csrf
                        <h3 class="box-title m-b-20">Change Password</h3>

                        <div class="form-group m-b-40" id="c_pass">
                            <input type="password" class="form-control" name="password" id="password" >
                            <span class="bar"></span>
                            <label for="password">Password</label>
                            <span class="help-block"><small id="msg_password" class="text-danger"></small></span>
                        </div>

                        <div class="form-group m-b-40" id="c_repass">
                            <input type="password" class="form-control" name="repassword" id="repassword" >
                            <span class="bar"></span>
                            <label for="repassword">Confirm Password</label>
                            <span class="help-block"><small id="msg_repassword" class="text-danger"></small></span>
                        </div>

                        <div class="form-group text-center p-b-10">
                            <div class="col-xs-12">
                                <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" name="submit" type="submit">Change Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page-js')
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function(){
                $(".dropify-wrapper").css("width","100%");

            },100);
        });

        $('#userFrm').on('submit', function(e)
        {
            e.preventDefault();
            var form    = $('#userFrm')[0];
            var formData= new FormData(form);
            $("#userFrm").find(".has-error").removeClass("has-error");
            $(".bar").html("");
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                url: $(this).attr('action'),
                cache : false,
                enctype: 'multipart/form-data',
                contentType: false,
                processData: false,
                data : formData,
                success: function(response){
                    //alert(response.redirect_url)
                    if(response.status == "validation-error")
                    {
                        $.each(response.data, function (key, value)
                        {
                            $("#"+key).parent("div").addClass("has-error");
                            //$("#"+key).next().html("<small class='text-danger'>" + value + "</small>");
                            $("#msg_"+key).html(value);
                        });
                    }
                    else if (response.redirect_url !== undefined)
                    {
                        window.location = "{{ url('admin/user-list') }}";
                    }
                },
            });
        });
    </script>
@endsection
