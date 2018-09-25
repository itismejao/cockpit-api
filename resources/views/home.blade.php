<doctype <!DOCTYPE html>
<html>
    <title>Cockpit Novo Mundo</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <style>
        img {
            max-width: 100%;
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
            font-family: Roboto;
        }

        .rodape {
            font-size: 80.0%;
        }
    </style>
 <body>
    <div id="content">
        <p>Bem Vindo! O Cockpit Novo Mundo é um aplicativo criado para acompanhamento de vendas, metas e resultados gerencias.</p>
        <p>O Cockpit Novo Mundo, está disponível nas plataformas Android e iOS.</p>
        <p>Abaixo, você pode clicar na sua plataforma desejada para obter o aplicativo.</p>

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