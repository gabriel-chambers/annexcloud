// Import realm section JS files
try {
  require
    .context( './realm-custom', true, /^.*\.js$/ )
    .keys()
    .forEach( key => {
      require( `./realm-custom/${key.substring( 2 )}` );
    } );
} catch ( _error ) {
  // ignore folder does not exists errors
}
// Add custom Javascript functions here
