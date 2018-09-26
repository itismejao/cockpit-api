<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">

        <title>Apps</title>

    </head>
    <body>
        <div>
            <table border="1">
                <thead>
                    <th>&nbsp;</th>
                    <th>Total</th>
                </thead>
                <tbody>

                <tr>
                    <td>Downloads iOS Total</td>
                    <td>{{ $data['count_ios_total'] }}</td>
                </tr>

                <tr>
                    <td>Downloads iOS Verificados</td>
                    <td>{{ $data['count_ios_verified'] }}</td>
                </tr>

                <tr>
                    <td>Downloads Android Total</td>
                    <td>{{ $data['count_android_total'] }}</td>
                </tr>

                <tr>
                    <td>Downloads Android Verificados</td>
                    <td>{{ $data['count_android_verified'] }}</td>
                </tr>

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
