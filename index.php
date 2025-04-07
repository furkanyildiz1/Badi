<?php

include 'nedmin/netting/baglan.php';
include 'header.php';
$veri_stmt = $db->prepare("SELECT * FROM anasayfa_veri WHERE id=:id");
$veri_stmt->execute([
    'id' => 1
]);
$anasayfa_veriler = $veri_stmt->fetch(PDO::FETCH_ASSOC);

// Format numbers to display k, M, B suffix
function formatNumber($num)
{
    if ($num < 1000) {
        // Return the number as is if less than 1000
        return $num;
    } else if ($num < 1000000) {
        // Convert to K for thousands (1K, 2K, etc.)
        $num = round($num / 1000, 1);
        // Remove decimal if it's .0
        return $num == intval($num) ? intval($num) . 'k' : $num . 'k';
    } else if ($num < 1000000000) {
        // Convert to M for millions (1M, 2M, etc.)
        $num = round($num / 1000000, 1);
        // Remove decimal if it's .0
        return $num == intval($num) ? intval($num) . 'M' : $num . 'M';
    } else {
        // Convert to B for billions (1B, 2B, etc.)
        $num = round($num / 1000000000, 1);
        // Remove decimal if it's .0
        return $num == intval($num) ? intval($num) . 'B' : $num . 'B';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badi Akademi</title>

    <!-- Önbelleğe alma ve önyükleme direktifleri -->
    <link rel="preload" href="assets/img/banner.webp" as="image" type="image/webp">
    <link rel="preconnect" href="/assets/img/" crossorigin>
    <link rel="preload" href="assets/fonts/boxicons.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/roboto/v15/rP2Yp2ywxg3V9EV7wKX9.woff2" as="font"
        type="font/woff2" crossorigin>

    <!-- Tarayıcı önbelleğe alma talimatları -->
    <meta http-equiv="Cache-Control" content="max-age=31536000">

    <!-- Kritik CSS -->
    <style>
        /* Temel Stil ve Reset */
        :root {
            --primary-color: #2bc7f8;
            --secondary-color: #333;
            --text-color: #666;
            --white-color: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-color);
            overflow-x: hidden;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        /* Font Tanımlamaları */
        @font-face {
            font-family: 'BoxIcons';
            src: url('assets/fonts/boxicons.woff2') format('woff2');
            font-display: swap;
        }

        @font-face {
            font-family: 'Roboto';
            src: url('https://fonts.gstatic.com/s/roboto/v15/rP2Yp2ywxg3V9EV7wKX9.woff2') format('woff2');
            font-display: swap;
        }

        /* Layout ve Grid */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-xl-8,
        .col-lg-7,
        .col-md-12 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }

        @media (min-width: 768px) {
            .col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        @media (min-width: 992px) {
            .col-lg-7 {
                flex: 0 0 58.333333%;
                max-width: 58.333333%;
            }
        }

        @media (min-width: 1200px) {
            .col-xl-8 {
                flex: 0 0 66.666667%;
                max-width: 66.666667%;
            }
        }

        /* Header ve Navbar */
        .navbar-area {
            background-color: var(--white-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            transition: all 0.3s ease;
        }

        .edu-navbar-area2 {
            background: transparent;
            position: absolute;
        }

        .navbar-brand img {
            max-height: 45px;
        }

        .edumim-nav .navbar .navbar-nav .nav-item {
            position: relative;
            margin-left: 15px;
            margin-right: 15px;
        }

        .edumim-nav .navbar .navbar-nav .nav-item a {
            font-size: 16px;
            font-weight: 500;
            padding: 30px 0;
            color: var(--secondary-color);
            transition: all 0.5s;
        }

        /* Banner Stilleri */
        .home-banner {
            padding-bottom: 50px;
            position: relative;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .banner-content {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .banner-content-inner {
            position: relative;
            z-index: 2;
        }

        .subtitle {
            display: block;
            font-size: 18px;
            font-weight: 500;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .title {
            font-size: 42px;
            line-height: 1.2;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }

        .title span {
            color: var(--primary-color);
        }

        .banner-content-inner p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Butonlar */
        .bbtns {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .bg_btn,
        .wborder_btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            margin-right: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .bg_btn {
            background-color: var(--primary-color);
            color: var(--white-color);
            border: 2px solid var(--primary-color);
        }

        .wborder_btn {
            background-color: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .bg_btn:hover {
            background-color: #1ab4e5;
            border-color: #1ab4e5;
        }

        .wborder_btn:hover {
            background-color: var(--primary-color);
            color: var(--white-color);
        }

        /* Banner SVG ve Görseller */
        .banner_img {
            position: relative;
            z-index: 1;
        }

        .banner_img_inner {
            display: flex;
            justify-content: center;
            position: relative;
        }

        .banner_img_inner img,
        .banner_img_inner svg {
            width: 100%;
            height: auto;
            max-width: 800px;
            aspect-ratio: 16/9;
            object-fit: cover;
            will-change: transform;
            contain: layout paint;
        }

        .tpcourse__thumb {
            position: relative;
            width: 100%;
            padding-top: 66.67%;
            overflow: hidden;
        }

        .tpcourse__thumb img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /* SVG Animasyonları */
        .moeffect {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .eitem {
            position: relative;
            transition: all 0.5s ease;
        }

        /* Badge Stilleri */
        .total_students_badge,
        .total_course_badge {
            position: absolute;
            background-color: var(--white-color);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
        }

        .total_students_badge {
            top: 20%;
            right: 8%;
        }

        .total_course_badge {
            bottom: 15%;
            left: 10%;
        }

        .total_students_badge h4,
        .total_course_badge h4 {
            margin: 10px 0 5px;
            font-size: 22px;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .total_students_badge span,
        .total_course_badge span {
            font-size: 14px;
            color: var(--text-color);
        }

        .total_students_badge .icon,
        .total_course_badge .icon {
            margin-bottom: 5px;
        }

        /* Şekil ve Arka Plan Elementleri */
        .bshape1,
        .bshape2,
        .bshape3,
        .bshape4 {
            position: absolute;
            z-index: -1;
            opacity: 0.5;
        }

        .bshape1 {
            top: 10%;
            right: 10%;
        }

        .bshape2 {
            bottom: 20%;
            left: 5%;
        }

        .bshape3 {
            top: 30%;
            left: 15%;
        }

        .bshape4 {
            bottom: 10%;
            right: 15%;
        }

        .tpshape,
        .btmshape {
            position: absolute;
            z-index: -1;
        }

        /* Responsive Stiller - Mobil için */
        @media only screen and (max-width: 991px) {
            .navbar-area {
                padding: 10px 0;
            }

            .home-banner {
                padding-top: 100px;
            }

            .title {
                font-size: 32px;
            }

            .banner_img {
                margin-top: 30px;
            }

            .total_students_badge,
            .total_course_badge {
                position: relative;
                top: auto;
                right: auto;
                bottom: auto;
                left: auto;
                margin: 15px auto;
                max-width: 200px;
            }
        }

        @media only screen and (max-width: 767px) {
            .banner_img .banner_img_inner svg {
                width: 100%;
                height: auto;
            }

            .bbtns {
                flex-direction: column;
                align-items: flex-start;
            }

            .bg_btn,
            .wborder_btn {
                width: 100%;
                margin-right: 0;
                text-align: center;
            }

            .edu-banner-content h1 {
                font-size: 28px;
            }

            .ptb-100 {
                padding-top: 60px;
                padding-bottom: 60px;
            }

            .pt-100 {
                padding-top: 60px;
            }

            .pb-100 {
                padding-bottom: 60px;
            }
        }
    </style>

    <!-- Kritik olmayan CSS dosyalarını asenkron yükleme -->
    <link rel="preload" href="assets/css/style-deferred.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="assets/css/responsive-deferred.css" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="assets/css/style-deferred.css">
        <link rel="stylesheet" href="assets/css/responsive-deferred.css">
    </noscript>

    <!-- Diğer CSS dosyaları için asenkron yükleme -->
    <link rel="preload" href="assets/css/boxicons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="assets/css/boxicons.min.css">
    </noscript>
</head>

<body>

    <head>
        <!-- Banner resmi için önbelleğe alma ve önyükleme direktifleri -->
        <link rel="preload" href="assets/img/banner.webp" as="image" type="image/webp">
        <link rel="preconnect" href="/assets/img/" crossorigin>

        <!-- Tarayıcı önbelleğe alma talimatları -->
        <meta http-equiv="Cache-Control" content="max-age=31536000">

        <!-- Mevcut font preload'ları -->
        <link rel="preload" href="assets/fonts/boxicons.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="https://fonts.gstatic.com/s/roboto/v15/rP2Yp2ywxg3V9EV7wKX9.woff2" as="font"
            type="font/woff2" crossorigin>

        <!-- Diğer stil tanımlamaları -->
        <style>
            /* Banner resmi için lazy loading ve boyut optimizasyonları */
            .banner_img_inner img {
                width: 100%;
                height: auto;
                max-width: 800px;
                aspect-ratio: 16/9;
                object-fit: cover;
                will-change: transform;
                contain: layout paint;
            }

            /* Mevcut font-face tanımlamaları */
            @font-face {
                font-family: 'BoxIcons';
                src: url('assets/fonts/boxicons.woff2') format('woff2');
                font-display: swap;
            }

            @font-face {
                font-family: 'Roboto';
                src: url('https://fonts.gstatic.com/s/roboto/v15/rP2Yp2ywxg3V9EV7wKX9.woff2') format('woff2');
                font-display: swap;
            }

            .tpcourse__thumb {
                position: relative;
                width: 100%;
                padding-top: 66.67%;
                /* 3:2 en-boy oranı için */
                overflow: hidden;
            }

            .tpcourse__thumb img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
            }

            .edu-about-image {
                position: relative;
                width: 100%;
                padding-top: 100%;
                /* 1:1 en-boy oranı için */
                overflow: hidden;
            }

            .edu-about-image img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
            }
        </style>
    </head>

    <!-- Start Banner Area -->

    <!-- Start Home Banner -->
    <section class="home-banner">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-7 col-md-12">
                    <div class="banner-content d-flex align-items-center">
                        <div class="banner-content-inner">
                            <span class="subtitle">Çevrimiçi ve Yüz yüze Eğitimler</span>
                            <h2 class="title"><span>Gelecek</span> Eğitime İnanarak İnşa Edilir</h2>
                            <p>
                                Uzun zamandır bilinen bir gerçektir ki okuyucular, <br>
                                dikkatlerini çeken içerikler ile meşgul olur.
                            </p>

                            <div class="hero-left-bottom" style="display: flex;flex-direction: column;">
                                <div class="bbtns">
                                    <a href="kurslar_1.php" class="bg_btn bt">Tüm Kurslar</a>
                                    <a href="contact.php" class="wborder_btn bt">İletişim</a>
                                </div>

                                <div class="sinfo">
                                    <img src="assets/img/rev-img.webp" alt="Mutlu öğrenci görseli" width="100"
                                        height="104" loading="lazy" decoding="async"
                                        style="width: 100%; height: auto; max-width: 100px; aspect-ratio: 245/104;"
                                        srcset="assets/img/rev-img.webp 245w" sizes="(max-width: 768px) 163px, 245px" />
                                    <span><?php echo formatNumber($anasayfa_veriler['mutlu_ogrenci']); ?>+ Mutlu
                                        Öğrenci</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="banner_img">
                <div class="banner_img_inner moeffect">
                    <!-- Eğitim temalı SVG illüstrasyon -->
                    <svg width="800" height="600" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Arka plan daire -->
                        <circle cx="400" cy="300" r="250" fill="#2bc7f8" fill-opacity="0.1" />

                        <!-- Kitap simgesi -->
                        <path d="M300 250 h200 v30 h-200 v-30" fill="#2bc7f8" stroke="#333" stroke-width="2" />
                        <path d="M300 280 q100 20 200 0" fill="none" stroke="#333" stroke-width="2" />

                        <!-- Mezuniyet şapkası -->
                        <path d="M350 200 l50-30 l50 30 l-50 30 z" fill="#2bc7f8" />
                        <path d="M400 200 v40" stroke="#333" stroke-width="2" />
                        <path d="M380 240 h40" fill="none" stroke="#333" stroke-width="2" />

                        <!-- Kalem -->
                        <path d="M450 350 l30-30 l10 10 l-30 30 z" fill="#2bc7f8" />
                        <path d="M460 360 l-20 20" stroke="#333" stroke-width="2" />

                        <!-- Laptop -->
                        <rect x="320" y="320" width="120" height="80" rx="5" fill="#2bc7f8" />
                        <rect x="320" y="320" width="120" height="60" rx="5" fill="#fff" />
                        <path d="M300 400 h160" stroke="#333" stroke-width="2" />

                        <!-- Formüller ve semboller -->
                        <text x="340" y="350" font-family="Arial" font-size="12" fill="#333">
                            <tspan x="340" dy="0">f(x) = ax² + bx + c</tspan>
                            <tspan x="340" dy="20">E = mc²</tspan>
                        </text>

                        <!-- Dekoratif elementler -->
                        <circle cx="250" cy="200" r="10" fill="#2bc7f8" />
                        <circle cx="550" cy="400" r="15" fill="#2bc7f8" />
                        <circle cx="450" cy="150" r="8" fill="#2bc7f8" />

                        <!-- Bağlantı çizgileri -->
                        <path d="M250 200 q50 -50 100 0" stroke="#2bc7f8" stroke-width="2" fill="none" />
                        <path d="M550 400 q-50 50 -100 0" stroke="#2bc7f8" stroke-width="2" fill="none" />

                        <!-- Dalga efekti -->
                        <path d="M200 500 q100 -30 200 0 t200 0" stroke="#2bc7f8" stroke-width="2" fill="none"
                            stroke-opacity="0.5" />
                        <path d="M200 520 q100 -30 200 0 t200 0" stroke="#2bc7f8" stroke-width="2" fill="none"
                            stroke-opacity="0.3" />

                        <!-- Parıltı efektleri -->
                        <circle cx="300" cy="150" r="3" fill="#2bc7f8" fill-opacity="0.6" />
                        <circle cx="500" cy="250" r="2" fill="#2bc7f8" fill-opacity="0.6" />
                        <circle cx="350" cy="450" r="4" fill="#2bc7f8" fill-opacity="0.6" />
                    </svg>

                    <!-- Mevcut badge'ler -->
                    <div class="total_students_badge eitem" value="1">
                        <div class="icon">
                            <svg fill="none" viewBox="0 0 31 35" width="31" height="35">
                                <path fill="#fff"
                                    d="M7.684 4.17C3.583 6.46.18 8.4.117 8.483c-.157.192-.157.417.007.616.068.088.889.512 1.893.984l1.771.827.02.834.021.834h-.383c-.314 0-.41.027-.547.171l-.17.164v5.346l.17.164c.164.17.178.17 1.47.17s1.306 0 1.47-.17l.17-.164v-5.346l-.17-.164c-.137-.144-.233-.17-.547-.17h-.376v-.548c0-.3.02-.547.048-.547.034 0 .95.417 2.05.937 1.97.916 1.997.93 2.12 1.21.068.15.225.41.348.567.26.349.28.5.062.5-.089 0-.315.081-.513.177-.93.465-1.367 1.64-1.012 2.693.247.718 1.101 1.367 1.791 1.367.205 0 .233.035.431.452.3.642.738 1.23 1.306 1.75l.478.45V22.703l-.178-.137c-.369-.294-.519-.218-1.455.704-.862.841-.862.841-1.245.89-1.018.109-2.645.785-3.575 1.49a8.61 8.61 0 00-3.083 4.593c-.24.896-.307 1.607-.307 3.104 0 1.312 0 1.319.17 1.483l.164.171h25.58l.178-.178.171-.17-.034-1.69c-.027-1.49-.055-1.777-.205-2.412-.403-1.689-1.169-3.035-2.42-4.239a7.671 7.671 0 00-2.16-1.497c-.65-.307-1.607-.594-2.16-.656-.383-.048-.383-.048-1.244-.889-.937-.922-1.087-.997-1.457-.704l-.177.137v-1.114l.478-.451a5.583 5.583 0 001.306-1.75c.198-.417.226-.451.43-.451.691 0 1.546-.65 1.792-1.368.355-1.052-.082-2.228-1.012-2.693-.198-.096-.424-.178-.513-.178-.218 0-.198-.164.041-.471.11-.137.267-.39.349-.568l.157-.314 2.058-.957c1.134-.527 3.09-1.443 4.347-2.03 1.388-.643 2.352-1.135 2.448-1.238.294-.335.164-.65-.417-.97-.192-.11-2.721-1.525-5.62-3.138-2.891-1.62-6.097-3.405-7.122-3.972C16.29.465 15.381 0 15.293 0c-.082.007-3.507 1.88-7.609 4.17zm14.41.786c3.706 2.065 6.72 3.78 6.693 3.808-.102.095-6.802 3.199-6.836 3.165-.027-.02-.061-1.019-.082-2.215l-.034-2.174-.185-.232c-.355-.479-1.285-.841-2.727-1.08-3.958-.657-9.058-.11-9.953 1.066l-.192.246-.034 2.174c-.02 1.196-.055 2.194-.075 2.215-.02.02-.875-.356-1.9-.834l-1.853-.862.014-1.64.02-1.64 5.161-2.886c2.837-1.592 5.182-2.884 5.202-2.878.02 0 3.077 1.696 6.782 3.767zM17.87 7.171c1.142.137 2.536.485 2.823.71.055.049.082.343.082 1.04v.977l-.526-.17c-1.71-.54-5.428-.732-7.916-.397-.848.11-2.133.39-2.365.513-.123.068-.13.04-.13-.91 0-.71.027-1.004.089-1.052.314-.253 1.688-.588 2.884-.704.417-.034.875-.082 1.026-.096.485-.048 3.397.02 4.033.089zM3.808 9.153l-.02.54-.957-.437c-.526-.246-.978-.465-1.005-.492-.027-.028.403-.301.957-.609l1.005-.553.02.499c.014.28.014.752 0 1.052zm14.377 1.265c1.538.212 2.474.52 2.597.84.178.459-.082 1.628-.499 2.25l-.157.24-1.812-.021-1.811-.02-.465-.22c-.766-.362-1.374-1.12-1.518-1.886-.075-.383-.273-.595-.553-.595-.342 0-.575.253-.575.608 0 .445-.26 1.026-.628 1.415-.458.486-.875.656-1.662.698l-.615.027-.157-.246a2.956 2.956 0 01-.52-1.695c-.006-.459.02-.623.103-.705.267-.273 1.846-.635 3.343-.786.93-.089 4.06-.027 4.928.096zm-2.878 3.938c.806.41.936.43 2.795.464l1.73.028-.034 1.476c-.034 1.593-.082 1.86-.445 2.632-.567 1.203-1.709 2.126-3.021 2.447-.485.117-1.566.117-2.05 0a4.587 4.587 0 01-2.981-2.365c-.404-.813-.451-1.08-.486-2.72l-.034-1.484h.349c1.06 0 2.249-.608 2.741-1.408l.13-.205.506.492c.273.267.636.56.8.643zm-10.391 1.23V17.5H3.822v-3.828h1.094v1.914zm4.799 1.21c.013.56.02 1.025.013 1.039-.04.048-.478-.267-.567-.417a1.231 1.231 0 01-.137-.902c.069-.26.451-.725.595-.725.041 0 .082.41.096 1.005zm11.573-.78c.376.377.444.95.164 1.402-.089.15-.526.465-.567.417-.055-.075.054-2.044.109-2.044.041 0 .17.103.294.226zm-6.61 6.55a5.644 5.644 0 002.283-.212c.24-.076.451-.137.485-.137.027 0 .048.362.048.806v.8L16.4 24.917l-1.093 1.094-1.094-1.094-1.094-1.094V22.176l.465.15c.253.089.745.192 1.094.24zm-1.662 2.727l1.504 1.504-.43.424-.424.43-1.518-1.517-1.524-1.525.41-.41c.226-.225.424-.41.444-.41.02 0 .711.677 1.539 1.504zm6.563-1.094l.41.41-1.524 1.525-1.518 1.517-.424-.43-.43-.424 1.504-1.504c.827-.827 1.517-1.504 1.538-1.504.02 0 .218.185.444.41zm-8.032 2.905c1.716 1.716 1.907 1.88 2.112 1.88.198 0 .328-.095.937-.697l.71-.697.711.697c.609.602.739.697.937.697.205 0 .396-.17 2.12-1.886l1.893-1.894.383.048c1.312.15 3.055 1.087 4.012 2.146 1.367 1.518 1.976 3.227 1.976 5.53v.978H23.51v-.923c0-.895-.007-.936-.171-1.093a.503.503 0 00-.752 0c-.164.157-.171.198-.171 1.093v.923H8.197v-.923c0-.895-.007-.936-.17-1.093a.502.502 0 00-.753 0c-.164.157-.17.198-.17 1.093v.923H3.254l.04-1.285c.028-1.073.062-1.38.206-1.948.724-2.755 2.741-4.758 5.414-5.373.71-.164.56-.267 2.632 1.805z" />
                            </svg>
                        </div>
                        <h4><?php echo formatNumber($anasayfa_veriler['toplam_ogrenci']); ?></h4>
                        <span>Toplam Öğrenci</span>
                    </div>

                    <div class="total_course_badge align-self-center eitem" value="1.5">
                        <div class="icon">
                            <svg fill="none" viewBox="0 0 37 32">
                                <path fill="#fff"
                                    d="M2.389.481c-.748.24-1.51.932-1.8 1.652l-.162.402-.021 13.19C.392 30.115.37 29.353.78 30.06c.254.424.811.932 1.291 1.15l.403.191h25.548l.388-.183a2.972 2.972 0 001.623-2.104c.035-.204.07-.755.07-1.213v-.84h2.28c2.505 0 2.738-.028 3.268-.417.155-.12.374-.38.494-.585l.204-.375V24.06c0-1.828-.02-1.912-.578-2.449-.523-.508-.55-.508-3.247-.536l-2.42-.021v-1.765c0-2.117-.036-2.25-.629-2.25a.53.53 0 00-.345.161c-.149.163-.156.212-.156 2.012v1.849H23.54c-4.474 0-5.484.014-5.674.099-.127.049-1.045.607-2.04 1.235-1.708 1.072-1.828 1.136-2.16 1.136-.296 0-.373.028-.493.183-.19.248-.19.466.014.7.12.147.247.197.579.24.388.049.578.154 2.286 1.227l1.863 1.179h11.073l-.028 1.002c-.021.811-.05 1.066-.155 1.277-.177.36-.501.678-.854.833-.268.127-.826.134-12.703.134H2.826l-.36-.17a1.657 1.657 0 01-.846-.938c-.078-.219-.092-2.124-.1-10.417V8.64h27.454v1.842c0 1.037.029 1.891.07 1.962.036.07.163.17.276.218.261.106.558-.014.692-.282.113-.226.134-9.083.02-9.669A2.89 2.89 0 0028.488.644l-.465-.226L15.353.404C5.014.397 2.63.41 2.39.48zM27.95 1.646c.353.155.677.473.833.12.247.134.48.155 2.646l.021 2.386H1.521V5.18c0-2.526.007-2.575.402-3.09.099-.135.346-.318.543-.41l.36-.17h12.422c11.877 0 12.435.008 12.703.135zm3.352 22.414v1.87H18.6v-1.82c0-1.002.021-1.85.05-1.87.02-.029 2.879-.05 6.351-.05h6.302v1.87zm3.494-1.771c.423.183.473.367.444 1.877l-.02 1.341-.198.19-.191.198-1.2.021-1.2.021V22.19h1.06c.761 0 1.128.028 1.305.099zm-17.34 2.435l-.022.698-1.08-.67-1.08-.678.516-.324c.282-.184.77-.487 1.08-.685l.564-.353.021.65c.008.36.008.974 0 1.362z" />
                                <path fill="#fff"
                                    d="M4.568 3.022c-.685.226-1.073.755-1.073 1.482 0 .868.65 1.524 1.525 1.524.973 0 1.672-.854 1.496-1.835-.163-.875-1.101-1.44-1.948-1.171zm.734 1.207c.176.183.183.367.014.578-.099.127-.515.113-.621-.014-.141-.17-.106-.473.07-.614.212-.17.325-.162.537.05zM8.754 2.986c-1.22.31-1.644 1.715-.769 2.59.353.36.692.48 1.235.438.558-.042.946-.304 1.214-.826.29-.586.233-1.164-.176-1.665-.353-.424-1.01-.657-1.504-.537zm.607 1.257c.303.402-.261.875-.635.536-.29-.261-.022-.783.352-.692a.548.548 0 01.283.156zM12.757 2.987c-.797.204-1.34 1.086-1.143 1.856.19.72.783 1.185 1.51 1.185.614 0 1.108-.31 1.398-.875.219-.43.162-1.136-.134-1.538-.353-.494-1.066-.77-1.63-.628zm.607 1.178c.177.12.219.501.07.65-.147.148-.528.105-.648-.071-.156-.219-.142-.318.063-.515.198-.205.296-.22.515-.064zM6.617 11.625c-.303.325-.14.826.304.939.099.021.388.028.649.014.381-.021.487-.056.593-.19.183-.226.17-.58-.028-.77-.142-.134-.247-.155-.763-.155-.536 0-.62.014-.755.162zM11.804 11.547c-.19.156-.275.466-.177.678.184.374.015.367 6.218.367h5.78l.205-.205c.254-.254.261-.515.028-.748l-.17-.176h-5.885c-4.595 0-5.907.02-6 .084zM29.277 14.215c-.212.085-.304.29-.304.691 0 .417.198.65.55.65.565 0 .791-.897.318-1.27-.197-.156-.317-.17-.564-.071zM6.617 15.365c-.261.276-.198.692.134.861.296.156 11.546.156 11.842 0a.53.53 0 00.128-.868l-.163-.155H6.765l-.148.162zM22.404 15.274c-.204.12-.303.459-.19.677.141.304.374.389.967.367.466-.02.536-.042.684-.218.22-.255.212-.502-.007-.72-.155-.163-.226-.177-.755-.177-.325 0-.635.036-.699.07zM6.615 19.106a.622.622 0 00-.085.684c.141.269.395.283 4.355.283 4.05 0 4.164-.007 4.283-.318.092-.24.043-.508-.12-.656l-.162-.155H6.763l-.148.162z" />
                            </svg>
                        </div>
                        <div class="tcourse_content">
                            <h4><?php echo formatNumber($anasayfa_veriler['toplam_kurs']); ?></h4>
                            <span>Toplam Kurs</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg_shapes moeffect">
                <div class="bshape1 eitem" value="1">
                    <svg fill="none" viewBox="0 0 188 188">
                        <mask id="a" fill="#fff">
                            <path
                                d="M188 94a94.003 94.003 0 01-75.662 92.194 94.001 94.001 0 1148.13-158.662L94 94h94z" />
                        </mask>
                        <path stroke="#fff" stroke-dasharray="18 18" stroke-opacity=".19" stroke-width="8"
                            d="M188 94a94.003 94.003 0 01-75.662 92.194 94.001 94.001 0 1148.13-158.662L94 94h94z"
                            mask="url(#a)" />
                    </svg>
                </div>

                <div class="bshape2 eitem" value="2.5">
                    <svg fill="none" viewBox="0 0 45 43">
                        <path fill="#fff" fill-opacity=".14"
                            d="M20.598 1.854c.599-1.843 3.205-1.843 3.804 0l3.823 11.766a2 2 0 001.902 1.382H42.5c1.937 0 2.743 2.48 1.175 3.618l-10.008 7.272a2 2 0 00-.727 2.236l3.823 11.766c.599 1.843-1.51 3.375-3.078 2.236l-10.008-7.272a2 2 0 00-2.352 0L11.316 42.13c-1.568 1.139-3.677-.394-3.078-2.236l3.823-11.766a2 2 0 00-.726-2.236L1.325 18.62c-1.567-1.139-.761-3.618 1.176-3.618h12.372a2 2 0 001.902-1.382l3.823-11.766z" />
                        <path stroke="#fff" stroke-opacity=".13" stroke-width="2"
                            d="M21.549 2.163c.3-.921 1.603-.921 1.902 0l3.823 11.766a3 3 0 002.853 2.073H42.5c.968 0 1.371 1.24.587 1.81l-10.008 7.27a3 3 0 00-1.09 3.355l3.823 11.766c.3.921-.755 1.687-1.539 1.118l-10.009-7.272a3 3 0 00-3.526 0l-10.01 7.272c-.783.57-1.837-.197-1.837-1.118l3.823-11.766a3 3 0 00-1.09-3.354L1.914 17.81c-.784-.57-.381-1.809.587-1.809h12.372a3 3 0 002.853-2.073l3.823-11.766z" />
                    </svg>
                </div>

                <div class="bshape3 eitem" value="-1.5">
                    <svg fill="none" viewBox="0 0 57 57">
                        <path fill="#fff" fill-opacity=".2"
                            d="M46.865.174c-.345.122-.846.367-1.113.557-.268.19-9.479 9.334-20.46 20.316l-19.96 19.97-2.629 6.393C1.255 50.92.041 53.993.02 54.227-.171 55.597 1.089 57 2.514 57c.479 0 .902-.167 7.496-2.885l5.947-2.45 20.038-20.026c11.015-11.016 20.17-20.25 20.349-20.527.768-1.215.869-2.785.234-4.055-.279-.557-.836-1.18-3.208-3.542C50.964 1.121 50.396.608 49.85.363c-.868-.4-2.105-.479-2.985-.19zm-1.693 11.628c2.228 2.228 4.044 4.088 4.044 4.155 0 .144-2.139 2.283-2.284 2.283-.056 0-1.949-1.838-4.199-4.088l-4.099-4.099 1.136-1.136c.635-.635 1.192-1.147 1.26-1.147.055 0 1.926 1.816 4.142 4.032zm-5.87 1.259l1.727 1.726-7.106 7.106c-7.685 7.696-7.429 7.384-6.816 7.986.601.613.29.869 7.986-6.817l7.106-7.106 1.726 1.727c.947.947 1.726 1.77 1.726 1.838 0 .055-6.783 6.894-15.07 15.18l-15.058 15.06-1.782-1.783-1.782-1.782 7.106-7.106c7.685-7.696 7.429-7.384 6.816-7.986-.601-.612-.29-.869-7.986 6.817l-7.106 7.106-1.782-1.782-1.782-1.782 15.059-15.06c8.287-8.286 15.125-15.069 15.18-15.069.068 0 .892.78 1.839 1.727zM10.178 46.864c2.072 2.072 3.753 3.799 3.731 3.82-.09.068-6.16 2.551-6.382 2.618-.156.045-.68-.412-2.038-1.77-1.003-1.003-1.827-1.872-1.827-1.939 0-.156 2.606-6.516 2.673-6.516.034 0 1.76 1.705 3.843 3.787z" />
                    </svg>
                </div>

                <div class="bshape4 eitem" value="2">
                    <svg fill="none" viewBox="0 0 146 91">
                        <path stroke="#fff" stroke-opacity=".25" stroke-width="3"
                            d="M1 88.974c57.757 1.132 10.964-35.222 26.06-46.86 15.097-11.638 32.815 14.148 53.859 8.381 21.044-5.766-12.378-39.299 12.998-47.543C119.292-5.29 116.734 43.06 145 29.644" />
                    </svg>
                </div>
            </div>

            <div class="tpshape">
                <svg fill="none" viewBox="0 0 147 297">
                    <path fill-rule="evenodd" stroke="#fff" stroke-opacity=".05" stroke-width="7"
                        d="M83.755-55.798c33.904 36.979 10.442 93.21 19.058 142.218 9.073 51.606 59.065 103.667 31.95 147.908-27.292 44.529-94.427 31.803-146.613 39.177-53.094 7.503-112.908 37.432-155.043 3.451-41.916-33.803-18.758-98.921-28.438-151.421-10.342-56.091-57.129-112.778-29.34-161.951 28.69-50.767 97.899-59.728 156.706-63.423 54.33-3.414 114.657 3.619 151.72 44.041z"
                        clip-rule="evenodd" />
                </svg>
            </div>

            <div class="btmshape">
                <svg fill="none" viewBox="0 0 236 409">
                    <path fill-rule="evenodd" stroke="#fff" stroke-opacity=".11" stroke-width="2"
                        d="M-89.755 52.094c37.15-14.894 77.387 2.568 112.843 19.565 34.705 16.637 69.624 37.842 81.693 73.691 11.968 35.549-6.127 72.04-20.797 107.193-16.373 20.349-26.341 44.529-67.167 59.065-40.895 14.626-80.23-17.547-117.945-37.376-34.682-18.235-76.17-33.695-87.396-70.589-11.017-36.203 16.557-70.85 33.888-105.254 17.307-34.357 28.555-76.251 64.881-90.814z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd" stroke="#fff" stroke-opacity=".11" stroke-width="2"
                        d="M-55.387 52.63C-19.19 39.093 19.54 57.897 53.646 76.07c33.383 17.787 66.909 40.134 78.094 76.268 11.091 35.832-6.952 71.55-21.66 106.056-16.414 38.509-26.772 87.72-66.526 100.837-39.82 13.14-77.47-20.287-113.723-41.36-33.338-19.38-73.309-36.232-83.663-73.378-10.16-36.45 17.039-70 34.313-103.666 17.251-33.62 28.738-74.961 64.132-88.197z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd" stroke="#fff" stroke-opacity=".11" stroke-width="2"
                        d="M-21.667 51.68c36.473-12.776 74.8 6.834 108.518 25.718 33.003 18.482 66.054 41.526 41.526 77.886 10.338 36.057-8.449 71.39-23.876 105.58-17.217 38.157-28.604 87.139-68.623 99.421C30.745 372.588-6.196 338.381-42 316.553c-32.925-20.073-72.534-37.759-82.108-75.114-9.395-36.654 18.501-69.627 36.477-102.925 17.95-33.251 30.3-74.342 65.964-86.834z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd" stroke="#fff" stroke-opacity=".11" stroke-width="2"
                        d="M-4.929 45.706C31.293 32.23 70.173 51.77 104.418 70.625c33.518 18.454 67.198 41.561 78.54 78.527 11.247 36.657-6.7 72.929-21.309 107.995-16.305 39.134-26.502 89.242-66.289 102.256-39.855 13.038-77.706-21.411-114.113-43.244-33.48-20.078-73.596-37.639-84.11-75.63-10.317-37.278 16.813-71.254 33.998-105.44C-51.704 100.95-40.347 58.882-4.93 45.706z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </section>
    <!-- End Home Banner -->
    <!-- End Banner Area -->

    <!-- Start Topics Area -->
    <section class="tp-feature-area" style="background-image:url(assets/img/bg/shape-bg-1.webp)">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-lg-12">
                    <div class="section-title"><span class="tp-sub-title">Ne Sunuyoruz</span>
                        <h2 class="tp-section-title">Geleceğin için Badi Akademi</h2>
                    </div>
                </div>
            </div>
            <div class="tp-feature-cn">
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="tpfea wow fadeInUp" data-wow-duration=".8s" data-wow-delay=".6s">
                            <div class="tpfea__icon"><i class="fa-solid fa-book"></i></div>
                            <div class="tpfea__text">
                                <h5 class="tpfea__title">Çevrimiçi Kurslar</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="tpfea wow fadeInUp" data-wow-duration=".8s" data-wow-delay=".6s">
                            <div class="tpfea__icon"><i class="fa-solid fa-chalkboard-user"></i></i></div>
                            <div class="tpfea__text">
                                <h5 class="tpfea__title">Uzman Eğitmenler</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="tpfea wow fadeInUp" data-wow-duration=".8s" data-wow-delay=".6s">
                            <div class="tpfea__icon"><i class="fa-solid fa-certificate"></i></div>
                            <div class="tpfea__text">
                                <h5 class="tpfea__title">Onaylı Sertifikalar</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="tpfea wow fadeInUp" data-wow-duration=".8s" data-wow-delay=".6s">
                            <div class="tpfea__icon"><i class="fa-regular fa-clock"></i></div>
                            <div class="tpfea__text">
                                <h5 class="tpfea__title">Ömür Boyu Erişim</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Topics Area -->

    <!-- Start About Area -->
    <div class="edu-about-area ptb-100">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-7">
                    <div class="edu-about-image">
                        <img src="assets/img/gecici-resim.webp" alt="Badi Akademi Hakkında" loading="lazy"
                            decoding="async" width="600" height="400"
                            style="width: 100%; height: auto; max-width: 600px; object-fit: cover;"
                            srcset="assets/img/gecici-resim.webp 600w" sizes="(max-width: 768px) 100vw, 600px">
                    </div>
                </div>
                <div class="col-lg-5 col-md-5">
                    <div class="edu-about-content">
                        <p class="sub-title">Badi Akademi Hakkında</p>
                        <h2> En İyi <span class="shape02">Eğitimi</span> Sunuyoruz</h2>
                        <p>There are many variations of passages of Lorem Ipsum available, but the majority have
                            suffered alteration in some form, by injected humour.</p>
                        <div class="progress-content">
                            <div class="progress-section">
                                <div class="progress-title">
                                    <h4 id="progress-title-1"><?php echo $anasayfa_veriler['yuzdelik1_isim'] ?></h4>
                                    <span class="title"><?php echo $anasayfa_veriler['yuzdelik1_yuzde'] ?>%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar psc01" role="progressbar"
                                        aria-valuenow="<?php echo $anasayfa_veriler['yuzdelik1_yuzde'] ?>"
                                        aria-valuemin="0" aria-valuemax="100" aria-labelledby="progress-title-1"
                                        style="max-width: <?php echo $anasayfa_veriler['yuzdelik1_yuzde'] ?>%">
                                    </div>
                                </div>
                            </div>
                            <div class="progress-section">
                                <div class="progress-title">
                                    <h4 id="progress-title-2"><?php echo $anasayfa_veriler['yuzdelik2_isim'] ?></h4>
                                    <span class="title"><?php echo $anasayfa_veriler['yuzdelik2_yuzde'] ?>%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar psc02" role="progressbar"
                                        aria-valuenow="<?php echo $anasayfa_veriler['yuzdelik2_yuzde'] ?>"
                                        aria-valuemin="0" aria-valuemax="100" aria-labelledby="progress-title-2"
                                        style="max-width: <?php echo $anasayfa_veriler['yuzdelik2_yuzde'] ?>%">
                                    </div>
                                </div>
                            </div>
                            <div class="progress-section">
                                <div class="progress-title">
                                    <h4 id="progress-title-3"><?php echo $anasayfa_veriler['yuzdelik3_isim'] ?></h4>
                                    <span class="title"><?php echo $anasayfa_veriler['yuzdelik3_yuzde'] ?>%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar psc03" role="progressbar"
                                        aria-valuenow="<?php echo $anasayfa_veriler['yuzdelik3_yuzde'] ?>"
                                        aria-valuemin="0" aria-valuemax="100" aria-labelledby="progress-title-3"
                                        style="max-width: <?php echo $anasayfa_veriler['yuzdelik3_yuzde'] ?>%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End About Area -->


    <!-- Start Courses Area -->
    <section class="course-area wow" data-wow-duration=".8s" data-wow-delay=".4s">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title text-center">
                        <h2 class="tp-section-title">Popüler Kurslar</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <?php while ($kurscek = $kurssor->fetch(PDO::FETCH_ASSOC)) {


                    $ortalama_puan = $kurscek['puan'] ? round($kurscek['puan'], 2) : 0;

                    $kategorisor = $db->prepare("SELECT * FROM kategoriler WHERE kategori_id=:kategori_id");
                    $kategorisor->execute([
                        'kategori_id' => $kurscek['kategori_id']
                    ]);
                    $kategoricek = $kategorisor->fetch(PDO::FETCH_ASSOC);

                    $altkategorisor = $db->prepare("SELECT * FROM alt_kategoriler WHERE alt_kategori_id=:alt_kategori_id");
                    $altkategorisor->execute([
                        'alt_kategori_id' => $kurscek['alt_kategori_id']
                    ]);
                    $altkategoricek = $altkategorisor->fetch(PDO::FETCH_ASSOC);


                    $egitmensor = $db->prepare("SELECT * FROM egitmen WHERE egitmen_id=:egitmen_id");
                    $egitmensor->execute([
                        'egitmen_id' => $kurscek['egitmen_id']
                    ]);
                    $egitmencek = $egitmensor->fetch(PDO::FETCH_ASSOC);

                    ?>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="tpcourse">
                            <div class="tpcourse__thumb">
                                <a href="kurs-detay.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>">
                                    <img src="<?php echo $kurscek['resim_yol']; ?>"
                                        alt="<?php echo $kurscek['baslik']; ?> kursu" loading="lazy" decoding="async">
                                </a>
                            </div>
                            <div class="tpcourse__content">
                                <div class="tpcourse__avatar align-items-center"><img
                                        src="<?php echo $egitmencek['egitmen_resimyol']; ?>" alt="course-avata" />
                                    <h4 class="tpcourse__title"><a
                                            href="kurs-detay.php?kurs_id=<?php echo $kurscek['kurs_id']; ?>"><?php echo $kurscek['baslik']; ?></a>
                                    </h4>
                                </div>
                                <div class="tpcourse__meta">
                                    <ul class="align-items-center">
                                        <li><i class="fa-solid fa-stopwatch"></i><span><?php echo $kurscek['sure']; ?>
                                                Saat</span></li>
                                        <li><i class="fa-solid fa-user"></i>
                                            <span>
                                                <?php echo isset($kurscek['ogrenci_sayi']) && $kurscek['ogrenci_sayi'] ? $kurscek['ogrenci_sayi'] : 0; ?>
                                                Öğrenci
                                            </span>
                                        </li>
                                        <li><i class="fa-solid fa-star"></i>
                                            <span>
                                                <?php echo isset($ortalama_puan) && $ortalama_puan ? $ortalama_puan : 0; ?>
                                                Puan
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tpcourse__category align-items-center">
                                    <ul class="tpcourse__price-list align-items-center">
                                        <li><a href="#"><?php echo $altkategoricek['ad']; ?></a></li>
                                        <li><a href="#"><?php echo $kategoricek['ad']; ?></a></li>
                                    </ul>
                                    <h5 class="tpcourse__course-price">
                                        <!-- --><?php echo number_format($kurscek['fiyat'], 0, '.', ''); ?> TL
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
            <div class="row text-center">
                <div class="col-lg-12">
                    <div class="course-btn"><a class="tp-btn" href="kurslar_1.php">Tüm Kurslar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Courses Area -->

    <!-- Start Counter Area 02 -->
    <div class="edu-counter-area02 pt-100">
        <div class="container">
            <div class="edu-section-title">
                <h2><span class="shape02">Başarı</span> Tablomuz</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-3 col-md-6 col-sm-12 text-center">
                    <div class="counter-icon">
                        <i class="fa-solid fa-user counter-icon1"></i>
                    </div>
                    <div class="counter-box02">
                        <h3><span class="odometer"
                                data-count="<?php echo $anasayfa_veriler['toplam_ogrenci'] ?>">00</span></h3>
                        <p>Toplam Öğrenci</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 text-center">
                    <div class="counter-icon">
                        <i class="fa-solid fa-calendar counter-icon2"></i>
                    </div>
                    <div class="counter-box02">
                        <h3><span class="odometer" data-count="<?php echo $anasayfa_veriler['deneyim'] ?>">00</span>
                        </h3>
                        <p>Yıllık Deneyim</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 text-center">
                    <div class="counter-icon">
                        <i class="fa-solid fa-school counter-icon3"></i>
                    </div>
                    <div class="counter-box02">
                        <h3><span class="odometer" data-count="<?php echo $anasayfa_veriler['toplam_kurs'] ?>">00</span>
                        </h3>
                        <p>Profesyonel Kurs</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 text-center">
                    <div class="counter-icon">
                        <i class="fa-solid fa-star counter-icon4"></i>
                    </div>
                    <div class="counter-box02">
                        <h3><span class="odometer"
                                data-count="<?php echo $anasayfa_veriler['olumlu_yorum'] ?>">00</span>
                        </h3>
                        <p>Olumlu Yorum</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Start Counter Area 02 -->

    <!-- Start Video and Brand Area -->
    <div class="edu-video-area">
        <div class="brands-area sectionBg07 ptb-100">
            <div class="container">
                <div class="brands-title">
                    <h3>1000+ <span class="mini-shape">Güvenilir</span> Referans</h3>
                </div>
                <!-- Swiper Container -->
                <div class="marka-swiper">
                    <div class="swiper-wrapper">
                        <?php while ($markacek = $markasor->fetch(PDO::FETCH_ASSOC)) { ?>
                            <div class="swiper-slide swiper-marka">
                                <img src="<?php echo $markacek['marka_resimyol']; ?>" alt="brands">
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Video Area -->

    <!-- Start whyChoose Area -->
    <div class="edu-whyChoose-area ptb-100">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-5 col-sm-5">
                    <div class="edu-whyChoose-content">
                        <p class="sub-title"><?php echo $anahakkimizdacek['anahakkimizda_title']; ?></p>
                        <h2> <?php echo $anahakkimizdacek['anahakkimizda_header']; ?> <span
                                class="shape02"><?php echo $anahakkimizdacek['anahakkimizda_color']; ?> </span></h2>
                        <p><?php echo $anahakkimizdacek['anahakkimizda_text']; ?>
                        </p>
                        <div class="whyChoose-list">
                            <div class="items">
                                <div class="edu-whyChoose-img">
                                    <img src="assets/img/svg/book-open-svgrepo-com.svg" alt="icon" loading="lazy">
                                </div>
                                <div class="whyChoose-single-content">
                                    <h3>Bilgiye Ulaşmanın En Kolay Yolu: Badi Akademi!</h3>
                                </div>
                            </div>
                            <div class="items">
                                <div class="edu-whyChoose-img">
                                    <img src="assets/img/svg/sail-boat-svgrepo-com.svg" alt="icon" loading="lazy">
                                </div>
                                <div class="whyChoose-single-content">
                                    <h3>Badi Akademi ile Öğren, Başarıya Yelken Aç!</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-md-7 col-sm-7">
                    <div class="edu-whyChoose-image edu-about-image">
                        <img src="<?php echo $anahakkimizdacek['anahakkimizda_resimyol']; ?>" alt="banner-img">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End whyChoose Area -->


    <!-- Start Artical Area -->
    <div class="edu-blog-area sectionBg05 ptb-100">
        <div class="container">
            <div class="edu-section-title text-start">
                <h2><span class="shape02">Bloglarımız</span></h2>
                <a href="blog.php" class="default-btn">Hepsini Gör</a>
            </div>
            <div class="row justify-content-center">
                <?php while ($blogcek = $blogsor->fetch(PDO::FETCH_ASSOC)) {

                    $blogkategorisor = $db->prepare("SELECT kategori_ad FROM blog_kategori WHERE blogkategori_id = :blogkategori_id");
                    $blogkategorisor->execute([
                        'blogkategori_id' => $blogcek['blog_kategori_id']
                    ]);
                    $blogkategoricek = $blogkategorisor->fetch(PDO::FETCH_ASSOC);

                    ?>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="single-blog-box">
                            <div class="image">
                                <!-- ID değerini URL'ye ekliyoruz -->
                                <a href="blog-details.php?blog_id=<?php echo $blogcek['blog_id']; ?>" class="d-block">
                                    <img src="<?php echo $blogcek['blog_resimyol']; ?>" alt="image" loading="lazy">
                                </a>
                                <div class="cr-tag">
                                    <a href="#"><span><?php echo $blogkategoricek['kategori_ad']; ?></span></a>
                                </div>
                            </div>
                            <div class="content">
                                <ul class="cr-items">
                                    <li><a href="#"><i
                                                class='bx bx-user'></i><span><?php echo $blogcek['yazar_ad']; ?></span></a>
                                    </li>
                                    <li>
                                        <i class='bx bx-star'></i>
                                        <span>
                                            <?php
                                            $tarih = new DateTime($blogcek['blog_tarih']);
                                            $aylar = array(
                                                'January' => 'Ocak',
                                                'February' => 'Şubat',
                                                'March' => 'Mart',
                                                'April' => 'Nisan',
                                                'May' => 'Mayıs',
                                                'June' => 'Haziran',
                                                'July' => 'Temmuz',
                                                'August' => 'Ağustos',
                                                'September' => 'Eylül',
                                                'October' => 'Ekim',
                                                'November' => 'Kasım',
                                                'December' => 'Aralık'
                                            );
                                            $ay = $aylar[$tarih->format('F')];
                                            echo $tarih->format('d ') . $ay . $tarih->format(' Y');
                                            ?>
                                        </span>
                                    </li>
                                </ul>
                                <!-- ID'yi tıklanan başlığa da ekliyoruz -->
                                <h3><a
                                        href="blog-details.php?blog_id=<?php echo $blogcek['blog_id']; ?>"><?php echo $blogcek['blog_ad']; ?></a>
                                </h3>
                                <a class="blog-btn"
                                    href="blog-details.php?blog_id=<?php echo $blogcek['blog_id']; ?>">Devamını
                                    oku...</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="go-top active">
        <i class="bx bx-up-arrow-alt"></i>
    </div>
    <!-- End Artical Area -->

    <?php include 'footer.php'; ?>

    <script>
        const swiper = new Swiper('.swiper-instructor', {
            slidesPerView: 3,
            spaceBetween: 30,
            navigation: {
                nextEl: '.swiper-button-next-instructor',
                prevEl: '.swiper-button-prev-instructor',
            },
            loop: true,
            breakpoints: {
                640: { slidesPerView: 1, spaceBetween: 10 },
                768: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 30 },
            },
        });
    </script>