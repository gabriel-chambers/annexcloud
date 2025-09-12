/* eslint-env jquery, browser */
import { CountUp } from 'countup.js';

$( () => {
  const options = {
    root: null,
    rootMargin: '0px',
    threshold: 0,
  };

  const io = new IntersectionObserver( entries => {
    entries.forEach( entry => {
      if ( entry.isIntersecting ) {
        const item = entry.target.querySelector( '[data-counterup]' );
        const dataOptions = $( item ).attr( 'data-options' );
        const counterOptions = JSON.parse( dataOptions );
        const countUp = new CountUp( item, counterOptions.endVal, counterOptions );
        if ( !countUp.error ) {
          countUp.start();
        }
      }
    } );
  }, options );
  const sections = document.querySelectorAll( '.bs-counter' );
  sections.forEach( el => {
    io.observe( el );
  } );
} );
