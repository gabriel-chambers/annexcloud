const {
    i18n: { __ },
    components: { PanelBody, ToggleControl },
  } = wp;
  
  const Visibility = ({attributeName, attributes, setAttributes, initialOpen = true }) => {
    return (
      <PanelBody title={__('Visibility', 'b3rg')} initialOpen={initialOpen}>
        <ToggleControl
          label="Disable | Enable"
          checked={attributes[attributeName]}
          onChange={(value) => {
            setAttributes({ [attributeName]: value });
          }}
        />
      </PanelBody>
    );
  };
  
  export default Visibility;