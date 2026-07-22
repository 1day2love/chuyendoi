"use strict";
(()=>{
    var c = n=>{
        (window.jwplayerPluginJsonp || window.jwplayer && window.jwplayer().registerPlugin || function() {}
        )(n.pluginName, n.playerMinimumVersion, n)
    }
      , s = class {
        constructor(t, e, o) {
            this.playerInstance = t,
            this.pluginConfig = e,
            this.pluginDiv = o
        }
    }
    ;
    var g = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" class="jw-svg-icon"><path d="M 19.402344 6 C 17.019531 6 14.96875 7.679688 14.5 10.011719 L 14.097656 12 L 9 12 C 6.238281 12 4 14.238281 4 17 L 4 38 C 4 40.761719 6.238281 43 9 43 L 41 43 C 43.761719 43 46 40.761719 46 38 L 46 17 C 46 14.238281 43.761719 12 41 12 L 35.902344 12 L 35.5 10.011719 C 35.03125 7.679688 32.980469 6 30.597656 6 Z M 25 17 C 30.519531 17 35 21.480469 35 27 C 35 32.519531 30.519531 37 25 37 C 19.480469 37 15 32.519531 15 27 C 15 21.480469 19.480469 17 25 17 Z M 25 19 C 20.589844 19 17 22.589844 17 27 C 17 31.410156 20.589844 35 25 35 C 29.410156 35 33 31.410156 33 27 C 33 22.589844 29.410156 19 25 19 Z "></path></svg>';
    function p(n) {
        return new Promise((t,e)=>{
            var o;
            try {
                let r = document.createElement("canvas");
                r.width = n.videoWidth,
                r.height = n.videoHeight,
                (o = r == null ? void 0 : r.getContext("2d")) == null || o.drawImage(n, 0, 0),
                r.toBlob(l=>{
                    l && t(URL.createObjectURL(l))
                }
                )
            } catch (r) {
                e(r)
            }
        }
        )
    }
    function d(n, t) {
        let e = document.createElement("a");
        e.style.display = "none",
        e.href = n,
        e.download = t,
        document.body.appendChild(e),
        e.click(),
        document.body.removeChild(e)
    }
    var i = class extends s {
        constructor(t, e, o) {
            super(t, e, o);
            let {enabled: r=!0, name: l="screenshot"} = e;
            r !== !1 && t.on("ready", ()=>{
                let a = document.querySelector(".jw-video");
                //a == null || a.setAttribute("crossOrigin", "anonymous");
                let u = async()=>{
                    if (a) {
                        let y = await p(a);
                        d(y, l)
                    }
                }
                ;
                t.addButton(g, "Chụp hình hiện tại trên phim", u, "jw-plugin-screenshot")
            }
            )
        }
    }
    ;
    i.pluginName = "screenshot",
    i.playerMinimumVersion = "8.0.0";
    c(i);
}
)();
