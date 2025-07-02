import React from 'react'

const Dropzone = ({
    dimensions,
    fileInputRef,
    handleFileChange,
    StyledDropArea    
}) => {
  return (
      <div
          className="d-flex align-items-center justify-content-center"
          style={{
              width: dimensions.width,
              height: dimensions.height,
              border: "1px dashed #ccc",
              padding: "1rem",
          }}
      >
          <StyledDropArea
              className="mb-0 text-dark p-3 bold"
              width={dimensions.width}
              height={dimensions.height}
          >
              Drop file here or click to select file
          </StyledDropArea>
          <div>
              <input
                  ref={fileInputRef}
                  type="file"
                  className="d-none"
                  onChange={handleFileChange}
              />
              <button
                  className="btn btn-secondary"
                  onClick={() => fileInputRef.current?.click()}
              >
                  Select file
              </button>
          </div>
      </div>
  );
}

export default Dropzone