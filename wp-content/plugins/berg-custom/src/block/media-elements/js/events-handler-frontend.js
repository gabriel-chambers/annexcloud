(($) => {
  let controlButtons = document.querySelectorAll(
    "video+.play-button, video+.pause-button, video+.play-button+.pause-button"
  );
  Array.from(controlButtons).forEach((controlButton) => {
    let videoElement = controlButton.parentNode.querySelector("video");
    controlButton.addEventListener("click", (event) => {
      let button = event.currentTarget;
      let video = button.parentNode.querySelector("video");
      controlButton.classList.contains("play-button")
        ? video.play()
        : video.pause();
    });

    videoElement.addEventListener("play", (event) => {
      let playButton =
        event.currentTarget.parentNode.querySelector(".play-button");
      let pauseButton =
        event.currentTarget.parentNode.querySelector(".pause-button");
      if (playButton) {
        playButton.classList.add("hide");
      }
      if (pauseButton) {
        pauseButton.classList.remove("hide");
      }
    });

    videoElement.addEventListener("pause", (event) => {
      let playButton =
        event.currentTarget.parentNode.querySelector(".play-button");
      let pauseButton =
        event.currentTarget.parentNode.querySelector(".pause-button");
      if (playButton) {
        playButton.classList.remove("hide");
      }
      if (pauseButton) {
        pauseButton.classList.add("hide");
      }
    });
  });
  $.defaults.beforeShow = (instance, current) => {
    const encodedVpopContent = current.opts.encodedContent;
    if (typeof encodedVpopContent === "string") {
      let content = "<p>No content available</p>";
      if (encodedVpopContent.length > 0) {
        try {
          const decodedVpopContent = Buffer.from(encodedVpopContent, "base64");
          content = `<div>${decodedVpopContent}</div>`;
        } catch (err) {
          console.error(
            `Video pop-up content decoding failed with error: ${err}`
          );
        }
      }
      instance.setContent(current, content);
    }
  };
})(FancyappsUi);
