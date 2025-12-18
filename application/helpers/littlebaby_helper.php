<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$crlf=chr(13).chr(10);
$itime=2;  // minimum number of seconds between one-visitor visits
$imaxvisit=15000;  // maximum visits in $itime x $imaxvisits seconds
$ipenalty=60; // seconds for waiting
$iplogdir="./log/";
$iplogfile="AttackersIPs.Log";

// Time
$today = date("Y-m-j,G");
$min = date("i");
$sec = date("s");
$r = substr(date("i"),0,1);
$m =  substr(date("i"),1,1);
$minute = 0;

// Set ur admin's email address and others as u like
$to      = 'hanafi14@gmail.com';   //ur admin's email address
$headers = 'From: Little Lady Baby@yehg.net' . "\r\n" .   //  change as ur wish 	   
           'X-Mailer: yehg.net DDoS Attack Shield';
$subject = "Warning of Possible DoS Attack @ $today:$min:$sec";

// Warning Messages:
$message5=' Your site got attacking or bot like visiting from IP address: '.$_SERVER["REMOTE_ADDR"];

//---------------------- End of Initialization ---------------------------------------  
// Get file time:
$ipfile=substr(md5($_SERVER["REMOTE_ADDR"]),-3);  // -3 means 4096 possible files
$oldtime=0;
if (file_exists($iplogdir.$ipfile)) $oldtime=filemtime($iplogdir.$ipfile);

// Update times:
$time=time();
if ($oldtime<$time) $oldtime=$time;
$newtime=$oldtime+$itime;

// Check human or bot:
if ($newtime>=$time+$itime*$imaxvisit)
{
    // To block visitor:
    touch($iplogdir.$ipfile,$time+$itime*($imaxvisit-1)+$ipenalty);
    header("HTTP/1.0 503 Service Temporarily Unavailable");
    header("Connection: close");
    header("Content-Type: text/html");
    
    // Professional HTML Output
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Temporarily Limited</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .shield-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .shield-icon::after {
            content: "🛡️";
            font-size: 40px;
            filter: brightness(0) invert(1);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 25px;
        }

        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .info-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .info-card p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        .countdown {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .countdown h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }

        .timer {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
            font-family: "Courier New", monospace;
        }

        .suggestions {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }

        .suggestions h3 {
            color: #2e7d32;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .suggestions ul {
            list-style: none;
            padding: 0;
        }

        .suggestions li {
            padding: 8px 0;
            color: #2e7d32;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .suggestions li::before {
            content: "✓";
            background: #4caf50;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
        }

        .refresh-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            margin-top: 20px;
            transition: transform 0.2s;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="shield-icon"></div>
        
        <h1>Service Temporarily Limited</h1>
        
        <div class="status-badge">High Traffic Detected</div>
        
        <div class="message">
            We have detected unusually high traffic to our servers. To ensure optimal performance for all users, 
            we have temporarily implemented traffic management measures.
        </div>

        <div class="countdown">
            <h3>Please wait for:</h3>
            <div class="timer" id="countdown">'.$ipenalty.'</div>
            <p>seconds before trying again</p>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <h3>🚀 What\'s Happening?</h3>
                <p>Our protection system has detected high traffic volume and is managing server load to maintain service quality.</p>
            </div>
            
            <div class="info-card">
                <h3>⚡ Why This Happens</h3>
                <p>This is a normal security measure that helps protect our servers from overload and ensures fair access for everyone.</p>
            </div>
        </div>

        <div class="suggestions">
            <h3>💡 What You Can Do</h3>
            <ul>
                <li>Wait for the countdown timer to complete</li>
                <li>Refresh the page after the timer reaches zero</li>
                <li>Try again in a few minutes if the issue persists</li>
                <li>Clear your browser cache if you continue experiencing issues</li>
                <li>Contact support if problems continue</li>
            </ul>
        </div>

        <button class="refresh-btn" onclick="location.reload()" id="refreshBtn" disabled>
            Refresh Page
        </button>

        <div class="footer">
            <p>Protected by Advanced Traffic Management System</p>
            <p>Your IP: <strong>'.$_SERVER["REMOTE_ADDR"].'</strong></p>
            <p>Time: '.date("D, d M Y H:i:s").'</p>
        </div>
    </div>

    <script>
        // Countdown timer
        let timeLeft = '.$ipenalty.';
        const countdownElement = document.getElementById("countdown");
        const refreshBtn = document.getElementById("refreshBtn");
        
        const timer = setInterval(function() {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownElement.textContent = "0";
                refreshBtn.disabled = false;
                refreshBtn.textContent = "Try Again Now";
                refreshBtn.style.background = "linear-gradient(135deg, #4caf50, #45a049)";
            }
        }, 1000);

        // Auto-refresh after countdown
        setTimeout(function() {
            location.reload();
        }, '.$ipenalty.' * 1000);
    </script>
</body>
</html>';
    
    // Mailing Warning Message to Site Admin
    @mail($to, $subject, $message5, $headers);	
    
    // logging:
    $fp=@fopen($iplogdir.$iplogfile,"a");
    
    if ($fp!==FALSE)
    {
        $useragent='<unknown user agent>';
        if (isset($_SERVER["HTTP_USER_AGENT"])) $useragent=$_SERVER["HTTP_USER_AGENT"];
        @fputs($fp,$_SERVER["REMOTE_ADDR"].' on '.date("D, d M Y, H:i:s").' as '.$useragent.$crlf);
    }
    
    @fclose($fp);
    exit();
}

// Modify file time:
touch($iplogdir.$ipfile,$newtime);

/* End of file Littlebaby_helper.php */
/* Location: ./application/helpers/littlebaby_helper.php */
?>