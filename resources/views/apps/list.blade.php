<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>Apps</title>

        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    </head>
    <body>
        <div>
            <table border="1">
                <thead>
                    <th>Plataforma</th>
                    <th>Versão</th>
                    <th>App Url</th>
                    <th>Ação</th>
                </thead>
                <tbody>
                @foreach($apps as $mapp)
                    <tr>
                        <td>{{ $mapp->platform }}</td>
                        <td><input id="{{$mapp->id}}-version" type="text" value="{{ $mapp->version }}"></td>
                        <td><input id="{{$mapp->id}}-appurl" type="text" value="{{ $mapp->app_url }}" size="100"></td>
                        <td><button onclick="_update({{$mapp->id}})">Atualizar</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div id="msg"></div>
        </div>

        <script>
            function _update(id) {
                document.getElementById('msg').innerText = 'Aguarde...';

                axios({
                    method: 'post',
                    url: '/dev/apps/update',
                    headers: {
                        'accept': 'application/json',
                        'content': 'application/json'
                    },
                    data: {
                        id: id,
                        version: document.getElementById(id + '-version').value,
                        app_url: document.getElementById(id + '-appurl').value
                    }
                })
                .then(function (response) {
                    document.getElementById('msg').innerText = 'Sucesso!';
                })
                .catch(function (error) {
                    document.getElementById('msg').innerText = 'Erro!';
                });
            }
        </script>
    </body>
</html>
