<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FlightPHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .hero {
            text-align: center;
            padding: 50px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .message {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Welcome to FlightPHP</h1>
        <p>A fast, simple, and extensible PHP framework</p>
    </div>

    <div class="message">
        <h2><?php echo htmlspecialchars($message ?? 'Hello World!'); ?></h2>
        <p>Congratulations! Your FlightPHP application is running successfully.</p>
        <p>You can now:</p>
        <ul>
            <li>Customize this welcome page in <code>bootstrap/views/welcome.php</code></li>
            <li>Add new routes in <code>bootstrap/config/routes.php</code></li>
            <li>Create controllers in <code>app/controllers/</code></li>
            <li>Configure your application in <code>bootstrap/config/config.php</code></li>
        </ul>
    </div>

    <div style="text-align: center; margin-top: 40px; color: #666;">
        <p>Need help? Check out the <a href="https://flightphp.com" target="_blank">FlightPHP documentation</a></p>
    </div>
</body>
</html>