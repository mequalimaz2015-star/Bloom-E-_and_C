<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloom Africa | Select Your Destination</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="uploads/gallery/BLOOM.jpg?v=1.0" type="image/jpeg">
    <link rel="shortcut icon" href="uploads/gallery/BLOOM.jpg?v=1.0" type="image/jpeg">
    <style>
        :root {
            --primary: #dfb180;
            --construction-accent: #f39c12;
            --restaurant-accent: #dfb180;
            --font-main: 'Outfit', sans-serif;
            --font-heading: 'Playfair Display', serif;
            --bg-deep: #050a0f;
            /* Attractive deep blue instead of black */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background: var(--bg-deep);
            color: #fff;
            height: 100vh;
            overflow: hidden;
            display: flex;
            position: relative;
        }

        /* Animated Circles Background */
        .circles-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .circle {
            position: absolute;
            background: radial-gradient(circle, rgba(223, 177, 128, 0.15) 0%, rgba(223, 177, 128, 0) 70%);
            border-radius: 50%;
            filter: blur(40px);
            animation: floatCircle 15s infinite ease-in-out;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            left: 10%;
            animation-delay: 0s;
        }

        .circle-2 {
            width: 300px;
            height: 300px;
            bottom: 10%;
            right: 20%;
            background: radial-gradient(circle, rgba(243, 156, 18, 0.1) 0%, rgba(243, 156, 18, 0) 70%);
            animation-delay: -5s;
        }

        .circle-3 {
            width: 500px;
            height: 500px;
            top: 30%;
            left: 40%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
            animation-delay: -10s;
        }

        @keyframes floatCircle {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, 50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .split-container {
            display: flex;
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 1;
        }

        .split-half {
            flex: 1;
            position: relative;
            overflow: hidden;
            transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
        }

        .split-half:hover {
            flex: 1.5;
        }

        /* Video Background Styling */
        .video-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: translate(-50%, -50%) scale(1.1);
            z-index: 1;
            transition: transform 1.2s cubic-bezier(0.23, 1, 0.32, 1);
            filter: grayscale(10%) brightness(0.6);
        }

        .split-half:hover .video-bg {
            transform: translate(-50%, -50%) scale(1);
            filter: grayscale(0%) brightness(0.85);
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(5, 10, 15, 0.3) 0%, rgba(5, 10, 15, 0.7) 100%);
            z-index: 2;
            transition: opacity 0.6s ease;
        }

        .split-half:hover .overlay {
            background: linear-gradient(180deg, rgba(5, 10, 15, 0.1) 0%, rgba(5, 10, 15, 0.5) 100%);
        }

        .content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 60px;
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .content i {
            font-size: 5rem;
            margin-bottom: 25px;
            display: block;
            transition: 0.5s;
        }

        .restaurant-side .content i {
            color: var(--restaurant-accent);
            text-shadow: 0 0 30px rgba(223, 177, 128, 0.4);
        }

        .construction-side .content i {
            color: var(--construction-accent);
            text-shadow: 0 0 30px rgba(243, 156, 18, 0.4);
        }

        .split-half:hover .content i {
            transform: translateY(-10px) rotate(5deg) scale(1.1);
        }

        .content h2 {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            margin-bottom: 20px;
            letter-spacing: 5px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .content p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
            max-width: 420px;
            margin: 0 auto 40px;
            line-height: 1.6;
            letter-spacing: 1px;
        }

        .split-half:hover .content p {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.2s;
        }

        .btn-enter {
            display: inline-block;
            padding: 16px 45px;
            border-radius: 50px;
            /* Circular feel for buttons too */
            text-decoration: none;
            font-weight: 800;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            transition: all 0.4s ease;
            opacity: 0;
            transform: translateY(20px);
            border: 2px solid #fff;
        }

        .split-half:hover .btn-enter {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.4s;
        }

        .restaurant-side .btn-enter {
            color: var(--restaurant-accent);
            border-color: var(--restaurant-accent);
        }

        .restaurant-side .btn-enter:hover {
            background: var(--restaurant-accent);
            color: #000;
            box-shadow: 0 0 30px rgba(223, 177, 128, 0.5);
        }

        .construction-side .btn-enter {
            color: var(--construction-accent);
            border-color: var(--construction-accent);
        }

        .construction-side .btn-enter:hover {
            background: var(--construction-accent);
            color: #000;
            box-shadow: 0 0 30px rgba(243, 156, 18, 0.5);
        }

        /* Center Branding */
        .center-brand {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 50;
            text-align: center;
            pointer-events: none;
        }

        .logo-box {
            background: rgba(5, 10, 15, 0.6);
            padding: 25px 50px;
            border-radius: 60px;
            /* Circular box */
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            position: relative;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.5);
        }

        .logo-box::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border: 1px solid var(--primary);
            opacity: 0.3;
            z-index: -1;
        }

        .logo-box h1 {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            color: #fff;
            letter-spacing: 8px;
            margin-bottom: 5px;
            white-space: nowrap;
        }

        .logo-box span {
            font-size: 0.7rem;
            color: var(--primary);
            letter-spacing: 12px;
            text-transform: uppercase;
            font-weight: 300;
        }

        /* Divider Line */
        .divider {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 1px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 40;
            transition: transform 0.8s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .restaurant-side:hover~.divider {
            transform: translateX(25%) scaleY(1.2);
        }

        @media(max-width: 991px) {
            .split-container {
                flex-direction: column;
            }

            .logo-box h1 {
                font-size: 1.6rem;
                letter-spacing: 4px;
            }

            .content p {
                display: none;
            }

            .btn-enter {
                opacity: 1;
                transform: translateY(0);
            }

            .center-brand {
                width: 90%;
            }

            .logo-box {
                padding: 15px;
            }

            .divider {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="circles-bg">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
    </div>
    <div class="center-brand">
        <div class="logo-box">
            <h1>BLOOM AFRICA</h1>
            <span>GROUP OF COMPANIES</span>
        </div>
    </div>
    <div class="split-container">
        <!-- Restaurant Side -->
        <div class="split-half restaurant-side" onclick="window.location.href='restaurant_home.php'">
            <video autoplay muted loop playsinline class="video-bg">
                <source
                    src="https://player.vimeo.com/external/494163956.sd.mp4?s=62ef7655079a407f872e4240751a02183147814b&profile_id=165"
                    type="video/mp4">
            </video>
            <div class="overlay"></div>
            <div class="content">
                <i class="fa-solid fa-utensils"></i>
                <h2>Restaurant</h2>
                <p>A culinary journey through authentic African flavors, refined for the modern palate.</p>
                <a href="restaurant_home.php" class="btn-enter" onclick="event.stopPropagation();">Enter Dining</a>
            </div>
        </div>
        <!-- Construction Side -->
        <div class="split-half construction-side" onclick="window.location.href='Construction/index.php'">
            <video autoplay muted loop playsinline class="video-bg">
                <source
                    src="https://player.vimeo.com/external/511342676.sd.mp4?s=0954b92d6e32d56a236052be9a4060855219277d&profile_id=165"
                    type="video/mp4">
            </video>
            <div class="overlay"></div>
            <div class="content">
                <i class="fa-solid fa-hard-hat"></i>
                <h2>Construction</h2>
                <p>Engineering excellence and sustainable infrastructure for a brighter tomorrow.</p>
                <a href="Construction/index.php" class="btn-enter" onclick="event.stopPropagation();">Enter Projects</a>
            </div>
        </div>
    </div>

</body>

</html>