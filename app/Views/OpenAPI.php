<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $spec['info']['title'] ?> - <?= $spec['info']['version'] ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.0.0-rc.2/swagger-ui.css" integrity="sha512-gu8R7CuuszjbyYpSy4XxJYfbzK0WrAybPxykNEJPKVjcIlSSaTVS3olXke1te7M4WjBLsYNbZKyaapi+ZlG+Jw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div id="swagger-ui"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.0.0-rc.2/swagger-ui-standalone-preset.js" integrity="sha512-tgEpXQXe1VQOK8Yu3LRVgaxH1tDpBv0gxC6/tZcDQSiTARrLZz/AwM9kZM7VWkab/8CEv47ZpAh4eHUJfxcYHA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.0.0-rc.2/swagger-ui-bundle.min.js" integrity="sha512-1eARbG7Ee0nVhImCo8whsEKPYH6wUBlvdaF4ptkPLZiXfUxT7gunEYnZfDjaBczdR3YEferIqB0LQ3BqOdgJHw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    window.onload = function () {
        const ui = SwaggerUIBundle({
            spec: <?= json_encode($spec) ?>,
        dom_id: '#swagger-ui',
            deepLinking: true,
            supportedSubmitMethods: [],
            presets: [
            SwaggerUIBundle.presets.apis,
        ],
            plugins: [
            SwaggerUIBundle.plugins.DownloadUrl
        ],
    })
        window.ui = ui
    }
</script>
</body>
</html>