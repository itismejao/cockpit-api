<!DOCTYPE html>
<html>
<head>
    <title>Cockpit Novo Mundo</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/app.css">
</head>    
 <body>
    <div id="content">
        <p>Bem Vindo família Novo Mundo! O Cockpit é um aplicativo criado para acompanhamento de seus indicadores.</p>
        <p>Para entrar no aplicativo, você deve inserir seu <b>RE</b> e <b>Senha</b> (a mesma do Vtrine).</p>
        <p>Abaixo, você pode clicar na sua plataforma desejada para obter o aplicativo.</p>
        <p><b>Você utiliza um dispositivo da Apple?</b> <a href="/ios-tutorial" target="_blank">Clique aqui</a> para ver o passo a passo da instalação.</p>

        <div id="left">
            <a href="https://play.google.com/store/apps/details?id=com.novomundo.labs.iceberg">
                <img src="img/botao-android.png" alt="Cockpit para Android">
            </a>
        </div>

        
        <div>&nbsp;</div>
        
        <div id="left">
            <!--<a href="itms-services://?action=download-manifest&url=https://cockpit.novomundo.com.br/ios/{{$iosVersion}}/manifest.plist">
                <img src="img/botao-ios.png" alt="Cockpit para iOS">
            </a>-->
            @if($iosVersion)
            Versão: {{$iosVersion}}
            @endif
            <a href="itms-services://?action=download-manifest&url=https://cockpit.novomundo.com.br/ios/build/manifest.plist">
                <img src="img/botao-ios.png" alt="Cockpit para iOS">
            </a>
        </div>
        

        <div>&nbsp;</div>

        <center class="rodape">Novo Mundo Móveis e Utilidades LTDA - {!! date('Y') !!} </center>
        <center class="rodape">API Version {!! \Tremby\LaravelGitVersion\GitVersionHelper::getVersion(); !!}</center>
    </div>
    
 </body>
</html>