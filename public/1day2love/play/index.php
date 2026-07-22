<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <base href="/" />
    <title>1day2love</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex"/>
    <meta name="referrer" content="no-referrer">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="1day2love/play/css/netflix.css">
    <!-- <script src="https://ssl.p.jwpcdn.com/player/v/8.28.1/jwplayer.js"></script> -->
    <script src="https://ssl.p.jwpcdn.com/player/v/8.38.3/jwplayer.js"></script>
    <script src="1day2love/play/js/hls.min.js"></script>
    <script src="1day2love/play/js/jwplayer.hlsjs.min.js"></script>
  <!--   <script src="//cdn.jsdelivr.net/npm/devtools-detector"></script>
    <script type="text/javascript"> if(typeof devtoolsDetector === "undefined") { location.reload(); } else { devtoolsDetector.launch(); devtoolsDetector.addListener(function (isOpen) { if (isOpen) { location.reload(); } }); } </script>
    <script type="text/javascript" src="1day2love/play/js/loading.js"></script> -->
    <style type="text/css">html,body{width:100%;height:100%; padding:0; margin:0;}#player-embed,iframe{width:100%;height:100%;}</style>
    <script type="text/javascript">
        jwplayer.key = "ITWMv7t88JGzI0xPwW8I0+LveiXX9SWbfdmt0ArUSyc=";
    </script>
</head>
<body marginwidth="0" marginheight="0">

    <div id="player-fake"></div>
    <script>
        function initializePlayer() {
            var urlParams = new URLSearchParams(window.location.search);
            var videoUrl = urlParams.get('url');

            var playerInstance = jwplayer('player-fake');
            playerInstance.setup({
                "sources": [{
                       "file": videoUrl, // Thay đổi đường dẫn video theo tham số truy vấn
                        "type": "hls",
                }],
                width: '100%',
                height: '100%',
                primary: 'html5',
                controls: true,
                tracks: [{ "file": "", "kind": "captions", label: "US", default: "true" }],
                playbackRateControls: [0.5, 0.75, 1, 1.5, 2],
                logo: {
                    link: "#",
                    position: "top-left"
                },
                skin: {
                    name: "netflix"
                },
                plugins: {
                    "/1day2love/js/screenshot.js": {
                        enabled: true,
                        name: "screenshot"
                    }
                },
                sharing: true,
                displaytitle: true,
                displaydescription: true,
                abouttext: "Powered by 1day2love",
                aboutlink: "https://phimtvi.net",
                image: "",
                autostart: false,
                mute: "false",
                volume: "100",
                "intl": {
                    "en": {
                        "errors": {
                            "cantPlayVideo": "An error occurred while loading the video, please try reloading the page or choose another server to watch!"
                        }
                    }
                },
                advertising: {
                    "client": "vast",
                    "adscheduleid": "",
                    "skipoffset": 5,
                    "admessage": "Quảng cáo sẽ đóng sau xx giây.",
                    "skipmessage": "Bỏ qua quảng cáo trong xx giây.",
                    "skiptext": "Bỏ qua quảng cáo",
                    "schedule": [
                        {
                            "offset": "pre",
                            "tag": "",
                        }
                    ]
                }
        });
      

            playerInstance.on("ready", function () {
                const buttonId = "download-video-button";
                const iconPath =
                "https://phimtvi.net/1day2love/logoimg.png";
                const tooltipText = "1day2love";

                // Call the player's `addButton` API method to add the custom button
                //playerInstance.addButton(iconPath, tooltipText, buttonClickAction, buttonId);

                // This function is executed when the button is clicked
                function buttonClickAction() {
                    const playlistItem = playerInstance.getPlaylistItem();
                    const anchor = document.createElement("a");
                    const fileUrl = playlistItem.file;
                    anchor.setAttribute("hef", fileUrl); // Corrected attribute name from "href" to "hef"
                    const downloadName = playlistItem.file.split("/").pop();
                    anchor.setAttribute("download", downloadName);
                    anchor.style.display = "none";
                    document.body.appendChild(anchor);
                    anchor.click();
                    document.body.removeChild(anchor);
                }

                // Move the timeslider in-line with other controls
                const playerContainer = playerInstance.getContainer();
                const buttonContainer = playerContainer.querySelector(".jw-button-container");
                const spacer = buttonContainer.querySelector(".jw-spacer");
                const timeSlider = playerContainer.querySelector(".jw-slider-time");

                // Detect adblock
                playerInstance.on("adBlock", () => {
                    const modal = document.querySelector("div.modal");
                    modal.style.display = "flex";

                    document
                        .getElementById("close")
                        .addEventListener("click", () => location.reload());
                });

                // Forward 10 seconds
                const rewindContainer = playerContainer.querySelector(
                    ".jw-display-icon-rewind"
                );
                const forwardContainer = rewindContainer.cloneNode(true);
                const forwardDisplayButton = forwardContainer.querySelector(
                    ".jw-icon-rewind"
                );
                forwardDisplayButton.style.transform = "scaleX(-1)";
                forwardDisplayButton.ariaLabel = "Forward 10 Seconds";
                const nextContainer = playerContainer.querySelector(".jw-display-icon-next");
                nextContainer.parentNode.insertBefore(forwardContainer, nextContainer);

                // Control bar icon
                playerContainer.querySelector(".jw-display-icon-next").style.display = "none"; // hide next button
                const rewindControlBarButton = buttonContainer.querySelector(
                    ".jw-icon-rewind"
                );
                const forwardControlBarButton = rewindControlBarButton.cloneNode(true);
                forwardControlBarButton.style.transform = "scaleX(-1)";
                forwardControlBarButton.ariaLabel = "Forward 10 Seconds";
                rewindControlBarButton.parentNode.insertBefore(
                    forwardControlBarButton,
                    rewindControlBarButton.nextElementSibling
                );

                // Add onclick handlers
                [forwardDisplayButton, forwardControlBarButton].forEach((button) => {
                    button.onclick = () => {
                        playerInstance.seek(playerInstance.getPosition() + 10);
                    };
                });
                
                // Add Skip Button
               // playerInstance.addButton(
                 //   '<svg xmlns="http://www.w3.org/2000/svg" class="jw-svg-icon jw-svg-icon-rewind2" viewBox="0 0 240 240" focusable="false"><path d="m 25.993957,57.778 v 125.3 c 0.03604,2.63589 2.164107,4.76396 4.8,4.8 h 62.7 v -19.3 h -48.2 v -96.4 H 160.99396 v 19.3 c 0,5.3 3.6,7.2 8,4.3 l 41.8,-27.9 c 2.93574,-1.480087 4.13843,-5.04363 2.7,-8 -0.57502,-1.174985 -1.52502,-2.124979 -2.7,-2.7 l -41.8,-27.9 c -4.4,-2.9 -8,-1 -8,4.3 v 19.3 H 30.893957 c -2.689569,0.03972 -4.860275,2.210431 -4.9,4.9 z m 163.422413,73.04577 c -3.72072,-6.30626 -10.38421,-10.29683 -17.7,-10.6 -7.31579,0.30317 -13.97928,4.29374 -17.7,10.6 -8.60009,14.23525 -8.60009,32.06475 0,46.3 3.72072,6.30626 10.38421,10.29683 17.7,10.6 7.31579,-0.30317 13.97928,-4.29374 17.7,-10.6 8.60009,-14.23525 8.60009,-32.06475 0,-46.3 z m -17.7,47.2 c -7.8,0 -14.4,-11 -14.4,-24.1 0,-13.1 6.6,-24.1 14.4,-24.1 7.8,0 14.4,11 14.4,24.1 0,13.1 -6.5,24.1 -14.4,24.1 z m -47.77056,9.72863 v -51 l -4.8,4.8 -6.8,-6.8 13,-12.99999 c 3.02543,-3.03598 8.21053,-0.88605 8.2,3.4 v 62.69999 z"></path></svg>',
                 //   "Bỏ qua OP/ED",
                //    function () {
                 //       (skip_time = playerInstance.getPosition() + 90), playerInstance.seek(skip_time);
                 //   },
               //     "skipButton"
               // );
                
                // Get the list of tracks (subtitles) from the playlistItem
                const tracks = playlistItem.tracks;

                // Add options for the dropdown list
                const dropdown = document.createElement("select");
                dropdown.id = "subtitle-selector";
                dropdown.style.marginLeft = "10px";

                // Create a default option
                const defaultOption = document.createElement("option");
                defaultOption.text = "None";
                defaultOption.value = "";
                defaultOption.selected = true;
                dropdown.appendChild(defaultOption);

                // Create options for each track
                tracks.forEach((track, index) => {
                    const option = document.createElement("option");
                    option.text = track.label;
                    option.value = index.toString();
                    dropdown.appendChild(option);
                });

                // Add event for the dropdown list
                dropdown.addEventListener("change", function () {
                    const selectedIndex = parseInt(this.value);
                    playerInstance.setCurrentCaptions(selectedIndex);
                });

                // Insert dropdown into the container
                buttonContainer.insertBefore(dropdown, spacer.nextSibling);
            });
        }

        $(document).ready(function () {
            initializePlayer();
        });
    </script>
    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vedd3670a3b1c4e178fdfb0cc912d969e1713874337387" integrity="sha512-EzCudv2gYygrCcVhu65FkAxclf3mYM6BCwiGUm6BEuLzSb5ulVhgokzCZED7yMIkzYVg65mxfIBNdNra5ZFNyQ==" data-cf-beacon='{"rayId":"886e6d1c1e911086","r":1,"version":"2024.4.1","token":"5594f1c6aa13408cb46885fcd3c0e75c"}' crossorigin="anonymous"></script>
</body>

</html>
