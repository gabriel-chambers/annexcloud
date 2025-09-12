import React, { useState, useEffect } from 'react';

export const PreviewSlider = (props) => {
	const { section, onClose } = props;
	const [sliderImages, setSliderImages] = useState([]);
	const [activeSliderIndex, setActiveSliderIndex] = useState(0);

	useEffect(() => {
		const images = section?.images || [];
		if (!images.length && section.image) {
			images.push(section.image);
		}
		setSliderImages(images);
	}, [section]);

	const handleNav = (dir) => {
		const mod = dir === 'next' ? 1 : -1;
		const nextIndex = activeSliderIndex + mod;
		const lastIndex = sliderImages.length - 1;
		setActiveSliderIndex(
			nextIndex > lastIndex ? 0 : nextIndex < 0 ? lastIndex : nextIndex
		);
	};

	return (
		<>
			<div className="section-preview" onClick={onClose}>
				<h3 className="section-preview__title">{section.name}</h3>
				<div
					className="section-preview__slider"
					onClick={(e) => e.stopPropagation()}
				>
					<span className="section-preview__slider-counter">
						{activeSliderIndex + 1} / {sliderImages.length}
					</span>
					<div
						className="section-preview__slider-track"
						style={{
							transform: `translate3d(${
								-activeSliderIndex * 100
							}%, 0, 0)`,
						}}
					>
						{sliderImages.map((image, i) => (
							<div
								key={i}
								className={`section-preview__slider-wrapper ${
									activeSliderIndex === i ? 'active' : ''
								}`}
							>
								<img src={image} alt={section.name} />
							</div>
						))}
					</div>

					{sliderImages.length > 1 && (
						<>
							<button
								onClick={() => handleNav('prev')}
								className="section-preview__slider-nav section-preview__slider-nav--prev"
							></button>
							<button
								onClick={() => handleNav('next')}
								className="section-preview__slider-nav section-preview__slider-nav--next"
							></button>
						</>
					)}
				</div>
				<button
					className="section-preview__close-btn"
					onClick={onClose}
				>
					X
				</button>
			</div>
		</>
	);
};
