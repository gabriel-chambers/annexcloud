/* global FancyappsUi */
import { Fancybox } from '@fancyapps/ui/dist/fancybox/fancybox.esm.js';
window.FancyappsUi = Fancybox;

FancyappsUi.bind( '[data-fancybox]', {
  preload: 0,
  Html: {
    html5video: {
      tpl: fancybox => {
        let sourceTags = '';
        const videoData = fancybox.options.target.dataset.video_data;
        const videoDataJson = videoData ? JSON.parse( videoData ) : '';

        if ( videoDataJson ) {
          for ( const ext in videoDataJson ) {
            const type = videoDataJson[ext].isVideoTransparent
              ? `${videoDataJson[ext].format}; codecs=&quot;hvc1&quot;`
              : videoDataJson[ext].format;
            const videoSrc = `${videoDataJson[ext].src}#t=0.01`;
            sourceTags += `<source src="${videoSrc}" type="${type}" />`;
          }
        }
        const tpl = videoData
          ? `<video class="fancybox-video" controls controlsList="nodownload">
				${sourceTags}
				'Sorry, your browser doesn't support embedded videos,
        <a href="{{src}}">download</a>
        and watch with your favorite video player!'
				</video>`
          : `<video class="fancybox-video" controls controlsList="nodownload">
				<source src="{{src}}" type="{{format}}" />
				Sorry, your browser doesn't support embedded videos,
        <a href="{{src}}">download</a>
        and watch with your favorite video player!
				</video>`;
        return tpl;
      },
    },
  },
  on: {
    ready: fancybox => {
      $( fancybox.container ).addClass( fancybox.options.trigger.dataset.mainClass || '' );
    },
  },
} );
