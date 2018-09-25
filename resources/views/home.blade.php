<doctype <!DOCTYPE html>
<html>
    <title>Cockpit Novo Mundo</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        img {
            max-width: 100%;
        }

        @font-face {
            font-family: 'UniNeueBook';
            src: url('fonts/UniNeueBook-Regular.otf');
        }

        #content {
            margin: 0 auto;
            top: 50%; /* IMPORTANT */
            left: 50%; /* IMPORTANT */
            display: block;
            max-width: 600px;
        }

        #linkapp {
            float:left;
        }

        body {
            background-color: #f3f3f3;
            font-family: 'UniNeueBook';
        }

        .rodape {
            font-size: 80.0%;
        }
    </style>
 <body>
    <div id="content">
        <p>Bem Vindo família Novo Mundo! O Cockpit Novo Mundo é um aplicativo criado para acompanhamento gerenciais de seus indicadores.</p>
        <p>Para entrar no aplicativo, você deve inserir seu <b>RE</b> e <b>Senha</b> (a mesma do Vtrine).</p>
        <p>O Cockpit Novo Mundo, está disponível nas plataformas Android e iOS.</p>

        <div id="linkapp">
            <a href="https://play.google.com/store/apps/details?id=com.novomundo.labs.iceberg">
                <img src="img/botao-android.png" alt="Cockpit para Android">
            </a>
        </div>

        <div>&nbsp;</div>

        <div id="linkapp">
            <a href="itms-services://?action=download-manifest&url=https://cockpith.novomundo.com.br/ios/manifest.plist">
                <img src="img/botao-ios.png" alt="Cockpit para iOS">
            </a>
        </div>

        <div>&nbsp;</div>

        <center class="rodape">Novo Mundo Móveis e Utilidades LTDA - {!! date('Y') !!} </center>
        <center class="rodape">API Version {!! \Tremby\LaravelGitVersion\GitVersionHelper::getVersion(); !!}</center>
    </div>
    
 </body>
</html>