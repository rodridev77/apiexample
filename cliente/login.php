<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Api Client</title>
</head>
<body>
    
    <script src="jquery-3.6.0.min.js"></script>
    <script>
        let userEmail = "teste@mail.com";
        let userPassword = "123";

        function loginApi() {
            $.ajax({
                url: 'http://localhost/API-implementation-01/auth',
                method: 'POST',
                contentType: "application/json",
                data: JSON.stringify({"email" : userEmail, "password" : userPassword}),
            })
            .done(function(data) {
                console.log(data.user_token);
                localStorage.setItem('user_token_jwt', data.user_token);
            });
        }

        function allUsers() {
            $.ajax({
                url: 'http://localhost/API-implementation-01/api/users',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token_jwt')
                },
            })
            .done(function(data) {
                console.log(data);
            })
            .fail(function(data) {
                console.log(data.responseJSON)
                alert(data.responseJSON.message);
            });
        }

        function addUser() {
            $.ajax({
                url: 'http://localhost/API-implementation-01/api/users',
                method: 'POST',
                contentType: "application/json",
                data: JSON.stringify({"name" : "User", "email" : "user@mail.com", "password" : "abc"}),
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('user_token_jwt')
                },
            })
            .done(function(data) {
                console.log(data);
            })
            .fail(function(data) {
                console.log(data.responseJSON)
                alert(data.responseJSON.message);
            });
        }
    </script>
</body>
</html>