@extends('visitor.layout.app')

@section('content')
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">

                    <div class="card shadow">
                        <div class="card-body">

                            <ul class="nav nav-tabs" id="authTab" role="tablist">
                                {{-- <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#login">
                                        Sign In
                                    </a>
                                </li> --}}

                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#register">
                                        Sign Up
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-4">

                                <!-- LOGIN -->
                                {{-- <div class="tab-pane fade show active" id="login">
                                    <form>

                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" class="form-control">
                                        </div>

                                        <button class="btn btn-primary btn-block">
                                            Sign In
                                        </button>

                                    </form>
                                </div> --}}

                                <!-- REGISTER -->
                                <div class="tab-pane fade show active" id="register">
                                    <form>

                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input type="text" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label>Upload KTP</label>
                                            <input type="file" name="ktp" class="form-control">
                                        </div>

                                        <button class="btn btn-success btn-block">
                                            Register
                                        </button>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
