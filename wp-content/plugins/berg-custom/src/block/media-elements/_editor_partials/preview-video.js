const PreviewVideo = props => {
    const { attributes, divClass } = props
    const {
        video_behaviour,
        video_poster,
        previewVideoOptions
    } = attributes

    const posterUrl = video_poster.url ?? null
    const autoplayAndLoop = posterUrl ? true : false
    const videotypeClass = video_behaviour === "thumbnail" ? "mw-100" : "w-100"
    const posterClass = posterUrl ? 'has-poster' : null

    let outputHtml = (
        <>
            <div class={`${divClass} ${posterClass} video-wrapper`}>
                {previewVideoOptions.videoUrl && (
                    <video key={posterUrl}
                        autoplay={autoplayAndLoop}
                        loop={autoplayAndLoop}
                        class={`${videotypeClass} ${posterClass}`}
                        preload={posterUrl ? "none" : "metadata"}
                        poster={posterUrl}
                        playsinline={true}
                    >
                        {previewVideoOptions.videoUrl && <source src={`${previewVideoOptions.videoUrl}#t=0.01`} type={previewVideoOptions.videoType} />}
                        Your browser does not support HTML5 video.
                    </video>
                )}
            </div>
        </>
    )

    return <>{outputHtml}</>
}

export default PreviewVideo