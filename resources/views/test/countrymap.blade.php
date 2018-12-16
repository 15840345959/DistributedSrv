<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width">
    <title>美景听听</title>
    <link rel="stylesheet" href="https://cache.amap.com/lbs/static/main1119.css"/>
    <script src="https://cache.amap.com/lbs/static/es5.min.js"></script>
    <script src="https://webapi.amap.com/maps?v=1.4.9&key=59jTR8CY79Z1XCvIBUG1xCoS2FVFG0i8"></script>
    <!-- <script type="text/javascript" src="https://cache.amap.com/lbs/static/addToolbar.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

    <style type="text/css">
        /* 仿微信switch样式 */
        .mui-switch {
            width: 52px;
            height: 31px;
            position: relative;
            border: 1px solid #dfdfdf;
            background-color: #fdfdfd;
            box-shadow: #dfdfdf 0 0 0 0 inset;
            border-radius: 20px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            background-clip: content-box;
            display: inline-block;
            -webkit-appearance: none;
            user-select: none;
            outline: none;
        }

        .mui-switch:before {
            content: '';
            width: 29px;
            height: 29px;
            position: absolute;
            top: 0px;
            left: 0;
            border-radius: 20px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        .mui-switch:checked {
            border-color: #64bd63;
            box-shadow: #64bd63 0 0 0 16px inset;
            background-color: #64bd63;
        }

        .mui-switch:checked:before {
            left: 21px;
        }

        .mui-switch.mui-switch-animbg {
            transition: background-color ease 0.4s;
        }

        .mui-switch.mui-switch-animbg:before {
            transition: left 0.3s;
        }

        .mui-switch.mui-switch-animbg:checked {
            box-shadow: #dfdfdf 0 0 0 0 inset;
            background-color: #64bd63;
            transition: border-color 0.4s, background-color ease 0.4s;
        }

        .mui-switch.mui-switch-animbg:checked:before {
            transition: left 0.3s;
        }

        .mui-switch.mui-switch-anim {
            transition: border cubic-bezier(0, 0, 0, 1) 0.4s, box-shadow cubic-bezier(0, 0, 0, 1) 0.4s;
        }

        .mui-switch.mui-switch-anim:before {
            transition: left 0.3s;
        }

        .mui-switch.mui-switch-anim:checked {
            box-shadow: #64bd63 0 0 0 16px inset;
            background-color: #64bd63;
            transition: border ease 0.4s, box-shadow ease 0.4s, background-color ease 1.2s;
        }

        .mui-switch.mui-switch-anim:checked:before {
            transition: left 0.3s;
        }

        /* 取消高德自带样式 */
        #container {
            position: absolute;
            top: 0px;
            left: 0;
            right: 0;
            bottom: 0px;
            width: 100%;
            height: 100%;
        }

        .amap-zoomcontrol {
            width: 24px;
            position: absolute;
            display: none;
            /* opacity: 1; */
        }

        .amap-logo {
            right: 0 !important;
            left: auto !important;
            display: none;
            bottom: -100px;
        }

        .amap-copyright {
            right: 0px !important;
            left: auto !important;
            bottom: -100px;
            display: none;
        }

        /* 顶部窗 */
        .top {
            position: fixed;
            top: 0px;
            left: 0px;
            height: 70px;
            width: 100%;
            background: #e31c17;
            z-index: 1;
        }

        .page-top-position {
            position: fixed;
            top: 0px;
            left: 0px;
            height: 70px;
            width: 100%;
            z-index: 1;
        }

        .page-top {
            width: 100%;
            height: 50px;
            background-color: #e31c17;
        }

        .page-top-title {
            font-family: PingFangSC-Semibold;
            font-size: 22px;
            color: #fff;
            letter-spacing: 0.35px;
            text-align: center;
            line-height: 50px;
            z-index: 1000;
        }

        .center-style {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* 立即购买 */
        .buy-border {
            height: 50px;
            width: 94%;
            background-color: #fff;
            border-radius: 25px;
            margin: 10px 3%;
            position: fixed;
            top: 0px;
            left: 0px;
        }

        .flex-row {
            display: flex;
            flex-direction: row;
        }

        .buy-font {
            width: 250px;
            height: 50px;
            margin-left: 40px;
            font-size: 14px;
        }

        .buy-font-button {
            font-family: PingFang-SC-Medium;
            font-size: 14px;
            color: #fff;
            letter-spacing: 0.22px;
        }

        /* 自动导览 */
        .myPosition {
            position: absolute;
            bottom: 110px;
            left: 3%;
            width: 60px;
            height: 120px;
            background: #fff;
            border-radius: 3px;
            box-shadow: 0 0 6px 0 rgba(0, 0, 0, 0.20);
            border: 1px #8f8d8d solid;
        }

        .line {
            position: absolute;
            top: 60px;
            left: 2.5px;
            width: 55px;
            height: 1px;
            background: #ececec;
        }

        /* 底部景区 */
        .scenic-border {
            background: #fff;
            box-shadow: 0 0 6px 0 rgba(0, 0, 0, 0.20);
            border-radius: 10px;
            width: 94%;
            height: 72px;
            position: absolute;
            bottom: 10px;
            left: 0px;
            margin: 0px 3%;
        }

        .text-oneline {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            word-break: break-all;
            -webkit-line-clamp: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .scenic-play {
            position: absolute;
            top: 1.6rem;
            left: 1.8rem;
        }

        .aui-hide {
            display: none;
        }

        .scenic-play-one {
            position: absolute;
            top: 1.05rem;
            left: 1.3rem;
        }

        .ui-tip {
            width: auto;
            height: auto;
            color: #fff;
            padding: 0.4rem;
            margin: 0 auto;
            position: relative;
            text-align: center;
            border-radius: 20px;
            line-height: 20px;
            box-shadow: 0px 0px 3px #999;

        }

        /* tip with arrow */

        .ui-tip-arrow::before,
        .ui-tip-arrow::after {
            content: "";
            display: block;
            position: absolute;
            width: 0;
            height: 0px;
            border-width: 0.25rem;
            border-style: solid;
        }

        .ui-tip-arrow.ui-tip-arrow-up::after {
            border-left-color: transparent;
            border-top-color: transparent;
            bottom: 100%;
            right: 50%;
        }

        .ui-tip-arrow.ui-tip-arrow-up::before {
            border-right-color: transparent;
            border-top-color: transparent;
            bottom: 100%;
            left: 50%;
        }

        /* arrow down */

        .ui-tip-arrow.ui-tip-arrow-down::after {
            border-left-color: transparent;
            border-bottom-color: transparent;
            right: 50%;
            top: 100%;
        }

        .ui-tip-arrow.ui-tip-arrow-down::before {
            border-right-color: transparent;
            border-bottom-color: transparent;
            left: 50%;
            top: 100%;
        }

        .ui-theme-black {
            /* background-color: rgba(48, 48, 48, 1); */
            background-color: #E4150F;
            color: black;
            box-shadow: 0px 0px 3px #999;
        }

        .ui-theme-black::after,
        .ui-theme-black::before {
            /* border-color: rgba(48, 48, 48, 1); */
            border-color: #E4150F;
            /* box-shadow: 0px 0px 3px #999; */
        }

        .text-overflow {
            display: block;
            word-break: keep-all;
            /* 不换行 */
            white-space: nowrap;
            /* 内容超出宽度时隐藏超出部分的内容 */
        }

        /*
        *
        *
        *进度条样式
        *
        *
        *
        */

        /* -------------------------------------
         * Bar container
         * ------------------------------------- */
        .progress-radial {
            display: inline-block;
            /*margin: 15px;*/
            /*position: relative;*/
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 3px solid #5d6771;
            background-color: #fffde8;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
        }

        .progress-radial:after {
            z-index: 998;
            box-shadow: none;
            transform: translate(0, -72.5px);
        }

        .progress-radial b:after {
            color: #fffde8;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
            position: absolute;
            font-weight: 900;
            left: 60%;
            top: 60%;
            width: 30%;
            height: 30%;
            background-color: #5d6771;
            border-radius: 50%;
            margin-left: -25%;
            margin-top: -25%;
            text-align: center;
            line-height: 90px;
            font-size: 0px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.3) inset, 0 0 0 4px #5d6771;
        }

        /* -------------------------------------
         * Mixin for progress-% class
         * ------------------------------------- */
        .progress-0 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(90deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-0 b:after {
            content: "0%";
        }

        .progress-1 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(93.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-1 b:after {
            content: "1%";
        }

        .progress-2 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(97.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-2 b:after {
            content: "2%";
        }

        .progress-3 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(100.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-3 b:after {
            content: "3%";
        }

        .progress-4 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(104.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-4 b:after {
            content: "4%";
        }

        .progress-5 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(108deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-5 b:after {
            content: "5%";
        }

        .progress-6 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(111.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-6 b:after {
            content: "6%";
        }

        .progress-7 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(115.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-7 b:after {
            content: "7%";
        }

        .progress-8 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(118.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-8 b:after {
            content: "8%";
        }

        .progress-9 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(122.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-9 b:after {
            content: "9%";
        }

        .progress-10 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(126deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-10 b:after {
            content: "10%";
        }

        .progress-11 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(129.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-11 b:after {
            content: "11%";
        }

        .progress-12 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(133.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-12 b:after {
            content: "12%";
        }

        .progress-13 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(136.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-13 b:after {
            content: "13%";
        }

        .progress-14 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(140.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-14 b:after {
            content: "14%";
        }

        .progress-15 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(144deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-15 b:after {
            content: "15%";
        }

        .progress-16 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(147.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-16 b:after {
            content: "16%";
        }

        .progress-17 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(151.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-17 b:after {
            content: "17%";
        }

        .progress-18 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(154.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-18 b:after {
            content: "18%";
        }

        .progress-19 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(158.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-19 b:after {
            content: "19%";
        }

        .progress-20 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(162deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-20 b:after {
            content: "20%";
        }

        .progress-21 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(165.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-21 b:after {
            content: "21%";
        }

        .progress-22 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(169.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-22 b:after {
            content: "22%";
        }

        .progress-23 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(172.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-23 b:after {
            content: "23%";
        }

        .progress-24 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(176.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-24 b:after {
            content: "24%";
        }

        .progress-25 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(180deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-25 b:after {
            content: "25%";
        }

        .progress-26 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(183.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-26 b:after {
            content: "26%";
        }

        .progress-27 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(187.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-27 b:after {
            content: "27%";
        }

        .progress-28 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(190.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-28 b:after {
            content: "28%";
        }

        .progress-29 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(194.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-29 b:after {
            content: "29%";
        }

        .progress-30 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(198deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-30 b:after {
            content: "30%";
        }

        .progress-31 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(201.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-31 b:after {
            content: "31%";
        }

        .progress-32 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(205.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-32 b:after {
            content: "32%";
        }

        .progress-33 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(208.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-33 b:after {
            content: "33%";
        }

        .progress-34 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(212.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-34 b:after {
            content: "34%";
        }

        .progress-35 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(216deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-35 b:after {
            content: "35%";
        }

        .progress-36 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(219.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-36 b:after {
            content: "36%";
        }

        .progress-37 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(223.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-37 b:after {
            content: "37%";
        }

        .progress-38 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(226.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-38 b:after {
            content: "38%";
        }

        .progress-39 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(230.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-39 b:after {
            content: "39%";
        }

        .progress-40 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(234deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-40 b:after {
            content: "40%";
        }

        .progress-41 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(237.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-41 b:after {
            content: "41%";
        }

        .progress-42 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(241.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-42 b:after {
            content: "42%";
        }

        .progress-43 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(244.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-43 b:after {
            content: "43%";
        }

        .progress-44 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(248.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-44 b:after {
            content: "44%";
        }

        .progress-45 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(252deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-45 b:after {
            content: "45%";
        }

        .progress-46 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(255.6deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-46 b:after {
            content: "46%";
        }

        .progress-47 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(259.2deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-47 b:after {
            content: "47%";
        }

        .progress-48 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(262.8deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-48 b:after {
            content: "48%";
        }

        .progress-49 {
            background-image: linear-gradient(90deg, #5d6771 50%, transparent 50%, transparent), linear-gradient(266.4deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-49 b:after {
            content: "49%";
        }

        .progress-50 {
            background-image: linear-gradient(-90deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-50 b:after {
            content: "50%";
        }

        .progress-51 {
            background-image: linear-gradient(-86.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-51 b:after {
            content: "51%";
        }

        .progress-52 {
            background-image: linear-gradient(-82.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-52 b:after {
            content: "52%";
        }

        .progress-53 {
            background-image: linear-gradient(-79.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-53 b:after {
            content: "53%";
        }

        .progress-54 {
            background-image: linear-gradient(-75.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-54 b:after {
            content: "54%";
        }

        .progress-55 {
            background-image: linear-gradient(-72deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-55 b:after {
            content: "55%";
        }

        .progress-56 {
            background-image: linear-gradient(-68.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-56 b:after {
            content: "56%";
        }

        .progress-57 {
            background-image: linear-gradient(-64.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-57 b:after {
            content: "57%";
        }

        .progress-58 {
            background-image: linear-gradient(-61.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-58 b:after {
            content: "58%";
        }

        .progress-59 {
            background-image: linear-gradient(-57.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-59 b:after {
            content: "59%";
        }

        .progress-60 {
            background-image: linear-gradient(-54deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-60 b:after {
            content: "60%";
        }

        .progress-61 {
            background-image: linear-gradient(-50.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-61 b:after {
            content: "61%";
        }

        .progress-62 {
            background-image: linear-gradient(-46.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-62 b:after {
            content: "62%";
        }

        .progress-63 {
            background-image: linear-gradient(-43.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-63 b:after {
            content: "63%";
        }

        .progress-64 {
            background-image: linear-gradient(-39.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-64 b:after {
            content: "64%";
        }

        .progress-65 {
            background-image: linear-gradient(-36deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-65 b:after {
            content: "65%";
        }

        .progress-66 {
            background-image: linear-gradient(-32.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-66 b:after {
            content: "66%";
        }

        .progress-67 {
            background-image: linear-gradient(-28.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-67 b:after {
            content: "67%";
        }

        .progress-68 {
            background-image: linear-gradient(-25.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-68 b:after {
            content: "68%";
        }

        .progress-69 {
            background-image: linear-gradient(-21.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-69 b:after {
            content: "69%";
        }

        .progress-70 {
            background-image: linear-gradient(-18deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-70 b:after {
            content: "70%";
        }

        .progress-71 {
            background-image: linear-gradient(-14.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-71 b:after {
            content: "71%";
        }

        .progress-72 {
            background-image: linear-gradient(-10.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-72 b:after {
            content: "72%";
        }

        .progress-73 {
            background-image: linear-gradient(-7.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-73 b:after {
            content: "73%";
        }

        .progress-74 {
            background-image: linear-gradient(-3.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-74 b:after {
            content: "74%";
        }

        .progress-75 {
            background-image: linear-gradient(0deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-75 b:after {
            content: "75%";
        }

        .progress-76 {
            background-image: linear-gradient(3.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-76 b:after {
            content: "76%";
        }

        .progress-77 {
            background-image: linear-gradient(7.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-77 b:after {
            content: "77%";
        }

        .progress-78 {
            background-image: linear-gradient(10.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-78 b:after {
            content: "78%";
        }

        .progress-79 {
            background-image: linear-gradient(14.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-79 b:after {
            content: "79%";
        }

        .progress-80 {
            background-image: linear-gradient(18deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-80 b:after {
            content: "80%";
        }

        .progress-81 {
            background-image: linear-gradient(21.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-81 b:after {
            content: "81%";
        }

        .progress-82 {
            background-image: linear-gradient(25.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-82 b:after {
            content: "82%";
        }

        .progress-83 {
            background-image: linear-gradient(28.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-83 b:after {
            content: "83%";
        }

        .progress-84 {
            background-image: linear-gradient(32.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-84 b:after {
            content: "84%";
        }

        .progress-85 {
            background-image: linear-gradient(36deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-85 b:after {
            content: "85%";
        }

        .progress-86 {
            background-image: linear-gradient(39.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-86 b:after {
            content: "86%";
        }

        .progress-87 {
            background-image: linear-gradient(43.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-87 b:after {
            content: "87%";
        }

        .progress-88 {
            background-image: linear-gradient(46.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-88 b:after {
            content: "88%";
        }

        .progress-89 {
            background-image: linear-gradient(50.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-89 b:after {
            content: "89%";
        }

        .progress-90 {
            background-image: linear-gradient(54deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-90 b:after {
            content: "90%";
        }

        .progress-91 {
            background-image: linear-gradient(57.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-91 b:after {
            content: "91%";
        }

        .progress-92 {
            background-image: linear-gradient(61.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-92 b:after {
            content: "92%";
        }

        .progress-93 {
            background-image: linear-gradient(64.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-93 b:after {
            content: "93%";
        }

        .progress-94 {
            background-image: linear-gradient(68.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-94 b:after {
            content: "94%";
        }

        .progress-95 {
            background-image: linear-gradient(72deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-95 b:after {
            content: "95%";
        }

        .progress-96 {
            background-image: linear-gradient(75.6deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-96 b:after {
            content: "96%";
        }

        .progress-97 {
            background-image: linear-gradient(79.2deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-97 b:after {
            content: "97%";
        }

        .progress-98 {
            background-image: linear-gradient(82.8deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-98 b:after {
            content: "98%";
        }

        .progress-99 {
            background-image: linear-gradient(86.4deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        .progress-99 b:after {
            content: "99%";
        }

        .progress-100 {
            background-image: linear-gradient(90deg, #fffde8 50%, transparent 50%, transparent), linear-gradient(270deg, #fffde8 50%, #5d6771 50%, #5d6771);
        }

        /*.progress-100:before {*/
        /*transform: rotate(360deg) translate(0, -72.5px);*/
        /*}*/
        .progress-100 b:after {
            content: "100%";
        }

        /* 缩放 */
        .switck-scale {
            transform: scale(0.8);
            -ms-transform: scale(0.8);
            -webkit-transform: scale(0.8);
            -o-transform: scale(0.8);
            -moz-transform: scale(0.8);
        }
    </style>

</head>

<body>
<div id="container"></div>
<!-- 顶部立即购买 -->
<div class='buy-border flex-row animation-top'>
    <div class='buy-font'
         style='font-size: 14px;color: #666666;line-height:50px; font-family: PingFang-SC-Regular;letter-spacing: 0.22px;'>
        29元解锁北京所有景点3个月
    </div>
    <div id="but"
         style='width:100px;height:35px;border-radius:17.5px;background:#e31c17;float:right;margin:7.5px 10px;text-align:center;line-height:35px;'
         class='buy-font-button'>立即购买
    </div>
</div>


<!-- 自动导览 定位 -->
<div class='myPosition' style='text-align:center;'>
    <div style='width:60px;height:60px;'>
        <!-- <img src='http://dsyy.isart.me/tmp/wx9b70c1acbcfda86b.o6zAJs3FFzas02nMmUHEIaQsPMXk.ftaFo6wp6yyDf1de13509ed16a9d85f305bebb29d300.png'
            style='width:25px;height:25px;margin:7.5px auto 5px auto;' /> -->
        <!-- <switch style='width:104rpx;height:70rpx;transform: scale(0.6);-ms-transform: scale(0.6);-webkit-transform: scale(0.6);-o-transform: scale(0.6);-moz-transform: scale(0.6);' color="#E31C17" checked bindchange="switch1Change" /> -->
        <label><input class="mui-switch mui-switch-anim switck-scale" type="checkbox"></label><br>
        <div style='font-size: 12px;color: #333333;letter-spacing: 0;'>自动导览</div>
    </div>

    <div style='width:60px;height:60px;' onclick="dw_map()">
        <img src='http://dsyy.isart.me/tmp/wx9b70c1acbcfda86b.o6zAJs3FFzas02nMmUHEIaQsPMXk.Nicx6FdG62vh3f8525c9eeb4d0655eb463c5ccb10a64.png'
             style='width:25px;height:25px;margin:7.5px auto 5px auto;'/>
        <div style='font-size: 12px;color: #333333;letter-spacing: 0;'>定位</div>
    </div>

    <div class='line'></div>
</div>


<!-- 底部景区 -->
<div class='scenic-border flex-row'>
    <img id="image"
         src='http://dsyy.isart.me/tmp/wx9b70c1acbcfda86b.o6zAJs3FFzas02nMmUHEIaQsPMXk.l5cDqfdK5jFU2ac8b861754d88021a064f740cf964ec.jpg?imagediv2/1/w/97/h/72/interlac12e/1'
         style='width:24%;height:72px;border-radius: 10px 0px 0px 10px;'/>


    <div style='margin-left:10px;width:198px;'>
        <div id="name" class='text-oneline'
             style='font-family: PingFangSC-Medium;font-size: 18px;color: #333333;letter-spacing: -0.45px;line-height:45px;'>
            景仁宫
        </div>
        <div class='text-oneline'
             style='font-family: Futura-Medium;font-size: 12px;color: #666666;letter-spacing: 0.4px;line-height: 14px;'>
            Castle San Angelo
        </div>

        <!-- <a href="javascript:;" class="weui-btn weui-btn_default">按钮</a> -->
    </div>


    <div style='margin-left:10px;width:60px;align-items:center;display:flex;' bindtap='sonScenic'>
        <img src="http://dsyy.isart.me/tmp/wx9b70c1acbcfda86b.o6zAJs3FFzas02nMmUHEIaQsPMXk.ftaFo6wp6yyDf1de13509ed16a9d85f305bebb29d300.png"
             style='width:30px;height:30px;'/>
    </div>


    <!--进度条-->
    <div id="play_jd" class="scenic-play-one progress-radial progress-0"><b></b></div>

    <img id="aPause" class="scenic-play aui-hide"
         src="http://dsyy.isart.me/tmp/wx9b70c1acbcfda86b.o6zAJs3FFzas02nMmUHEIaQsPMXk.57XSvNxyDsScd1dcfceca98fd91bc457064229d8b01c.png"
         style='width:25px;height:23.2px;' onclick="aPlay(1)"/>

    <img id="aPlay" class="scenic-play"
         src="http://dsyy.isart.me/tmp/wx9b70c1acbcfda86b.o6zAJs3FFzas02nMmUHEIaQsPMXk.aC1Q87ycVuac1e57363dec7826edb489db9e7a02dde4.png"
         style='width:25px;height:23.2px;' onclick="aPlay(0);"/>


</div>


<!-- <audio src="" controls="controls"></audio> -->

<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script>

<script type="text/javascript">

    //微信相关/////////////////////////////////////////////////////
    wx.config({!! $wxConfig !!});

    // document.getElementById("demo").innerHTML = '<audio src=" ' + mapData[0].audios[0].audio + '" controls autoplay></audio>';
    var audio = document.createElement("audio");

    // audio.src = mapData[0].audios[0].audio;
    audio.src = "https://music.gowithtommy.com/mjtt_backend_server%2Fprod%2Fdata%2F7b72b62d1c986158b9dc811781d46578bad9d8e3.mp3";

    audio.addEventListener("canplaythrough", function () {
        //        alert('音频文件已经准备好，随时待命');
    }, false);


    var log_time,
        all_time,
        xu_time;     //循环发生事件 事件
    var jindu = 0;  //进度条数组
    // 播放  0开始 1暂停
    function aPlay(type) {
        if (type == 0) {
            audio.play();
            $("#aPlay").addClass('aui-hide')
            $("#aPause").removeClass('aui-hide')
            $("#play_jd").removeClass('progress-' + jindu)
            log_time = audio.currentTime
            all_time = parseInt(audio.duration) - 2
            if (log_time < all_time) {
                xu_time = setTimeout(function () {
                    aPlay(0)
                }, 100);
            } else {
                //       console.log("停止");
                $("#aPlay").removeClass('aui-hide')
                $("#aPause").addClass('aui-hide')
            }
            jindu = Math.ceil(audio.currentTime / audio.duration * 100) + 10
            //    console.log(jindu);
            if (jindu >= 100) {
                jindu = 100
            }
            $("#play_jd").addClass('progress-' + jindu)


        } else {
            audio.pause();
            $("#aPlay").removeClass('aui-hide')
            $("#aPause").addClass('aui-hide')
            //终止setTimeout事件
            clearTimeout(xu_time)
        }
    }


    // 暂停
    function aPause() {
        audio.pause();
    }

    // 停止
    function aStop() {
        audio.currentTime = 0;
        audio.pause();
    }

    // 跳转到50秒
    function aSkip() {
        audio.currentTime = 50;
        audio.play();
    }

    $(function () {
        (function ($) {
            $.getUrlParam = function (name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return unescape(r[2]);
                return null;
            }
        })(jQuery);
        var city_id = $.getUrlParam('city_id');

        $("div").data("city_id", city_id);

        $.ajax({
            type: "get",
            url: "https://api.gowithtommy.com/rest/miniapp/city/list/",
            // data: { "city_id": city_id },
            data: {"country_id": 40},
            dataType: "text",
            success: function (ret) {
                var ret = JSON.parse(ret); //由JSON字符串转换为JSON对象
                console.log("城市列表" + JSON.stringify(ret))
                var mapData = ret.data
                //展示地图
                showMap(mapData)
            }
        });

    });


    var all_arr;  //存放 ajax 返回的数据
    var map; //地图
    function showMap(mapData) {
        //   console.log(mapData)
        all_arr = mapData

        var imageLayer = new AMap.ImageLayer({
            url: 'http://amappc.cn-hangzhou.oss-pub.aliyun-inc.com/lbs/static/img/dongwuyuan.jpg',
            bounds: new AMap.Bounds(
                [116.327911, 39.939229],
                [116.342659, 39.946275]
            ),
            zooms: [10, 18],
            opacity: 1
        });

        map = new AMap.Map('container', {
            resizeEnable: true,
            center: [mapData[0].longitude, mapData[0].latitude],
            zoom: 10,
            layers: [
                new AMap.TileLayer(),
                imageLayer
            ]
        });

        for (var i = 0; i < mapData.length; i++) {
            var content = mapData[i].name
            var plixel = new AMap.Pixel(-10, -34)

            //添加点标记，并使用自己的icon
            var marker = new AMap.Marker({
                map: map,
                position: [mapData[i].longitude, mapData[i].latitude],
                offset: plixel,
                content: '<div class="ui-tip ui-tip-arrow ui-theme-black ui-tip-arrow-down text-overflow" style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">' + content + '</div>',
            });
            //给标记点添加点击事件
            marker.on('click', markerClick, mapData[i].name);
            //    marker.emit('click', {target: marker});

        }
    }


    //地图 标记点 点击事件
    function markerClick(e) {
        //  console.log(e)
        //获取点击地点的名字   匹配预存数组中的all_arr[i].name 改变值
        var name = e.target.B.content.replace(/<[^>]+>/g, "")
        for (i = 0; i < all_arr.length; i++) {
            if (all_arr[i].name == name) {
                $("#name").text(all_arr[i].name)
                $('#image').attr('src', all_arr[i].image)
                audio.src = all_arr[i].audios[0].audio
                //点击地图点改变 页面相关样式
                audio.pause();
                clearTimeout(xu_time)
                $("#aPlay").removeClass('aui-hide')
                $("#aPause").addClass('aui-hide')
                $("#play_jd").removeClass('progress-' + jindu)
                $("#play_jd").addClass('progress-0')
            }
        }

    }


    //点击定位
    function dw_map() {

        var geolocation;
        map.plugin('AMap.Geolocation', function () {
            geolocation = new AMap.Geolocation({
                enableHighAccuracy: true,//是否使用高精度定位，默认:true
                timeout: 1000000,          // 10000  超过10秒后停止定位，默认：无穷大
                buttonOffset: new AMap.Pixel(10, 100),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
                buttonPosition: 'RB'
            });
            map.addControl(geolocation);
            geolocation.getCurrentPosition(function (res, err) {
                console.log("获取地图位置:" + JSON.stringify(err))
            });
            AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息

            // console.log("获取地理位置-----------" + JSON.stringify(latitude))

            wx.checkJsApi({
                jsApiList: ['getLocation'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
                success: function (res) {
                    console.log("需要检测的JS接口列表，所有JS接口列表见附录2," + res)
                    // 以键值对的形式返回，可用的api值true，不可用为false
                    // 如：{"checkResult":{"chooseImage":true},"errMsg":"checkJsApi:ok"}
                }
            });

            wx.getLocation({
                type: 'wgs84',
                success: function (res) {
                    var latitude = res.latitude
                    var longitude = res.longitude
                    var speed = res.speed
                    var accuracy = res.accuracy

                    console.log("获取地理位置--------2---" + JSON.stringify(latitude))

                },
                fail: function (err) {
                    console.log("获取地理位置错误" + JSON.stringify(err))
                }
            })


            wx.getLocation({
                type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                success: function (res) {
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    var speed = res.speed; // 速度，以米/每秒计
                    var accuracy = res.accuracy; // 位置精度
                }
            });

            // wx.openLocation(function (res) {
            //     console.log("获取地理位置" + JSON.stringify(res))
            // })

            AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息
        });

    }


    //解析定位结果
    function onComplete(data) {
        var str = ['定位成功'];
        str.push('经度：' + data.position.getLng());
        str.push('纬度：' + data.position.getLat());
        if (data.accuracy) {
            str.push('精度：' + data.accuracy + ' 米');
        }//如为IP精确定位结果则没有精度信息
        str.push('是否经过偏移：' + (data.isConverted ? '是' : '否'));
        //回调事件
        // console.log(str)
        for (i = 0; i < all_arr.length; i++) {
            var lnglat = new AMap.LngLat(all_arr[i].longitude, all_arr[i].latitude);//测量点
            var myDistance = lnglat.distance([data.position.getLat(), data.position.getLat()]);//这里测量距离
            //如果2点间距离小于10 触发事件
            if (Math.ceil(myDistance) <= 10) {
                $("#name").text(all_arr[i].name)
                audio.src = all_arr[i].audios[0].audio
                $('#image').attr('src', all_arr[i].image)
                audio.play();
            }
        }
    }

    //解析定位错误信息
    function onError(data) {
        console.log('定位失败:' + JSON.stringify(data))
    }

    $(document).ready(function () {
        $("#but").click(function () {
            $.ajax({
                type: "get",
                url: "https://api.gowithtommy.com/rest/miniapp/scene/list/ ",
                data: {"city_id": "298"},
                dataType: "text",
                success: function (data) {
                    console.log(data)
                }
            });
        });

        $("#back").click(function () {
            console.log("back")
            // wx.miniProgram.reLaunch({
            wx.miniProgram.navigateBack({
                delta: '2',
                // url: '/pages/search/search?test=testtest',
                success: function () {
                    console.log('success')

                },
                fail: function () {
                    console.log('fail');
                },
                complete: function () {
                    console.log('complete');
                }
            });
        });


    });


    wx.getLocation({
        type: 'wgs84',
        success: function (res) {
            var latitude = res.latitude
            var longitude = res.longitude
            var speed = res.speed
            var accuracy = res.accuracy

            console.log("获取地理位置--------2---" + JSON.stringify(latitude))

        },
        fail: function (err) {
            console.log("获取地理位置错误" + JSON.stringify(err))
        }
    })


</script>


</body>

</html>