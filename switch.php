<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css" />
    <title>⚡AUTO-TURN</title>
</head>

<body>
    <a href="#" id="logout" class="on">Logout</a>
    <div>
        <h2>Welcome, OTI Switch Admin!</h2>
        <!-- <button class="light off" onClick="turnOnOrOff()" id="turn-button" data-status="off">Turned OFF</button> -->
        <div id="devices"></div>
        <form action="#" method="POST" id="device-add-form">
            <div>
                <label>Device name</label>
                <input type="text" name="device-name" id="device-name" placeholder="Type here..." />
            </div>
            <div>
                <label>Device IP</label>
                <input type="text" name="device-ip" id="device-ip" placeholder="Type here..." />
            </div>
            <input type="submit" value="Add device" />
        </form>
    </div>
    <script>
        const logoutBtn = document.querySelector('#logout');
        const devices = document.querySelector('#devices');

        logoutBtn.onclick = () => {
            localStorage.removeItem("lgdi");
            window.location.href = "http://localhost/autoturn"
        }

        const addNewDeviceDiv = (device_name, device_ip) => {
            const deviceDiv = document.createElement('div');
            deviceDiv.id = "device";
            const iEl = document.createElement('i');
            iEl.classList.add('fas', 'fa-trash', 'delete');
            iEl.setAttribute('title', 'Remove device');
            iEl.onclick = () => {
                if (!confirm(`Are you sure you want to remove this device from the list?`)) return;
                const storedDevices = localStorage.getItem("devices");
                let storedDevicesArr = JSON.parse(storedDevices);
                storedDevicesArr = storedDevicesArr.filter((device) => device.ip != device_ip);
                localStorage.setItem("devices", JSON.stringify(storedDevicesArr));
                deviceDiv.remove();
                if (storedDevicesArr.length === 0) {
                    devices.innerHTML = `<h3 id="no-devices-yet">No devices on the list</h3>`
                }
            }

            const buttonEl = document.createElement('button');
            buttonEl.classList.add('light', 'off');
            buttonEl.setAttribute('data-status', 'off');
            buttonEl.innerHTML = 'Turned OFF';
            buttonEl.onclick = () => {
                turnOnOrOff(buttonEl, {
                    device_ip,
                    device_name
                })
            }
            deviceDiv.id = "device";
            deviceDiv.innerHTML = `
                <div>
                    <h3>${device_name}</h3>
                        <p>
                            ${device_ip}
                        </p>
                </div>
            `;
            const secDiv = document.createElement('div');
            secDiv.appendChild(iEl);
            secDiv.appendChild(buttonEl);
            deviceDiv.appendChild(secDiv);
            devices.appendChild(deviceDiv);
        }

        const turnOnOrOff = (turnBtn, device) => {
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

                        const xhr1 = new XMLHttpRequest();
                        xhr1.open("POST", "./utils/add_data.php", true);
                        xhr1.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        // xhr1.onreadystatechange = function() {
                        //     if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {}
                        // };
                        xhr1.send(`device_name=${device.device_name}&data_status=${status === "off" ? "ON" : "OFF"}`);
                    }
                };
            } catch (err) {
                alert(`Error: ${err.message}`)
            }
            xhr.open("GET", `${device.device_ip}:80/` + `?datastatus=${status === "off" ? "HIGH" : "LOW"}`);
            // xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send();
        }

        const deviceAddForm = document.querySelector('#device-add-form');
        deviceAddForm.onsubmit = (e) => {
            e.preventDefault();
            const deviceName = document.querySelector('#device-name').value;
            const deviceIp = document.querySelector('#device-ip').value;
            if (!deviceName || !deviceIp) {
                alert(`Please fill both fields`);
                return;
            }
            const regexExp = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/gi;

            if (deviceName.length > 50) {
                alert(`Device name should not be more than 50 characters`);
                return;
            }

            // validate IP address
            if (!regexExp.test(deviceIp)) {
                alert(`Please enter a valid IP address`);
                return;
            }

            const storedDevices = localStorage.getItem("devices");
            let wasEmpty = true;
            let storedDevicesArr = [];
            if (storedDevices) {
                storedDevicesArr = JSON.parse(storedDevices);
                if (storedDevicesArr.length > 0) wasEmpty = false;
            }

            if (storedDevicesArr.length >= 10) {
                alert(`You can't add more than 10 devices`);
                return;
            }

            if (storedDevicesArr.find((device) => device.name === deviceName)) {
                alert(`Device with name '${deviceName}' already exists`);
                return;
            }

            if ((storedDevicesArr.find((device) => device.ip === deviceIp))) {
                alert(`Device with IP '${deviceIp}' already exists`);
                return;
            }

            storedDevicesArr.push({
                name: deviceName,
                ip: deviceIp
            });
            localStorage.setItem("devices", JSON.stringify(storedDevicesArr));
            document.querySelector('#device-name').value = "";
            document.querySelector('#device-ip').value = "";
            if (wasEmpty) devices.innerHTML = "";
            addNewDeviceDiv(deviceName, deviceIp);
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

                const storedDevices = localStorage.getItem("devices");
                let noDevicesYet = true;
                if (storedDevices) {
                    const storedDevicesArr = JSON.parse(storedDevices);
                    if (storedDevicesArr.length > 0) noDevicesYet = false;
                    storedDevicesArr.forEach(device => {
                        const {
                            name,
                            ip
                        } = device;
                        addNewDeviceDiv(name, ip);
                    });
                }
                if (noDevicesYet) devices.innerHTML = `<h3 id="no-devices-yet">No devices on the list</h3>`
            } catch (err) {
                alert(`Error: ${err.message}`);
                window.location.location = "localhost/autoturn"
            }
        }
    </script>
</body>

</html>