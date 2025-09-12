( $ => {
  /* eslint-disable */
  const wrapOne = $(
    '.bs-posts__featured-grid .bs-post__featured_event_location_logo, .bs-posts__featured-grid .bs-post-taxonomy_event-location'
  );
  const wrapTwo = $(
    '.bs-posts__featured-grid .bs-post__featured_event_date_logo, .bs-posts__featured-grid .bs-post-event_date'
  );
  /* eslint-enable */
  wrapOne.wrapAll( '<div class="bs-post__location-wrapper"></div>' );
  wrapTwo.wrapAll( '<div class="bs-post__date-wrapper"></div>' );
} )( jQuery );
