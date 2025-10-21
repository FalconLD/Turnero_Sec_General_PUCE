<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ingreso</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="icon" type="image/png" href="{{ asset('login_template/images/icons/favicon1.ico') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/fonts/Linearicons-Free-v1.0.0/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('login_template/css/main.css') }}">
</head>
<body style="background-color: #666666;">
    
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                
                
                <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
                    
                    @csrf
                    <div style="text-align: center; padding-bottom: 25px;">
                        <img src="{{ asset('login_template/images/logo-puce.png') }}" alt="Logo PUCE" style="max-width: 320px;">
                    </div>

                    <span class="login100-form-title p-b-43">
                        Bienvenido al Turnero de Psicología Aplicada
                    </span>
                    
                    
                    <div class="wrap-input100 validate-input @error('email') alert-validate @enderror" data-validate = "El correo es requerido">
                        <input class="input100" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <span class="focus-input100"></span>
                        <span class="label-input100">Correo Institucional</span>
                    </div>
                    
                    
                    @error('email')
                        <span class="d-block text-danger text-center mb-3" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    
                    <div class="wrap-input100 validate-input @error('password') alert-validate @enderror" data-validate="La contraseña es requerida">
                        
                        <input class="input100" type="password" name="password" required autocomplete="current-password">
                        <span class="focus-input100"></span>
                        <span class="label-input100">Contraseña</span>
                    </div>

                    @error('password')
                        <span class="d-block text-danger text-center mb-3" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    <div class="flex-sb-m w-full p-t-3 p-b-32">
                        <div class="contact100-form-checkbox">
                            <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="label-checkbox100" for="ckb1">
                                Recuerdame
                            </label>
                        </div>                        
                    </div>
            
                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">
                            Ingresar
                        </button>
                    </div>
                    
                    
                </form>

                <div class="login100-more" style="background-image: url('{{ asset('login_template/images/bg-01.svg') }}');">
                    <div class="wrap-login100-more">
                        <div class="img100-wrap" style="background-size: contain; background-position: center; background-repeat: no-repeat;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <script src="{{ asset('login_template/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('login_template/vendor/animsition/js/animsition.min.js') }}"></script>
    <script src="{{ asset('login_template/vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('login_template/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('login_template/js/main.js') }}"></script>

</body>
</html>
