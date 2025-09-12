const ImageType = props => {
  const { attributes } = props
  const {
    image_alt,
    image_title,
    image_show_caption,
    image_caption,
    image_behaviour,
    choice_link,
    image_link,
    media_alignment,
    image_url,
    image_url_desktop,
    image_url_mobile,
    image_2x_url_desktop,
    image_2x_url_mobile,
    mask_image_url,
    open_new_tab,
    media_type_choice,
  } = attributes

  const desktopBreakpoint = typeof bergThemeData !== "undefined" ? bergThemeData.desktopBreakpoint : 1280
  const mobileBreakpoint = typeof bergThemeData !== "undefined" ? bergThemeData.mobileBreakpoint : 576

  const commonImageClass =
    media_type_choice === "video_popup" ? "bs-common-image common-video-popup" : "bs-common-image"

  let outputHtml = (
    <div class={commonImageClass}>
      <figure class={`figure ${media_alignment} d-flex`}>
        <picture>
          {image_url && image_url_mobile && (
            <source
              srcset={image_2x_url_mobile ? `${image_url_mobile}, ${image_2x_url_mobile} 2x` : `${image_url_mobile}`}
              media={`(max-width:${mobileBreakpoint - 1}px)`}
            ></source>
          )}

          {image_url && image_url_desktop && (
            <source
              srcset={
                image_2x_url_desktop ? `${image_url_desktop}, ${image_2x_url_desktop} 2x` : `${image_url_desktop}`
              }
              media={`(max-width:${desktopBreakpoint}px)`}
            ></source>
          )}
          {image_url ? (
            <img src={image_url} class={image_behaviour} alt={image_alt} title={image_title} />
          ) : (
            <img src="https://via.placeholder.com/300.png" class={image_behaviour} alt="placeholder" title="" />
          )}
        </picture>
        {image_show_caption && image_caption && <figcaption class="figure-caption">{image_caption}</figcaption>}
      </figure>
    </div>
  )

  if (mask_image_url) {
    outputHtml = (
      <div class="bs-common-mask">
        <div class="bs-common-mask__wrap">
          {outputHtml}
          <div class="bs-common-mask__layer">
            <style>{`.bs-common-mask__layer{background-image: url(${mask_image_url})}`}</style>
          </div>
        </div>
      </div>
    )
  }

  if (choice_link && media_type_choice === "image") {
    outputHtml = (
      <a class="" href={image_link} target={open_new_tab} role="link" aria-label="image link" title="Image Link">
        <div class="mask_image_link">{outputHtml}</div>
      </a>
    )
  }
  return <>{outputHtml}</>
}

export default ImageType
