<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Verification</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 20px;
    }
    .email-container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .header {
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #333;
    }
    .otp {
      font-size: 20px;
      font-weight: bold;
      color: #ff6f61;
      text-align: center;
      margin: 20px 0;
    }
    .message {
      font-size: 16px;
      line-height: 1.5;
      color: #555;
    }
    .footer {
      text-align: center;
      font-size: 14px;
      color: #aaa;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">Your OTP Code</div>
    <div class="message">
      Dear [User's Name],<br><br>
      To complete your verification process, please use the following OTP code:
    </div>
    <div class="otp">{{ $otp }}</div>
    <div class="message">
      This OTP is valid for 10 minutes. If you did not request this code, please ignore this email.
    </div>
    <div class="footer">
      Thank you for choosing our service.<br>
      The [Your Company Name] Team
    </div>
  </div>
</body>
</html>
