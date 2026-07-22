
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Công cụ lọc tiêu đề tập phim</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f0f2f5;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 { color: #1a73e8; text-align: center; margin-top: 0; }
        textarea {
            width: 100%;
            height: 150px;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 15px;
            margin-bottom: 10px;
            outline: none;
            resize: vertical;
        }
        textarea:focus { border-color: #1a73e8; }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            transition: opacity 0.2s;
        }
        button:active { transform: translateY(1px); }
        .btn-process { background-color: #1a73e8; color: white; }
        .btn-copy { background-color: #34a853; color: white; }
        .btn-clear { background-color: #ea4335; color: white; }
        
        label { font-weight: bold; display: block; margin-bottom: 8px; color: #5f6368; }
        .status-msg {
            text-align: center;
            font-size: 14px;
            color: #34a853;
            height: 20px;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Lọc "Tập" & Số "0"</h2>

        <label for="input">Dán danh sách tại đây:</label>
        <textarea id="input" placeholder="Ví dụ:&#10;Tập 01&#10;Tập 009&#10;05 - Phần tiếp theo"></textarea>

        <div class="button-group">
            <button class="btn-process" onclick="processText()">Xử lý ngay</button>
            <button class="btn-copy" onclick="copyResult()">Sao chép</button>
            <button class="btn-clear" onclick="clearAll()">Xóa sạch</button>
        </div>

        <div id="status" class="status-msg"></div>

        <label for="output">Kết quả:</label>
        <textarea id="output" readonly placeholder="Kết quả hiển thị tại đây..."></textarea>
    </div>

    <script>
        function processText() {
            const input = document.getElementById('input').value;
            if (!input.trim()) {
                showStatus("Vui lòng nhập nội dung!");
                return;
            }

            const lines = input.split('\n');
            const processedLines = lines.map(line => {
                // Xóa chữ "Tập" và khoảng trắng đầu dòng
                let text = line.replace(/^[Tt]ập\s*/g, "").trim();
                // Xóa các số 0 ở đầu dòng
                return text.replace(/^0+/, "");
            });
            
            document.getElementById('output').value = processedLines.join('\n');
            showStatus("Đã xử lý xong!");
        }

        function copyResult() {
            const output = document.getElementById('output');
            if (!output.value) {
                showStatus("Chưa có gì để chép!");
                return;
            }
            output.select();
            document.execCommand('copy');
            showStatus("Đã sao chép vào bộ nhớ!");
        }

        function clearAll() {
            document.getElementById('input').value = "";
            document.getElementById('output').value = "";
            showStatus("Đã xóa hết!");
        }

        function showStatus(msg) {
            const statusDiv = document.getElementById('status');
            statusDiv.innerText = msg;
            setTimeout(() => { statusDiv.innerText = ""; }, 2000);
        }
    </script>

<script defer src="https://static.cloudflareinsights.com/beacon.min.js/v8c78df7c7c0f484497ecbca7046644da1771523124516" integrity="sha512-8DS7rgIrAmghBFwoOTujcf6D9rXvH8xm8JQ1Ja01h9QX8EzXldiszufYa4IFfKdLUKTTrnSFXLDkUEOTrZQ8Qg==" data-cf-beacon='{"version":"2024.11.0","token":"6593eb9236d54c95aa1e75fe198f8da9","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
</body>
</html>