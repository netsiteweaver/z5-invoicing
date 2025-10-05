<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 Not Found</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/4b851d0dd9.js" crossorigin="anonymous"></script>
<style>
  body {
    margin: 0;
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    overflow: hidden;
  }

  .container {
    text-align: center;
    max-width: 700px;
    padding: 20px;
    position: relative;
  }

  .illustration {
    max-width: 100%;
    height: auto;
    margin-bottom: 30px;
    animation: float 4s ease-in-out infinite;
  }

  @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
    100% { transform: translateY(0px); }
  }

  h1 {
    font-size: 96px;
    margin: 0;
    color: #c93634;
  }

  p {
    font-size: 22px;
    margin: 15px 0 30px;
  }

  a.button {
    display: inline-block;
    padding: 14px 32px;
    background-color: #c93634;
    color: #fff;
    font-weight: bold;
    text-decoration: none;
    border-radius: 3px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgb(196, 99, 97);
  }

  a.button:hover {
    background-color:rgb(116, 20, 19);
    transform: translateY(5px);
    box-shadow: 0 8px 20px rgb(196, 99, 97);
  }

  /* Background floating circles */
  .circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.2;
    background-color: #c93634;
    animation: drift 20s linear infinite;
  }

  .circle:nth-child(1) { width: 80px; height: 80px; top: 10%; left: 5%; animation-delay: 0s; }
  .circle:nth-child(2) { width: 50px; height: 50px; top: 60%; left: 15%; animation-delay: 5s; }
  .circle:nth-child(3) { width: 100px; height: 100px; top: 30%; left: 80%; animation-delay: 10s; }

  @keyframes drift {
    0% { transform: translateY(0) translateX(0); }
    50% { transform: translateY(-50px) translateX(30px); }
    100% { transform: translateY(0) translateX(0); }
  }

  @media (max-width: 600px) {
    h1 { font-size: 72px; }
    p { font-size: 18px; }
  }
</style>
</head>
<body>
  <div class="circle"></div>
  <div class="circle"></div>
  <div class="circle"></div>

  <div class="container">
    <img class="illustration" src="{{ asset('images/page-not-found.png') }}" alt="Not Found">
    <h1>404</h1>
    <p>Oops! The page you’re looking for doesn’t exist or has been moved.</p>
    <a href="/" class="button"><i class="fas fa-home"></i> Go Home</a>
  </div>
</body>
</html>
