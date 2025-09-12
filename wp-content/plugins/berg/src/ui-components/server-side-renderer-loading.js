import { Spinner } from "@wordpress/components"
import ServerSideRendererError from "./server-side-renderer-error"

const ServerSideRendererLoading = ({ children, showLoader }) => {
  return (
    <div style={{ position: "relative" }}>
      {showLoader && (
        <div
          style={{
            position: "absolute",
            top: "50%",
            left: "50%",
            marginTop: "-9px",
            marginLeft: "-9px",
          }}
        >
          <Spinner />
        </div>
      )}
      <div style={{ opacity: showLoader ? "0.3" : 1 }}>
        {typeof children === "object" &&
        children.props &&
        typeof children.props.children === "object" &&
        children.props.children.error === true ? (
          <ServerSideRendererError />
        ) : (
          children
        )}
      </div>
    </div>
  )
}

export default ServerSideRendererLoading
