/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n"
import Select from "react-select"
import ReactPlayer from "react-player"
import {
  PanelHeader,
  PanelBody,
  PanelRow,
  TextControl,
  SelectControl,
  ToggleControl,
  Button,
  RangeControl,
  __experimentalDivider as Divider,
} from "@wordpress/components"
import { InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } from "@wordpress/block-editor"
import { withSelect } from "@wordpress/data"
import { capitalize } from "lodash"

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
// import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @param {Object} [props]           Properties passed from the editor.
 * @param {string} [props.className] Class name generated for the block.
 *
 * @return {WPElement} Element to render.
 */
import ImageVariations from "./_editor_partials/image-variations"
import ImageType from "./_editor_partials/image"
import UploadVideoType from "./_editor_partials/video-resource"
import URLVideoType from "./_editor_partials/video-link"
import PreviewVideo from "./_editor_partials/preview-video"

const { useEffect, useState } = wp.element;

const Edit = props => {
  const { coreComponents = {}, coreComponentsTheme = {} } = props.blockClasses || {}

  const { attributes, setAttributes, videoMobileImageUrl } = props
  const {
    mediaVisibility,
    media_type_choice = "image",
    mask_image,
    image_alt,
    image_title,
    image_show_caption,
    image_caption,
    image_behaviour,
    choice_link,
    new_tab,
    image_link,
    video_file_name,
    video_source,
    video,
    video_mobile_img,
    video_settings,
    aspect_ratios,
    video_url,
    play_icon,
    video_behaviour,
    media_alignment,
    video_poster,
    custom_play_button,
    custom_pause_button,
    blockClassNames,
    vpopupHtml,
    vpopupFancyBoxOptions,
    enableFallbackVideo = false,
    fallbackVideoOptions,
    isVideoTransparent,
    isImgLazyLoad,
    vpopupRowHtml,
    previewVideoOptions,
    previewStartTime,
    previewEndTime
  } = props.attributes

  const blockProps = useBlockProps();

  // mask image upload
  const onMaskImageChange = img => {
    setAttributes({
      mask_image_url: img.url,
      mask_image: img.id,
    });
  };

  // video play image upload
  const onVideoPlayIconChange = img => {
    setAttributes({
      play_icon_url: img.url,
      play_icon: img.id,
    });
  };

  // remove image
  const removeMaskImage = () => {
    setAttributes({
      mask_image_url: "",
      mask_image: 0,
    });
  };

  // remove image
  const removeVideoPlayIcon = img => {
    setAttributes({
      play_icon_url: "",
      play_icon: 0,
    });
  };

  const handleVideoPosterChange = mediaObj => {
    setAttributes({
      video_poster:
        typeof mediaObj === "undefined"
          ? {
            id: 0,
            url: null,
          }
          : {
            id: mediaObj.id,
            url: mediaObj.url,
          },
    });
  };

  const handleCustomPlayButtonChange = mediaObj => {
    setAttributes({
      custom_play_button: {
        ...custom_play_button,
        image:
          typeof mediaObj === "undefined"
            ? {
              id: 0,
              url: null,
            }
            : {
              id: mediaObj.id,
              url: mediaObj.url,
            },
      },
    });
  };

  const handleCustomPlayButtonWidthChange = val => {
    setAttributes({
      custom_play_button: {
        ...custom_play_button,
        size: {
          ...custom_play_button.size,
          width: val,
        },
      },
    });
  };

  const handleCustomPlayButtonHeightChange = val => {
    setAttributes({
      custom_play_button: {
        ...custom_play_button,
        size: {
          ...custom_play_button.size,
          height: val,
        },
      },
    });
  };

  const handleCustomPlayButtonSizeUnitChange = val => {
    setAttributes({
      custom_play_button: {
        ...custom_play_button,
        size: {
          ...custom_play_button.size,
          unit: val,
        },
      },
    });
  };

  const handleCustomPauseButtonChange = mediaObj => {
    setAttributes({
      custom_pause_button: {
        ...custom_pause_button,
        image:
          typeof mediaObj === "undefined"
            ? {
              id: 0,
              url: null,
            }
            : {
              id: mediaObj.id,
              url: mediaObj.url,
            },
      },
    });
  };

  const handleCustomPauseButtonWidthChange = val => {
    setAttributes({
      custom_pause_button: {
        ...custom_pause_button,
        size: {
          ...custom_pause_button.size,
          width: val,
        },
      },
    });
  };

  const handleCustomPauseButtonHeightChange = val => {
    setAttributes({
      custom_pause_button: {
        ...custom_pause_button,
        size: {
          ...custom_pause_button.size,
          height: val,
        },
      },
    });
  };

  const handleCustomPauseButtonSizeUnitChange = val => {
    setAttributes({
      custom_pause_button: {
        ...custom_pause_button,
        size: {
          ...custom_pause_button.size,
          unit: val,
        },
      },
    });
  };

  const [mediaElementClasses, setMediaElementClasses] = useState([]);
  const [defaultClassValues, setDefaultClassValues] = useState([]);
  const [htmlValid, setHtmlValid] = useState(true);

  useEffect(() => {
    const classList = []
    if (coreComponents["bs-media-element"] && coreComponents["bs-media-element"].length > 0) {
      classList.push({
        value: `${coreComponents["bs-media-element"]}`,
        label: "Default",
      });
    }

    if (coreComponentsTheme["bs-media-element"] && coreComponentsTheme["bs-media-element"].length > 0) {
      coreComponentsTheme["bs-media-element"].map((className, index) => {
        let classLabel = className.replace("bs-media-element--", "").split("-")
        classList.push({
          value: className,
          label: classLabel.map(capitalize).join(" "),
        })
      })
    }
    const defaultValues = blockClassNames ? blockClassNames : [classList[0]]
    setMediaElementClasses(classList)
    setDefaultClassValues(defaultValues)
  }, [JSON.stringify(props.blockClasses)])

  let decodedVpopHtml = "";
  if (vpopupHtml.length > 0) {
    try {
      decodedVpopHtml = Buffer.from(vpopupHtml, "base64");
    } catch (error) {
      // ignore errors
    }
  }
  const classNames = blockClassNames ? blockClassNames.map(x => x.value).join(" ") : ""

  let div_class = ""
  if (video_mobile_img) {
    div_class = "d-none d-lg-block d-md-block "
  }

  if (video_behaviour === "thumbnail") {
    const align_class = media_alignment ? `${media_alignment} d-flex` : ""
    div_class += align_class
  }

  let editor_html = ""
  if (media_type_choice === "image") {
    editor_html = <ImageType attributes={attributes} />
  } else if (media_type_choice === "video" && video_source === "upload") {
    editor_html = (
      <UploadVideoType attributes={attributes} divClass={div_class} videoMobileImageUrl={videoMobileImageUrl} />
    )
  } else if (media_type_choice === "video" && video_source === "url") {
    editor_html = (
      <URLVideoType attributes={attributes} divClass={div_class} videoMobileImageUrl={videoMobileImageUrl} />
    )
  } else if (media_type_choice === "video" && video_source === "html") {
    editor_html = <ReactPlayer url={vpopupRowHtml} />
  } else if (media_type_choice === "video_popup") {
    editor_html = <ImageType attributes={attributes} />
  } else if (media_type_choice === "video_with_video_popup") {
    editor_html = (
      <PreviewVideo attributes={attributes} divClass={div_class}  />
    )
  }

  return (
    <div {...blockProps} key={blockProps.id}>
      <InspectorControls>
        <PanelBody title={__("Visibility", "")} initialOpen={false}>
          <ToggleControl
            label="Enable/Disable"
            checked={mediaVisibility}
            onChange={val => {
              setAttributes({ mediaVisibility: val });
            }}
          />
        </PanelBody>
        <PanelBody title={__("General Settings", "")}>
          <SelectControl
            label={__("Media Type", "")}
            options={[
              { value: "image", label: "Image" },
              { value: "video", label: "Video" },
              { value: "video_popup", label: "Image + Video Popup" },
              { value: "video_with_video_popup", label: "Video + Video Popup" },
            ]}
            value={media_type_choice}
            onChange={media_type_choice => setAttributes({ media_type_choice })}
          />
          {["image", "video_popup"].includes(media_type_choice) && (
            <>
              <ImageVariations setAttributes={setAttributes} attributes={attributes} />
              <PanelRow>Mask Image</PanelRow>
              <div className="components-base-control media-preview__row">
                <div className="editor-post-featured-image">
                  <MediaUploadCheck>
                    <MediaUpload
                      onSelect={onMaskImageChange}
                      allowedTypes={["image"]}
                      value={mask_image}
                      render={({ open }) => (
                        <>
                          {attributes.mask_image_url ? (
                            <>
                              <Button onClick={open} isPrimary>
                                {__("Replace image", "")}
                              </Button>
                              <Button onClick={removeMaskImage} isSecondary isDestructive>
                                {__("Remove image", "")}
                              </Button>
                            </>
                          ) : (
                            <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                              Choose the Mask Image
                            </Button>
                          )}
                        </>
                      )}
                    />
                  </MediaUploadCheck>
                </div>
                {attributes.mask_image_url ? (
                  <div className="media-preview__img">
                    <img src={attributes.mask_image_url} />
                  </div>
                ) : null}
              </div>
              <TextControl
                label={__("Image Alt Text", "")}
                type="text"
                value={image_alt}
                onChange={image_alt => setAttributes({ image_alt })}
              />
              <TextControl
                label={__("Image Title Text", "")}
                type="text"
                value={image_title}
                onChange={image_title => setAttributes({ image_title })}
              />
              <ToggleControl
                label={__("Show Caption")}
                checked={image_show_caption}
                onChange={image_show_caption => setAttributes({ image_show_caption })}
              />
              {image_show_caption && (
                <TextControl
                  label={__("Image Caption Text", "")}
                  type="text"
                  value={image_caption}
                  onChange={image_caption => setAttributes({ image_caption })}
                />
              )}
              {media_type_choice === "image" && (
                <>
                  <ToggleControl
                    label={__("Image Link")}
                    checked={choice_link}
                    onChange={choice_link => setAttributes({ choice_link })}
                  />
                  {choice_link && (
                    <>
                      <TextControl
                        label={__("Image Link", "")}
                        type="text"
                        value={image_link}
                        onChange={image_link => setAttributes({ image_link })}
                      />
                      <ToggleControl
                        label={__("Open In New Tab")}
                        checked={new_tab}
                        onChange={new_tab =>
                          setAttributes({
                            new_tab: new_tab,
                            open_new_tab: new_tab ? "_blank" : "_self",
                          })
                        }
                      />
                    </>
                  )}
                </>
              )}

              {
                <ToggleControl
                  label={__("Image lazy loading")}
                  checked={isImgLazyLoad}
                  onChange={isImgLazyLoad => setAttributes({ isImgLazyLoad })}
                />
              }
            </>
          )}
          {["video", "video_popup", "video_with_video_popup"].includes(media_type_choice) && (
            <>
              {/* Image + Video popup */}
              {media_type_choice === "video_popup" && (
                <div className="components-base-control media-preview__row">
                  <label className={"components-base-control__label"}>Play Icon</label>
                  <div className="editor-post-featured-image">
                    <MediaUploadCheck>
                      <MediaUpload
                        onSelect={onVideoPlayIconChange}
                        allowedTypes={["image"]}
                        value={play_icon}
                        render={({ open }) => (
                          <>
                            {attributes.play_icon_url ? (
                              <>
                                <Button onClick={open} isPrimary>
                                  {__("Replace Play Icon", "")}
                                </Button>
                                <Button onClick={removeVideoPlayIcon} isSecondary isDestructive>
                                  {__("Remove Play Icon", "")}
                                </Button>
                              </>
                            ) : (
                              <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                Choose the Play Icon
                              </Button>
                            )}
                          </>
                        )}
                      />
                    </MediaUploadCheck>
                  </div>
                  {attributes.play_icon_url ? (
                    <div className="media-preview__img">
                      <img src={attributes.play_icon_url} />
                    </div>
                  ) : null}
                </div>
              )}

              {/* Video + Video popup  - Preview video supports ONLY for uploaded video files */}
              {media_type_choice === 'video_with_video_popup' && (
                <>
                  <div className="components-base-control media-preview__row">
                    <Divider />
                    <label className={"components-base-control__label"}>{__("Preview Video", "")}</label>
                    <div className="editor-post-featured-image">
                      <MediaUploadCheck>
                        <MediaUpload
                          onSelect={media => {
                            setAttributes({
                              previewVideoOptions: {
                                ...previewVideoOptions,
                                videoId: media.id,
                                videoUrl: media.url,
                                videoFileName: media.filename,
                                videoType: media.mime
                              }
                            })
                          }}
                          allowedTypes={["video"]}
                          value={previewVideoOptions?.videoId || 0}
                          render={({ open }) => (
                            <>
                              {previewVideoOptions?.videoUrl ? (
                                <>
                                  <p>{previewVideoOptions.videoFileName}</p>
                                  <Button onClick={open} isPrimary>
                                    {__("Replace Video", "")}
                                  </Button>
                                  <Button
                                    onClick={() => {
                                      setAttributes({
                                        previewVideoOptions: {
                                          ...previewVideoOptions,
                                          videoId: 0,
                                          videoUrl: "",
                                          videoFileName: "",
                                          videoType: ""
                                        },
                                      });
                                    }}
                                    isSecondary
                                    isDestructive
                                  >
                                    {__("Remove Video", "")}
                                  </Button>
                                </>
                              ) : (
                                <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                  {__("Choose the Video", "")}
                                </Button>
                              )}
                            </>
                          )}
                        />
                      </MediaUploadCheck>
                    </div>
                  </div>
                  <RangeControl
                    label={__("Preview Start Time (ms)", "")}
                    min="0"
                    max="9999"
                    value={previewStartTime}
                    onChange={(previewStartTime) => {
                      setAttributes({ previewStartTime });
                    }}
                  />
                  <RangeControl
                    label={__("Preview End Time (ms)", "")}
                    min={previewStartTime}
                    max="9999"
                    value={previewEndTime}
                    onChange={(previewEndTime) => {
                      setAttributes({ previewEndTime });
                    }}
                  />
                    <div className="components-base-control media-preview__row">
                        <label className={"components-base-control__label"}>{__("Poster Image", "")}</label>
                        <div className="editor-post-featured-image">
                          <MediaUploadCheck>
                            <MediaUpload
                              onSelect={handleVideoPosterChange}
                              allowedTypes={["image"]}
                              value={video_poster.id}
                              render={({ open }) => (
                                <>
                                  {video_poster.url ? (
                                    <>
                                      <Button onClick={open} isPrimary>
                                        {__("Replace image", "")}
                                      </Button>
                                      <Button onClick={() => handleVideoPosterChange()} isSecondary isDestructive>
                                        {__("Remove image", "")}
                                      </Button>
                                    </>
                                  ) : (
                                    <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                      Choose the Poster
                                    </Button>
                                  )}
                                </>
                              )}
                            />
                          </MediaUploadCheck>
                        </div>
                        {video_poster.url ? (
                          <div className="media-preview__img">
                            <img src={video_poster.url} />
                          </div>
                        ) : null}
                      </div>
                </>
              )}

              {/* Popup settings for both 'Image + Video popup' and 'Video + Video popup' options */}
              <Divider />
              <SelectControl
                label={__("Video Source", "")}
                options={[
                  { value: "upload", label: "Upload (html5)" },
                  { value: "url", label: "URL (iframe)" },
                  { label: "Embed Code or HTML", value: "html" },
                ]}
                value={video_source}
                onChange={video_source => setAttributes({ video_source })}
              />
              {(video_source === "url" || video_source === "upload") && (
                <>
                  {video_source === "upload" && (
                    <>
                      <div className="components-base-control media-preview__row">
                        <label className={"components-base-control__label"}>Video</label>
                        <div className="editor-post-featured-image">
                          <MediaUploadCheck>
                            <MediaUpload
                              onSelect={media =>
                                setAttributes({
                                  video: media.id,
                                  video_url: media.url,
                                  video_file_name: media.filename,
                                  regularVideoType: media.mime,
                                })
                              }
                              allowedTypes={["video"]}
                              value={video}
                              render={({ open }) => (
                                <>
                                  {video_url ? (
                                    <>
                                      <p>{video_file_name}</p>
                                      <Button onClick={open} isPrimary>
                                        {__("Replace Video", "")}
                                      </Button>
                                      <Button
                                        onClick={media => {
                                          setAttributes({
                                            video: 0,
                                            video_url: "",
                                          });
                                        }}
                                        isSecondary
                                        isDestructive
                                      >
                                        {__("Remove Video", "")}
                                      </Button>
                                    </>
                                  ) : (
                                    <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                      Choose the Video
                                    </Button>
                                  )}
                                </>
                              )}
                            />
                          </MediaUploadCheck>
                        </div>
                      </div>
                      <ToggleControl
                        label={__("Enable Fallback Video", "")}
                        help={__(
                          "Add a fallback video to be displayed in case if the browser doesn't support the regular video (Video extension should be different from regular video)",
                          ""
                        )}
                        checked={enableFallbackVideo}
                        onChange={val => {
                          setAttributes({ enableFallbackVideo: val });
                        }}
                      />
                      {enableFallbackVideo && (
                        <div className="components-base-control media-preview__row">
                          <label className={"components-base-control__label"}>{__("Fallback Video", "")}</label>
                          <div className="editor-post-featured-image">
                            <MediaUploadCheck>
                              <MediaUpload
                                onSelect={media => {
                                  const videoMimeType = isVideoTransparent ? `${media.mime}; codecs="hvc1"` : media.mime
                                  setAttributes({
                                    fallbackVideoOptions: {
                                      ...fallbackVideoOptions,
                                      videoId: media.id,
                                      videoUrl: media.url,
                                      videoFileName: media.filename,
                                    },
                                    fallBackVideoType: videoMimeType,
                                  })
                                }}
                                allowedTypes={["video"]}
                                value={fallbackVideoOptions?.videoId || 0}
                                render={({ open }) => (
                                  <>
                                    {fallbackVideoOptions?.videoUrl ? (
                                      <>
                                        <p>{fallbackVideoOptions.videoFileName}</p>
                                        <Button onClick={open} isPrimary>
                                          {__("Replace Video", "")}
                                        </Button>
                                        <Button
                                          onClick={media => {
                                            setAttributes({
                                              fallbackVideoOptions: {
                                                ...fallbackVideoOptions,
                                                videoId: 0,
                                                videoUrl: "",
                                                videoFileName: "",
                                              },
                                            });
                                          }}
                                          isSecondary
                                          isDestructive
                                        >
                                          {__("Remove Video", "")}
                                        </Button>
                                      </>
                                    ) : (
                                      <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                        Choose the Video
                                      </Button>
                                    )}
                                  </>
                                )}
                              />
                            </MediaUploadCheck>
                          </div>
                          <ToggleControl
                            label={__("Transparent Video")}
                            checked={isVideoTransparent}
                            onChange={isVideoTransparent => setAttributes({ isVideoTransparent })}
                            help={__("Enable this if the fallback video is transparent", "")}
                          />
                        </div>
                      )}
                    </>
                  )}
                  {video_source === "url" && (
                    <>
                      {media_type_choice !== "video_popup" && (
                        <SelectControl
                          label={__("Aspect ratios", "")}
                          options={[
                            { value: "embed-responsive-21by9", label: "21:9" },
                            { value: "embed-responsive-16by9", label: "16:9" },
                            { value: "embed-responsive-4by3", label: "4:3" },
                            { value: "embed-responsive-1by1", label: "1:1" },
                          ]}
                          value={aspect_ratios}
                          onChange={aspect_ratios => setAttributes({ aspect_ratios })}
                        />
                      )}
                      <TextControl
                        label={__("Video URL", "")}
                        type="text"
                        value={video_url}
                        onChange={video_url => setAttributes({ video_url })}
                      />
                    </>
                  )}

                  {media_type_choice == "video" && (
                    <>
                      <div className="components-base-control media-preview__row">
                        <label className={"components-base-control__label"}>Mobile Image</label>
                        <div className="editor-post-featured-image">
                          <MediaUploadCheck>
                            <MediaUpload
                              onSelect={media =>
                                setAttributes({
                                  video_mobile_img: media.id,
                                })
                              }
                              allowedTypes={["image"]}
                              value={video_mobile_img}
                              render={({ open }) => (
                                <>
                                  {videoMobileImageUrl ? (
                                    <>
                                      <Button onClick={open} isPrimary>
                                        {__("Replace image", "")}
                                      </Button>
                                      <Button
                                        onClick={media => {
                                          setAttributes({
                                            video_mobile_img: 0,
                                          });
                                        }}
                                        isSecondary
                                        isDestructive
                                      >
                                        {__("Remove image", "")}
                                      </Button>
                                    </>
                                  ) : (
                                    <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                      Choose the Mobile Image
                                    </Button>
                                  )}
                                </>
                              )}
                            />
                          </MediaUploadCheck>
                        </div>
                        {videoMobileImageUrl ? (
                          <div className="media-preview__img">
                            <img src={videoMobileImageUrl} />
                          </div>
                        ) : null}
                      </div>
                      {video_source !== "url" && (
                        <>
                          <div className="components-base-control">
                            <label className="components-base-control__label">Video Settings</label>
                            <Select
                              isMulti
                              closeMenuOnSelect={false}
                              hideSelectedOptions={false}
                              options={[
                                { value: "autoplay", label: "Autoplay" },
                                { value: "muted", label: "Muted" },
                                { value: "loop", label: "Loop" },
                                { value: "controls", label: "Controls" },
                              ]}
                              value={video_settings}
                              onChange={settings => {
                                setAttributes({
                                  video_settings: settings || [],
                                });
                              }}
                            />
                          </div>
                        </>
                      )}
                      <div className="components-base-control media-preview__row">
                        <label className={"components-base-control__label"}>Poster Image</label>
                        <div className="editor-post-featured-image">
                          <MediaUploadCheck>
                            <MediaUpload
                              onSelect={handleVideoPosterChange}
                              allowedTypes={["image"]}
                              value={video_poster.id}
                              render={({ open }) => (
                                <>
                                  {video_poster.url ? (
                                    <>
                                      <Button onClick={open} isPrimary>
                                        {__("Replace image", "")}
                                      </Button>
                                      <Button onClick={() => handleVideoPosterChange()} isSecondary isDestructive>
                                        {__("Remove image", "")}
                                      </Button>
                                    </>
                                  ) : (
                                    <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                      Choose the Poster
                                    </Button>
                                  )}
                                </>
                              )}
                            />
                          </MediaUploadCheck>
                        </div>
                        {video_poster.url ? (
                          <div className="media-preview__img">
                            <img src={video_poster.url} />
                          </div>
                        ) : null}
                      </div>
                      <PanelHeader>Play Button Settings</PanelHeader>
                      <PanelRow>
                        <div className="components-base-control media-preview__row">
                          <label className={"components-base-control__label"}>Play Button Image</label>
                          <div className="editor-post-featured-image">
                            <MediaUploadCheck>
                              <MediaUpload
                                onSelect={handleCustomPlayButtonChange}
                                allowedTypes={["image"]}
                                value={custom_play_button.image.id}
                                render={({ open }) => (
                                  <>
                                    {custom_play_button.image.url ? (
                                      <>
                                        <Button onClick={open} isPrimary>
                                          {__("Replace image", "")}
                                        </Button>
                                        <Button
                                          onClick={() => handleCustomPlayButtonChange()}
                                          isSecondary
                                          isDestructive
                                        >
                                          {__("Remove image", "")}
                                        </Button>
                                      </>
                                    ) : (
                                      <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                        Choose an image
                                      </Button>
                                    )}
                                  </>
                                )}
                              />
                            </MediaUploadCheck>
                          </div>
                          {custom_play_button.image.url ? (
                            <div className="media-preview__img">
                              <img src={custom_play_button.image.url} />
                            </div>
                          ) : null}
                        </div>
                      </PanelRow>
                      {custom_play_button.image.id != 0 ? (
                        <PanelRow>
                          <div className="components-base-control">
                            <RangeControl
                              label="Play Button Width"
                              max="1000"
                              value={custom_play_button.size.width}
                              onChange={handleCustomPlayButtonWidthChange}
                            />
                            <RangeControl
                              label="Play Button Height"
                              max="1000"
                              value={custom_play_button.size.height}
                              onChange={handleCustomPlayButtonHeightChange}
                            />
                            <SelectControl
                              label="Play Button Size Unit"
                              options={[
                                { value: "%", label: "Percentage" },
                                { value: "px", label: "Pixels" },
                              ]}
                              value={custom_play_button.size.unit}
                              onChange={handleCustomPlayButtonSizeUnitChange}
                            />
                          </div>
                        </PanelRow>
                      ) : null}
                      <PanelHeader>Pause Button Settings</PanelHeader>
                      <PanelRow>
                        <div className="components-base-control media-preview__row">
                          <label className={"components-base-control__label"}>Pause Button Image</label>
                          <div className="editor-post-featured-image">
                            <MediaUploadCheck>
                              <MediaUpload
                                onSelect={handleCustomPauseButtonChange}
                                allowedTypes={["image"]}
                                value={custom_pause_button.image.id}
                                render={({ open }) => (
                                  <>
                                    {custom_pause_button.image.url ? (
                                      <>
                                        <Button onClick={open} isPrimary>
                                          {__("Replace image", "")}
                                        </Button>
                                        <Button
                                          onClick={() => handleCustomPauseButtonChange()}
                                          isSecondary
                                          isDestructive
                                        >
                                          {__("Remove image", "")}
                                        </Button>
                                      </>
                                    ) : (
                                      <Button className={"editor-post-featured-image__toggle"} onClick={open}>
                                        Choose an image
                                      </Button>
                                    )}
                                  </>
                                )}
                              />
                            </MediaUploadCheck>
                          </div>
                          {custom_pause_button.image.url ? (
                            <div className="media-preview__img">
                              <img src={custom_pause_button.image.url} />
                            </div>
                          ) : null}
                        </div>
                      </PanelRow>
                      {custom_pause_button.image.id != 0 ? (
                        <PanelRow>
                          <div className="components-base-control">
                            <RangeControl
                              label="Pause Button Width"
                              max="1000"
                              value={custom_pause_button.size.width}
                              onChange={handleCustomPauseButtonWidthChange}
                            />
                            <RangeControl
                              label="Pause Button Height"
                              max="1000"
                              value={custom_pause_button.size.height}
                              onChange={handleCustomPauseButtonHeightChange}
                            />
                            <SelectControl
                              label="Pause Button Size Unit"
                              options={[
                                { value: "%", label: "Percentage" },
                                { value: "px", label: "Pixels" },
                              ]}
                              value={custom_pause_button.size.unit}
                              onChange={handleCustomPauseButtonSizeUnitChange}
                            />
                          </div>
                        </PanelRow>
                      ) : null}
                    </>
                  )}
                </>
              )}

              {video_source === "html" && (
                <>
                  <React.Fragment>
                    <div className="bs-pro-button__videopopuphtml">
                      <p>Embed code or HTML</p>
                      <textarea
                        placeholder="Paste the Embed code or HTML here"
                        onChange={event => {
                          const htmlInput = event.currentTarget.value;
                          setHtmlValid(true);
                          if (/<([A-Za-z][A-Za-z0-9]*)\b[^>]*>(.*?)<\/\1>/.test(htmlInput)) {
                            const htmlCodeEncoded = Buffer.from(htmlInput, "utf-8").toString("base64")
                            setAttributes({ vpopupHtml: htmlCodeEncoded, vpopupRowHtml: htmlInput })
                          } else if (htmlInput.length > 0) {
                            setHtmlValid(false);
                          }
                        }}
                        rows="10"
                        defaultValue={decodedVpopHtml}
                      ></textarea>
                      {!htmlValid ? <span className="input-validation-error">Not a valid HTML content</span> : ""}
                    </div>
                    {media_type_choice === "video_popup" && (
                      <>
                        <div>
                          <TextControl
                            label="Popup class(es)"
                            placeholder="Class name(s)"
                            value={vpopupFancyBoxOptions?.classNames || ""}
                            onChange={vpopupClasses => {
                              setAttributes({
                                vpopupFancyBoxOptions: {
                                  ...vpopupFancyBoxOptions,
                                  classNames: vpopupClasses,
                                },
                              });
                            }}
                          />
                        </div>
                        <div>
                          <SelectControl
                            label="Popup outside click action"
                            value={vpopupFancyBoxOptions?.dismissOnClickOutside || "close"}
                            options={[
                              { label: "Dismiss", value: "close" },
                              { label: "Do nothing", value: false },
                            ]}
                            onChange={selectedVal => {
                              setAttributes({
                                vpopupFancyBoxOptions: {
                                  ...vpopupFancyBoxOptions,
                                  dismissOnClickOutside: selectedVal,
                                },
                              });
                            }}
                          />
                        </div>
                      </>
                    )}
                  </React.Fragment>
                </>
              )}
              {/* End of the Popup settings for both 'Image + Video popup' and 'Video + Video popup' options */}
            </>
          )}

          {["image", "video_popup"].includes(media_type_choice) ? (
            <SelectControl
              label={__("Image Type", "")}
              options={[
                { value: "img-fluid", label: "Fluid Image" },
                { value: "img-thumbnail", label: "Thumbnail" },
              ]}
              value={image_behaviour}
              onChange={image_behaviour => setAttributes({ image_behaviour })}
            />
          ) : (
            <SelectControl
              label={__("Video Type", "")}
              options={[
                { value: "full", label: "Fluid Video" },
                { value: "thumbnail", label: "Thumbnail" },
              ]}
              value={video_behaviour}
              onChange={video_behaviour => setAttributes({ video_behaviour })}
            />
          )}

          {((["image", "video_popup"].includes(media_type_choice) && image_behaviour === "img-thumbnail") ||
            (["video"].includes(media_type_choice) && video_behaviour === "thumbnail")) && (
              <SelectControl
                label={__("Media Alignment", "")}
                options={[
                  { value: "justify-content-start", label: "Left" },
                  { value: "justify-content-center", label: "Center" },
                  { value: "justify-content-end", label: "Right" },
                ]}
                value={media_alignment}
                onChange={media_alignment => setAttributes({ media_alignment })}
              />
            )}
        </PanelBody>
        <PanelBody title={__("Media Element Block Class", "")} initialOpen={false}>
          <Select
            defaultValue={defaultClassValues}
            isMulti
            name="mediaElementClasses"
            options={mediaElementClasses}
            className="basic-multi-select"
            classNamePrefix="select"
            onChange={val => {
              setAttributes({
                blockClassNames: val,
              });
            }}
          />
        </PanelBody>
      </InspectorControls>
      <>
        <div class={`media-elements ${classNames} ${mediaVisibility === true ? "enable" : "disable"}`}>
          {editor_html}
        </div>
      </>
    </div>
  );
};

export default withSelect((select, props) => {
  const { attributes } = props
  const { video_mobile_img } = attributes
  const { getMedia } = select("core")
  const mediaObj = getMedia(video_mobile_img)
  const theme = select("core").getCurrentTheme()
  const { block_classes: blockClasses = {} } = theme || {}
  return {
    videoMobileImageUrl: mediaObj ? mediaObj.source_url : null,
    blockClasses,
  }
})(Edit)
