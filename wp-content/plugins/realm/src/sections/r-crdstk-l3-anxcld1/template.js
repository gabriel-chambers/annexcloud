const content = {
	blockName: 'e25m-realm/r-crdstk-l3-anxcld1',
	attrs: {
		modulePrefix: 'r-crdstk-l3',
		realmClassNames: [
			{
				value: 'r-crdstk-l3--anxcld1',
				label: 'Anxcld1',
			},
		],
	},
	innerBlocks: [
		[
			'e25m/section',
			{
				sectionClassNames: [
					{
						value: 'bs-section---default',
						label: 'Default',
					},
				],
				realmDefaultClass: 'r-crdstk-l3',
			},
			[
				[
					'e25m/row',
					{
						rowClassNames: [
							{
								value: 'bs-row---default',
								label: 'Default',
							},
						],
					},
					[
						[
							'e25m/column',
							{
								sizeLg: 6,
								colClassList: [
									'bs-column',
									'col-sm-12',
									'col-lg-6',
								],
								columnClassNames: [
									{
										value: 'bs-column---default',
										label: 'Default',
									},
								],
							},
							[
								[
									'core/heading',
									{
										content: 'Why Join <br>Annex Cloud',
									},
									[],
								],
							],
						],
						[
							'e25m/column',
							{
								sizeLg: 6,
								colClassList: [
									'bs-column',
									'col-sm-12',
									'col-lg-6',
								],
								columnClassNames: [
									{
										value: 'bs-column---default',
										label: 'Default',
									},
								],
							},
							[
								[
									'core/paragraph',
									{
										content:
											'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco',
									},
									[],
								],
							],
						],
					],
				],
				[
					'e25m/row',
					{
						rowClassNames: [
							{
								value: 'bs-row---default',
								label: 'Default',
							},
							{
								value: 'bs-row--r-crdstk-l3-anxcld1-card-wrapper',
								label: 'R Crdstk L3 Anxcld1 Card Wrapper',
							},
						],
					},
					[
						[
							'e25m/column',
							{
								sizeLg: 4,
								colClassList: [
									'bs-column',
									'col-sm-12',
									'col-lg-4',
								],
								columnClassNames: [
									{
										value: 'bs-column---default',
										label: 'Default',
									},
								],
							},
							[
								[
									'e25m/card',
									{
										titleTag: 'h3',
										title: 'Our commitment to<br>collaboration',
										content:
											'We are dedicated to helping our global clients build lifelong relationships with<br>their customers.',
										imgURL: 'https://realm-uploads.s3.amazonaws.com/uploads/2023/05/Rectangle-2536.png',
										imgAlt: '',
										cardClassNames: [
											{
												value: 'bs-card---default',
												label: 'Default',
											},
											{
												value: 'bs-card--r-crdstk-l3-anxcld1',
												label: 'R Crdstk L3 Anxcld1',
											},
										],
									},
									[],
								],
							],
						],
						[
							'e25m/column',
							{
								sizeLg: 4,
								colClassList: [
									'bs-column',
									'col-sm-12',
									'col-lg-4',
								],
								columnClassNames: [
									{
										value: 'bs-column---default',
										label: 'Default',
									},
								],
							},
							[
								[
									'e25m/card',
									{
										titleTag: 'h3',
										title: 'Our commitment to<br>our people',
										content:
											'To ensure we all succeed professionally + personally which includes a balanced<br>work and personal life.',
										imgURL: 'https://realm-uploads.s3.amazonaws.com/uploads/2023/05/Rectangle-2536-1.png',
										imgAlt: '',
										cardClassNames: [
											{
												value: 'bs-card---default',
												label: 'Default',
											},
											{
												value: 'bs-card--r-crdstk-l3-anxcld1',
												label: 'R Crdstk L3 Anxcld1',
											},
										],
									},
									[],
								],
							],
						],
						[
							'e25m/column',
							{
								sizeLg: 4,
								colClassList: [
									'bs-column',
									'col-sm-12',
									'col-lg-4',
								],
								columnClassNames: [
									{
										value: 'bs-column---default',
										label: 'Default',
									},
								],
							},
							[
								[
									'e25m/card',
									{
										titleTag: 'h3',
										title: 'Our commitment<br>to DE&amp;I',
										content:
											'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus tristique mollis arcu lacinia vehicula. Ut mauris vitae lacinia.',
										imgURL: 'https://realm-uploads.s3.amazonaws.com/uploads/2023/05/Rectangle-2536-2.png',
										imgAlt: '',
										cardClassNames: [
											{
												value: 'bs-card---default',
												label: 'Default',
											},
											{
												value: 'bs-card--r-crdstk-l3-anxcld1',
												label: 'R Crdstk L3 Anxcld1',
											},
										],
									},
									[],
								],
							],
						],
					],
				],
			],
		],
	],
};
module.exports = {
	name: 'Card Stack Layout 3 ANXCLD 1',
	content,
	tags: ['Card'],
	images: [
		'https://realm-uploads.s3.amazonaws.com/uploads/2023/05/r-crdstk-l3-anxcld1.png',
		'https://realm-uploads.s3.amazonaws.com/uploads/2023/05/img-mobile.png',
	],
};
