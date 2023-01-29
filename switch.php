<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./index.css" />
    <title>⚡AUTO-TURN</title>
</head>

<body>
    <h2>Welcome!</h2>
    <button class="light off" onClick="turnOnOrOff()" id="turn-button" data-status="off">Turned OFF</button>
    <script>
        const turnOnOrOff = () => {
            const turnBtn = document.querySelector('#turn-button')
            const status = turnBtn.getAttribute("data-status")
            turnBtn.innerHTML = `Turning ${status === "off" ? "on" : "off"}...`
            var xhr = new XMLHttpRequest();
            try {
                xhr.onreadystatechange = function() {
                    //console.log("readee", xhr.readyState, xhr.status, "'" + xhr.responseText + "'")
                    if (xhr.readyState === 4) {
                        // var response = xhr.responseText;
                        turnBtn.classList.remove(`${status}`)
                        turnBtn.classList.add(`${status === "off" ? "on" : "off"}`)
                        turnBtn.setAttribute("data-status", `${status === "off" ? "on" : "off"}`)
                        turnBtn.innerHTML = `Turned ${status === "off" ? "ON" : "OFF"}`;
                    } else {
                        alert("Failed! Couldn't connect to the microcontroller.")
                    }
                };
            } catch (err) {
                alert(`Error: ${err.message}`)
            }
            xhr.open("GET", "http://192.168.8.111:80/" + `?datastatus=${status === "off" ? "HIGH" : "LOW"}`);
            // xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send();
        }

        window.onload = () => {
            try {
                const stored = localStorage.getItem("lgdi");
                let loggedIn = true;
                if (!stored) {
                    loggedIn = false;
                } else {
                    const storedObj = JSON.parse(stored);
                    if (storedObj?.email !== "admin@switches.net" || storedObj?.password !== "123switch") {
                        loggedIn = false;
                    }
                }
                if (!loggedIn) {
                    alert(`You need to login first`);
                    window.location.href = "http://localhost/autoturn"
                }
            } catch (err) {
                alert(`Error: ${err.message}`);
                window.location.location = "localhost/autoturn"
            }
        }
    </script>
</body>

</html>