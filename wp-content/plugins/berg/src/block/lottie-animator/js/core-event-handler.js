const lottie = require("lottie-web");

export class LottieAnimator {
  constructor(element) {
    this.element = element;
    const animationSettings = $(this.element).data("settings");
    const deviceTypeKey = this.getDeviceTypeKey();
    this.settings =
      animationSettings[deviceTypeKey] &&
      animationSettings[deviceTypeKey].settings &&
      animationSettings[deviceTypeKey].settings.path
        ? animationSettings[deviceTypeKey].settings
        : animationSettings["desktop"].settings;

    this.handleIntersectionObserver =
      this.handleIntersectionObserver.bind(this);
    this.animationPlayed = false;
  }

  getDeviceTypeKey() {
    const viewportWidth = $(window).width();
    return viewportWidth < 576 ? "mobile" : "desktop";
  }

  init() {
    this.loadAnimation();
  }

  loadAnimation() {
    const { loadAnimation } = lottie;
    const { path, loop } = this.settings;
    const animationData = {
      renderer: "svg",
      autoplay: false,
      path,
      loop,
      container: this.element,
    };
    this.lottieAnimation = loadAnimation(animationData);
  }

  handleIntersectionObserver(entries) {
    const {
      settings: { oneTimePlay },
      animationPlayed,
    } = this;
    entries.forEach((entry) => {
      if (entry.isIntersecting && !animationPlayed) {
        this.lottieAnimation.goToAndStop(0, true);
        this.animationEvent();
        if (oneTimePlay === true) {
          this.animationPlayed = true;
        }
      }
    });
  }

  animationEvent() {
    const {
      trigger,
      playSpeed,
      direction,
      delay,
      mouseOutAction,
      loop,
      numberOfLoops,
    } = this.settings;

    this.lottieAnimation.setSpeed(playSpeed);
    if (parseInt(direction) === -1 && mouseOutAction !== "reverse") {
      this.setAnimationDirection(parseInt(direction));
    }

    if (trigger) {
      this.triggerMethod();
    } else if (delay) {
      this.setAnimationDelay(delay);
    } else {
      this.lottieAnimation.play();
    }

    if (loop && numberOfLoops) {
      this.setNumberOfLoops(numberOfLoops);
    }
  }

  setAnimationDirection(direction) {
    const { totalFrames } = this.lottieAnimation;
    this.lottieAnimation.goToAndStop(direction === -1 ? totalFrames : 0, true);
    this.lottieAnimation.setDirection(direction);
  }

  setNumberOfLoops(numberOfLoops) {
    this.lottieAnimation.addEventListener("loopComplete", (eventParam) => {
      const { currentLoop } = eventParam;
      if (Math.abs(currentLoop) >= numberOfLoops) {
        this.lottieAnimation.stop();
      }
    });
  }

  setAnimationDelay(delay) {
    setTimeout(() => {
      this.lottieAnimation.play();
    }, delay);
  }

  triggerMethod() {
    const { triggerMethod, mouseOutAction, scrollRelativeTo } = this.settings;

    switch (triggerMethod) {
      case "pageHover":
        this.handleOnMouseEnter();
        if (mouseOutAction !== "none") {
          this.handleOnMouseOut(mouseOutAction);
        }
        break;
      case "pageClick":
        this.handleOnClick();
        break;
      case "pageScroll":
        if (scrollRelativeTo === "withinSection") {
          this.scrollWithinSection();
        } else {
          this.scrollOnEntirePage();
        }
        break;
    }
  }

  handleOnMouseEnter(start, end) {
    $(this.element).on("mouseenter", () => {
      if (parseInt(this.settings.direction) !== -1) {
        this.lottieAnimation.setDirection(1);
      }
      const { playDirection, totalFrames } = this.lottieAnimation;
      const startFrame = start ? start : playDirection === -1 ? totalFrames : 0;
      const endFrame = end ? end : playDirection === -1 ? 0 : totalFrames;
      this.lottieAnimation.playSegments([startFrame, endFrame], true);
    });
  }

  handleOnMouseOut(mouseOutAction) {
    $(this.element).on("mouseleave", () => {
      switch (mouseOutAction) {
        case "stop":
          this.lottieAnimation.stop();
          break;
        case "pause":
          this.lottieAnimation.pause();
          break;
        case "reverse":
          this.lottieAnimation.setDirection(-1);
          break;
      }
    });
  }

  handleOnClick(start, end) {
    $(this.element).on("click", () => {
      const { playDirection, totalFrames } = this.lottieAnimation;
      const startFrame = start ? start : playDirection === -1 ? totalFrames : 0;
      const endFrame = end ? end : playDirection === -1 ? 0 : totalFrames;
      this.lottieAnimation.playSegments([startFrame, endFrame], true);
    });
  }

  scrollWithinSection() {
    const firstSvgElement = $(this.element).find("svg g")[0];
    const elementScrollTop = $(firstSvgElement).offset().top;
    const skipHeight = elementScrollTop - $(this.element).offset().top;
    const { playDirection, totalFrames } = this.lottieAnimation;

    document.addEventListener("scroll", () => {
      const elementHeight = $(this.element).height();
      const scrollPosition = window.scrollY;

      if (
        elementScrollTop < window.innerHeight &&
        scrollPosition + window.innerHeight < elementHeight + elementScrollTop
      ) {
        let frame =
          (totalFrames /
            (elementScrollTop + elementHeight - window.innerHeight)) *
          scrollPosition;
        if (playDirection === -1) {
          frame = totalFrames - frame;
        }
        this.lottieAnimation.goToAndStop(frame, true);
      } else if (
        scrollPosition + window.innerHeight > elementScrollTop + skipHeight &&
        scrollPosition + window.innerHeight < elementScrollTop + elementHeight
      ) {
        let frame =
          (totalFrames / (elementHeight - skipHeight)) *
          (scrollPosition +
            window.innerHeight -
            (elementScrollTop + skipHeight));
        if (playDirection === -1) {
          frame = totalFrames - frame;
        }
        this.lottieAnimation.goToAndStop(frame, true);
      }
    });
  }

  scrollOnEntirePage() {
    const documentHeight = $(document).height();
    const { totalFrames, playDirection } = this.lottieAnimation;

    document.addEventListener("scroll", () => {
      const scrollPosition = window.scrollY;
      let frame = (totalFrames / documentHeight) * scrollPosition;
      if (playDirection === -1) {
        frame = totalFrames - frame;
      }
      this.lottieAnimation.goToAndStop(frame, true);
    });
  }
}

const domLoadedEventHandler = (lottieAnimator, animateViewPort, element) => {
  let observer = new IntersectionObserver(
    lottieAnimator.handleIntersectionObserver,
    {
      threshold: animateViewPort / 100,
    }
  );
  observer.observe(element);
};

export const lottieResizeCallBack = () => {
  $(".bs-lottie-animator:not(.bs-exclude-init)").each((index, element) => {
    //destroy previous lottieAnimation object
    element.lottieAnimation.destroy();
    
    const lottieAnimator = new LottieAnimator(element);
    lottieAnimator.init();

    const {
      settings: { animateViewPort },
      lottieAnimation,
    } = lottieAnimator;

    lottieAnimation.addEventListener("DOMLoaded", () => {
      domLoadedEventHandler(lottieAnimator, animateViewPort, element);
    });

    element.lottieAnimation = lottieAnimation;
  });
};

export default () => {
  $(".bs-lottie-animator:not(.bs-exclude-init)").each((index, element) => {
    const lottieAnimator = new LottieAnimator(element);
    lottieAnimator.init();

    const {
      settings: { animateViewPort },
      lottieAnimation,
    } = lottieAnimator;

    lottieAnimation.addEventListener("DOMLoaded", () => {
      domLoadedEventHandler(lottieAnimator, animateViewPort, element);
    });
    element.lottieAnimation = lottieAnimation;
  });
};
