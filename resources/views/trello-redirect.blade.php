<!DOCTYPE html>
<html>
<head>
    <title>Trello Auth Redirect</title>
    <script type="text/javascript">
        window.onload = function() {
            // Extract the token from the URL fragment
            const fragment = new URLSearchParams(window.location.hash.substring(1));
            const token = fragment.get('token');

             if (token) {
                 console.log(token);

                 window.location.href = `/trello-auth?token=${token}`;
             } else {
                 // Handle the error case where the token is missing
                 console.error('Token not found in the URL');
             }
        };
    </script>
</head>
<body>
Redirecting...
</body>
</html>
