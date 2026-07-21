<!DOCTYPE html>
<html>
<head>
    <title>Reverb Test</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js'])

</head>

<body>

<h1>Reverb Testing</h1>

<div id="message"></div>


<script type="module">

window.Echo.private(
    "users.{{ auth()->id() }}"
)

.listen('NotificationSent', (event)=>{


    console.log(event);


    document.getElementById('message').innerHTML =
        JSON.stringify(event);


});


</script>


</body>
</html>