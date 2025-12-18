<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="<?php echo title; ?>">
		<meta name="author" content="<?php echo title; ?>">
		<link rel="icon" href="<?php echo base_url(); ?>favicon.png">
			<title>404 | <?php echo title; ?></title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #777677, #081da6);
            color: white;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        h1 {
            font-size: 4rem;
            margin: 0;
        }
        p {
            font-size: 1.2rem;
            margin: 15px 0 30px;
        }
        a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #081da6;
            color: white;
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        a:hover {
            background-color: #ff4f70;
            transform: translateY(-2px);
        }
        .animation {
            font-size: 8rem;
            margin-bottom: 20px;
            animation: float 3s infinite ease-in-out;
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
    </style>
    
    <?php include_once('analytics.php'); ?>
    
</head>
<body>
    <div class="container">
        <div class="animation">🚀</div>
        <h1>404</h1>
        <p>Oops! The page you're looking for doesn't exist.</p>
        <a href="<?php echo base_url_site; ?>">Go Back Home</a>
    </div>
</body>
</html>
