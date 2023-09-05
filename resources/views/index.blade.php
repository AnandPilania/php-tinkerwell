<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WebTinker - An online PHP code editor">
    <title>WebTinker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css"
          integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        html, body, * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            font-family: Arial, sans-serif;
            background-image: linear-gradient(45deg, #8d0096 0%, #ec4848 100%);
        }

        div.container {
            width: 100%;
            height: 100vh;
            display: flex;
        }

        .input-panel, .output-panel {
            width: 100%;
            display: inline-block;
            border: none;
            background-color: #FFF;
            margin: 5px 10px;
            resize: none;
            outline: none;
            border-radius: 10px;
        }

        .output-panel {
            padding: 20px;
        }

        #code-input {
            width: 100%;
            resize: none;
        }

        #execute-button {
            background: inherit;
            border: none;
        }

        .execute-container {
            position: fixed;
            top: 50%;
            left: 50%;
            margin-top: -26px;
            margin-left: -26px;
            padding: 4px;
            background: #fff;
            border: 1px solid;
            border-radius: 4px;
            width: 30px;
            height: 30px;
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
            border-radius: 10px;
            height: calc(100vh - 20px) !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="input-panel">
        <textarea id="code-input" placeholder="Enter your PHP code here">echo app()->version();</textarea>
    </div>
    <div class="execute-container">
        <button id="execute-button">
            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.46447 3.46447C2 4.92893 2 7.28595 2 12C2 16.714 2 19.0711 3.46447 20.5355C4.92893 22 7.28595 22 12 22C16.714 22 19.0711 22 20.5355 20.5355C22 19.0711 22 16.714 22 12C22 7.28595 22 4.92893 20.5355 3.46447C19.0711 2 16.714 2 12 2C7.28595 2 4.92893 2 3.46447 3.46447ZM6.42385 9.51988C6.68903 9.20167 7.16195 9.15868 7.48016 9.42385L7.75658 9.6542C8.36153 10.1583 8.87653 10.5874 9.23295 10.9821C9.61151 11.4013 9.90694 11.8834 9.90694 12.5C9.90694 13.1166 9.61151 13.5987 9.23295 14.0179C8.87653 14.4126 8.36153 14.8418 7.75658 15.3458L7.48016 15.5762C7.16195 15.8414 6.68903 15.7984 6.42385 15.4802C6.15868 15.1619 6.20167 14.689 6.51988 14.4239L6.75428 14.2285C7.41285 13.6797 7.84348 13.3185 8.11968 13.0126C8.38196 12.7222 8.40694 12.586 8.40694 12.5C8.40694 12.414 8.38196 12.2779 8.11968 11.9874C7.84348 11.6815 7.41285 11.3203 6.75429 10.7715L6.51988 10.5762C6.20167 10.311 6.15868 9.83809 6.42385 9.51988ZM17.75 15C17.75 15.4142 17.4142 15.75 17 15.75H12C11.5858 15.75 11.25 15.4142 11.25 15C11.25 14.5858 11.5858 14.25 12 14.25H17C17.4142 14.25 17.75 14.5858 17.75 15Z" fill="#1C274C"></path>
            </svg>
        </button>
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
            lineWrapping: true,
        });

        executeButton.addEventListener("click", () => {
            const code = codemirror.getValue();

            if (code.length <= 0) {
                return;
            }

            let output = `<pre class="parent">Command:</pre> ${code}`;

            fetch('web-tinker', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    __token: '{{ csrf_token() }}',
                    code: code,
                }),
            }).then(response => response.json())
                .then(data => {
                    outputElement.innerHTML = output + `<pre class="parent">Result:</pre> ${(data.status === 1 ? '' : 'Error: ')} ${data.response}`;
                }).catch(error => {
                console.error('Error:', error);
                outputElement.innerHTML = '<pre class="parent">Result:</pre> Error: Something went wrong.';
            });
        });
    });
</script>
</body>
</html>
