<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Tinkerwell Package</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .input-panel,
        .output-panel {
            flex: 1;
            padding: 20px;
            border: 1px solid #ccc;
        }

        #code-input {
            width: 100%;
            height: 300px;
            resize: vertical;
        }

        #execute-button {
            margin-top: 10px;
        }

        .output-panel {
            background-color: #f9f9f9;
        }

        #output {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="input-panel">
        <textarea id="code-input" placeholder="Enter your PHP code here">echo app()->version();</textarea>
        <button id="execute-button">Execute</button>
    </div>
    <div class="output-panel">
        <pre id="output"></pre>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const executeButton = document.getElementById("execute-button");
        const codeInput = document.getElementById("code-input");
        const outputElement = document.getElementById("output");

        executeButton.addEventListener("click", () => {
            const code = codeInput.value;

            if (code.length <= 0) {
                return;
            }

            let output = `<pre>Command:</pre> ${code}`;
            const xhttp = new XMLHttpRequest();

            xhttp.open('POST', 'php-tinker', true);
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    const response = JSON.parse(this.responseText);

                    output += `<pre>Result:</pre> ${(response.status === 1 ? '' : 'Error: ')} ${response.response}`;
                }

                outputElement.innerHTML = output;
            };
            xhttp.setRequestHeader("Content-Type", "application/json");
            xhttp.send(JSON.stringify({
                __token: '{{ csrf_token() }}',
                code: code,
            }));
        });
    });
</script>
</body>
</html>
