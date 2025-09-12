const URLVideoType = props => {
  const { attributes, divClass, videoMobileImageUrl } = props
  const { video_settings, aspect_ratios, video_url, video_poster, custom_play_button, custom_pause_button } = attributes

  const videoControls = video_settings.map(video_setting => video_setting.value)
  const poster = !videoControls.includes("autoplay") ? video_poster.url : ""

  let outputHtml = (
    <>
      <div class={`video-wrapper video-wrapper--iframe ${divClass}`}>
        {poster && <img class="video-wrapper__poster-image" src={poster} />}
        <div class={`embed-responsive ${aspect_ratios}`}>
          <iframe class="embed-responsive-item" src={video_url}></iframe>
        </div>
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

export default URLVideoType
