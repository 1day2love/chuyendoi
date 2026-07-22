<!DOCTYPE html>
<html>
<head>
    <title>1day2love</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="robots" content="noindex" />
    <style type="text/css">
        body, html {
            margin: 0;
            padding: 0
        }

        #previewPlayer {
            position: absolute;
            width: 100% !important;
            height: 100% !important;
            border: none;
            overflow: hidden;
        }

        #frameSlider {
            width: 100%
        }

        .jwplayer.jw-state-buffering .jw-display-icon-display .jw-icon {
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        .jw-progress {
            background-color: #ba400e !important;
        }
        .jw-rightclick {
            display: none !important
        }
        .jw-skin-vapor .jw-rail {
            background: rgba(2, 36, 134, 0.76) !important;
        }

        .jw-icon-rewind {
            display: none !important;
        }
        
        .jw-text-track-display span {
            font-weight: 700;
            font-family: Arial, sans-serif;
        }
        
        #first {
            display: none
        }
    </style>
    <script src="https://ssl.p.jwpcdn.com/player/v/8.26.0/jwplayer.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/hls.min.js"></script>
   
    <script>
        jwplayer.key = "ITWMv7t88JGzI0xPwW8I0+LveiXX9SWbfdmt0ArUSyc=";

        // Function to get URL parameter value by name
        function getUrlParameter(name) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(window.location.href);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        $(document).ready(function () {
            var videoUrl = getUrlParameter('url');
            var subtitleUrl = getUrlParameter('link_vtt') || getUrlParameter('link_srt') || getUrlParameter('sub');
            
            if (videoUrl) {
                var proxyUrl = 'get.php?url=' + encodeURIComponent(videoUrl);
                initPlayer(proxyUrl, subtitleUrl);
            } else {
                console.error("Không có URL video.");
            }
        });
        
        function initPlayer(videoUrl, subtitleUrl) {
            var videoPlayer = jwplayer("previewPlayer");
            
            // Cấu hình cơ bản
            var playerConfig = {
                sources: [{file: videoUrl, label: "1080p", type: "hls"}],
                logo: {
                    file: "",
                    logoBar: "",
                    position: "top-left",
                    link: "#"
                },
                abouttext: '1day2love',
                aboutlink: '',
                width: '100%',
                primary: 'html5',
                displaytitle: false,
                sharing: false,
                autostart: false,
                playbackRateControls: true,
                mute: false,
                
                //Phụ đề mặc định
                captions: {
                    color: "#FFFFFF", 
                    fontOpacity: 100,
                    edgeStyle: "outline",
                    backgroundOpacity: 0,
                    windowOpacity: 0
                },
                
                plugins: {
                    "/1day2love/js/screenshot.js": {
                        enabled: true,
                        name: "screenshot"
                    }
                }
            };
            
            
            // Thêm phụ đề nếu có
            if (subtitleUrl) {
                playerConfig.tracks = [{
                    file: subtitleUrl,
                    label: "Tiếng Việt",
                    kind: "captions",
                    "default": true
                }];
            }
            
            videoPlayer.setup(playerConfig);
            
            // Thêm nút tua
            videoPlayer.addButton("/1day2love/player/media/forward-10.png", "Đến 10 giây", function () {
                videoPlayer.seek(videoPlayer.getPosition() + 10);
            }, "Đến 10 giây");
            
            videoPlayer.addButton("/1day2love/player/media/back-10.png", "Lùi 10 giây", function () {
                videoPlayer.seek(videoPlayer.getPosition() - 10);
            }, "Lùi 10 giây");
        } 
    </script>
</head>
<body>
<div id="previewPlayer"></div>
</body>
</html>