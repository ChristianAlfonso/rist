<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifcation code</title>
</head>
<body>
    <h1>We have sent the verification code in your submitted email</h1>
    <input type="text">
    <span class = "resend" disabled>resend code in <span class = "countdown"></span></span>
    <button>Submit</button>
</body>
<script>
    let timeleft = 10;
    let downloadTimer = setInterval(function(){
    timeleft--;
    document.getElementsByClassName("countdown").innerHTML = timeleft;
    if(timeleft <= 0)
        clearInterval(downloadTimer);
    },1000);
    if(timeleft == 0){
        document.getElementsByClassName("resend").disabled = false;
    }
</script>
</html>