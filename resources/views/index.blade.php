<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebTinker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css"
          integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .input-panel, .output-panel {
            flex: 1;
            border: 1px solid #ccc;
        }

        .output-panel {
            padding: 20px;
        }

        #code-input {
            width: 100%;
            resize: none;
        }

        #execute-button {
            float: right;
        }

        .output-panel {
            background-color: #f9f9f9;
        }

        #output {
            white-space: pre-wrap;
        }

        pre.parent {
            font-weight: bold;
            color: #939393;
        }

        .CodeMirror {
            height: calc(100vh - 20px) !important;
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js"
        integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/php/php.min.js"
        integrity="sha512-jZGz5n9AVTuQGhKTL0QzOm6bxxIQjaSbins+vD3OIdI7mtnmYE6h/L+UBGIp/SssLggbkxRzp9XkQNA4AyjFBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const executeButton = document.getElementById("execute-button");
        const codeInput = document.getElementById("code-input");
        const outputElement = document.getElementById("output");
        const codemirror = CodeMirror.fromTextArea(codeInput, {
            lineNumbers: true,
            matchBrackets: true,
            mode: 'text/x-php',
            indentUnit: 4,
            indentWithTabs: true,
        });

        executeButton.addEventListener("click", () => {
            const code = codemirror.getValue();

            if (code.length <= 0) {
                return;
            }

            let output = `<pre class="parent">Command:</pre> ${code}`;
            const xhttp = new XMLHttpRequest();

            xhttp.open('POST', 'web-tinker', true);
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    const response = JSON.parse(this.responseText);

                    output += `<pre class="parent">Result:</pre> ${(response.status === 1 ? '' : 'Error: ')} ${response.response}`;
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
