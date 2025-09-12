const UploadVideoType = props => {
  const { attributes, divClass, videoMobileImageUrl } = props
  const {
    video_url,
    video_behaviour,
    custom_play_button,
    custom_pause_button,
    fallbackVideoOptions,
    video_settings,
    video_poster,
    regularVideoType,
    fallBackVideoType,
  } = attributes

  const videoControls = video_settings.map(video_setting => video_setting.value)
  const poster = !videoControls.includes("autoplay") ? video_poster.url : ""

  let outputHtml = (
    <>
      <div class={`${divClass} video-wrapper`}>
        <video
          loop={videoControls.includes("loop")}
          controls={videoControls.includes("controls")}
          class={video_behaviour === "thumbnail" ? "mw-100" : "w-100"}
          preload={poster ? "none" : "metadata"}
          poster={poster}
          playsinline=""
        >
          {video_url && <source src={`${video_url}#t=0.01`} type={regularVideoType} />}
          {fallbackVideoOptions.videoUrl && (
            <source src={`${fallbackVideoOptions.videoUrl}#t=0.01`} type={fallBackVideoType} />
          )}
          Your browser does not support HTML5 video.
        </video>
        {custom_play_button.image.url && (
          <span class="play-button">
            <style>{`.play-button{width: ${custom_play_button.size.width}${custom_play_button.size.unit};
                  height: ${custom_play_button.size.height}${custom_play_button.size.unit};
                  background-image: url(${custom_play_button.image.url})}`}</style>
          </span>
        )}
        {custom_pause_button.image.url && (
          <span class="pause-button hide">
            <style>{`.pause-button{width: ${custom_pause_button.size.width}${custom_pause_button.size.unit};
                  height: ${custom_pause_button.size.height}${custom_pause_button.size.unit};
                  background-image: url(${custom_pause_button.image.url})}`}</style>
          </span>
        )}
      </div>
      {videoMobileImageUrl && (
        <div class="d-md-none d-lg-none">
          <img src={videoMobileImageUrl} />
        </div>
      )}
    </>
  )

  return <>{outputHtml}</>
}

export default UploadVideoType
