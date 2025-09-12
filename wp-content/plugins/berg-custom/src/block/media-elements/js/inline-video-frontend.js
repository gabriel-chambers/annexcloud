const $ = require("jquery");

$.fn.mediaVideo = function () {
  this.find("iframe").each((index, element) => {
    const $this = $(element);
    $this.attr({ id: `embed-${index}` });
    $this.attr({
      allow:
        "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture",
    });
  });

  // Iframe video play function
  const pauseVideo = (e) => {
    const $src = $(e).attr("src");
    if ($src.indexOf("vimeo") !== -1) {
      $(e)[0].contentWindow.postMessage(
        JSON.stringify({ method: "pause", value: true }),
        "*"
      );
    } else if ($src.indexOf("youtube") !== -1) {
      $(e)[0].contentWindow.postMessage(
        JSON.stringify({ event: "command", func: "pauseVideo" }),
        "*"
      );
    } else if ($src.indexOf("wistia") !== -1) {
      $(e)[0].contentWindow.postMessage(
        JSON.stringify({ method: "pause", value: true }),
        "*"
      );
    }
  };

  // Iframe video pause function
  const playVideo = (e) => {
    const $src = $(e).attr("src");
    if ($src.indexOf("mute") !== -1) {
      if ($src.indexOf("vimeo") !== -1) {
        $(e)[0].contentWindow.postMessage(
          JSON.stringify({ method: "play", value: true }),
          "*"
        );
      } else if ($src.indexOf("youtube") !== -1) {
        $(e)[0].contentWindow.postMessage(
          JSON.stringify({
            event: "command",
            func: "playVideo",
          }),
          "*"
        );
      } else if ($src.indexOf("wistia") !== -1) {
        $(e)[0].contentWindow.postMessage(
          JSON.stringify({ method: "play", value: true }),
          "*"
        );
      }
    } else {
      const $domainSrc = $src.indexOf("?") !== -1 ? $src.split("?")[0] : $src;
      const $updatedSrc = `${$domainSrc}?enablejsapi=1&mute=0&autoplay=1&modestbranding=1&rel=0`;
      setTimeout(function () {
        $(e).attr("src", $updatedSrc);
      }, 50);
    }
  };

  //Play button click event
  this.on("click", ".play-button", (e) => {
    const parentItem = $(e.target).closest("div");
    parentItem
      .find(".play-button, .video-wrapper__poster-image")
      .addClass("hide");
    parentItem.find(".pause-button").removeClass("hide");
    parentItem.each((index, element) => {
      const $element = $(element);
      $element.find("iframe").each((index, ele) => {
        const $ele = $(ele);
        playVideo($ele);
      });
    });
  });

  // Pause button click event
  this.on("click", ".pause-button", (e) => {
    const parentItem = $(e.target).closest("div");
    parentItem.find(".play-button").removeClass("hide");
    parentItem.find(".pause-button").addClass("hide");
    parentItem.each((index, element) => {
      const $element = $(element);
      $element.find("iframe").each((index, ele) => {
        const $ele = $(ele);
        pauseVideo($ele);
      });
    });
  });
};

$(".bs-media-element---default").mediaVideo();
