/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";
import { PanelBody, Button } from "@wordpress/components";
import { MediaUpload, MediaUploadCheck } from "@wordpress/block-editor";

//Media Image
const ImageVariations = (props) => {
  const { attributes, setAttributes } = props;
  const {
    image,
    image_desktop,
    image_mobile,
    image_url,
    image_url_desktop,
    image_url_mobile,
    image_2x_desktop,
    image_2x_mobile,
    image_2x_url_desktop,
    image_2x_url_mobile,
  } = attributes;

  // remove image
  const removeImage = () => {
    setAttributes({
      image_url: "",
      image: 0,
      image_alt: "",
      image_title: "",
      image_caption: "",
      mask_image_url: "",
      mask_image: 0,
      play_icon_url: "",
      play_icon: 0,
      video: 0,
      video_file_name: "",
    });
  };

  const desktopBreakpoint =
    typeof bergThemeData !== "undefined"
      ? bergThemeData.desktopBreakpoint
      : 1280;
  const mobileBreakpoint =
    typeof bergThemeData !== "undefined" ? bergThemeData.mobileBreakpoint : 576;

  return (
    <>
      {/* Regular image */}
      <PanelBody
        title={__(`Regular size (Greater than ${desktopBreakpoint}px)`, "")}
        initialOpen={true}
      >
        <div className="components-base-control media-preview__row">
          <div className="editor-post-featured-image">
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(img) =>
                  setAttributes({
                    image: img.id,
                    image_url: img.url,
                  })
                }
                allowedTypes={["image"]}
                value={image}
                render={({ open }) => (
                  <>
                    {image_url ? (
                      <>
                        <Button onClick={open} isPrimary>
                          {__("Replace image", "")}
                        </Button>
                        <Button onClick={removeImage} isSecondary isDestructive>
                          {__("Remove image", "")}
                        </Button>
                      </>
                    ) : (
                      <Button
                        className={"editor-post-featured-image__toggle"}
                        onClick={open}
                      >
                        {__("Choose the Image", "")}
                      </Button>
                    )}
                  </>
                )}
              />
            </MediaUploadCheck>
          </div>
          {image_url ? (
            <div className="media-preview__img">
              <img src={image_url} />
            </div>
          ) : null}
        </div>
      </PanelBody>
      {/* Desktop image */}
      <PanelBody
        title={__(
          `Desktop (Between ${mobileBreakpoint}px and ${desktopBreakpoint}px)`,
          ""
        )}
        initialOpen={false}
      >
        <div className="components-base-control media-preview__row">
          <div className="editor-post-featured-image">
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(img) =>
                  setAttributes({
                    image_desktop: img.id,
                    image_url_desktop: img.url,
                  })
                }
                allowedTypes={["image"]}
                value={image_desktop}
                render={({ open }) => (
                  <>
                    {image_url_desktop ? (
                      <>
                        <Button onClick={open} isPrimary>
                          {__("Replace image", "")}
                        </Button>
                        <Button
                          onClick={() =>
                            setAttributes({
                              image_desktop: 0,
                              image_url_desktop: "",
                            })
                          }
                          isSecondary
                          isDestructive
                        >
                          {__("Remove image", "")}
                        </Button>
                      </>
                    ) : (
                      <Button
                        className={"editor-post-featured-image__toggle"}
                        onClick={open}
                      >
                        Choose the Image
                      </Button>
                    )}
                  </>
                )}
              />
            </MediaUploadCheck>
          </div>
          {image_url_desktop ? (
            <div className="media-preview__img">
              <img src={image_url_desktop} />
            </div>
          ) : null}
        </div>
        {/* Desktop 2x image */}
        <div className="components-base-control media-preview__row">
          <div className="editor-post-featured-image">
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(img) =>
                  setAttributes({
                    image_2x_desktop: img.id,
                    image_2x_url_desktop: img.url,
                  })
                }
                allowedTypes={["image"]}
                value={image_2x_desktop}
                render={({ open }) => (
                  <>
                    {image_2x_url_desktop ? (
                      <>
                        <Button onClick={open} isPrimary>
                          {__("Replace image", "")}
                        </Button>
                        <Button
                          onClick={() =>
                            setAttributes({
                              image_2x_desktop: 0,
                              image_2x_url_desktop: "",
                            })
                          }
                          isSecondary
                          isDestructive
                        >
                          {__("Remove image", "")}
                        </Button>
                      </>
                    ) : (
                      <Button
                        className={"editor-post-featured-image__toggle"}
                        onClick={open}
                      >
                        {__("Choose the 2x Image", "")}
                      </Button>
                    )}
                  </>
                )}
              />
            </MediaUploadCheck>
          </div>
          {image_2x_url_desktop ? (
            <div className="media-preview__img">
              <img src={image_2x_url_desktop} />
            </div>
          ) : null}
        </div>
      </PanelBody>
      {/* Mobile image */}
      <PanelBody
        title={__(`Mobile (Less than ${mobileBreakpoint}px)`, "")}
        initialOpen={false}
      >
        <div className="components-base-control media-preview__row">
          <div className="editor-post-featured-image">
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(img) =>
                  setAttributes({
                    image_mobile: img.id,
                    image_url_mobile: img.url,
                  })
                }
                allowedTypes={["image"]}
                value={image_mobile}
                render={({ open }) => (
                  <>
                    {image_url_mobile ? (
                      <>
                        <Button onClick={open} isPrimary>
                          {__("Replace image", "")}
                        </Button>
                        <Button
                          onClick={() =>
                            setAttributes({
                              image_mobile: 0,
                              image_url_mobile: "",
                            })
                          }
                          isSecondary
                          isDestructive
                        >
                          {__("Remove image", "")}
                        </Button>
                      </>
                    ) : (
                      <Button
                        className={"editor-post-featured-image__toggle"}
                        onClick={open}
                      >
                        Choose the Image
                      </Button>
                    )}
                  </>
                )}
              />
            </MediaUploadCheck>
          </div>
          {image_url_mobile ? (
            <div className="media-preview__img">
              <img src={image_url_mobile} />
            </div>
          ) : null}
        </div>
        {/* Mobile 2x image */}
        <div className="components-base-control media-preview__row">
          <div className="editor-post-featured-image">
            <MediaUploadCheck>
              <MediaUpload
                onSelect={(img) =>
                  setAttributes({
                    image_2x_mobile: img.id,
                    image_2x_url_mobile: img.url,
                  })
                }
                allowedTypes={["image"]}
                value={image_2x_mobile}
                render={({ open }) => (
                  <>
                    {image_2x_url_mobile ? (
                      <>
                        <Button onClick={open} isPrimary>
                          {__("Replace image", "")}
                        </Button>
                        <Button
                          onClick={() =>
                            setAttributes({
                              image_2x_mobile: 0,
                              image_2x_url_mobile: "",
                            })
                          }
                          isSecondary
                          isDestructive
                        >
                          {__("Remove image", "")}
                        </Button>
                      </>
                    ) : (
                      <Button
                        className={"editor-post-featured-image__toggle"}
                        onClick={open}
                      >
                        {__("Choose the 2x Image", "")}
                      </Button>
                    )}
                  </>
                )}
              />
            </MediaUploadCheck>
          </div>
          {image_2x_url_mobile ? (
            <div className="media-preview__img">
              <img src={image_2x_url_mobile} />
            </div>
          ) : null}
        </div>
      </PanelBody>
    </>
  );
};

export default ImageVariations;
