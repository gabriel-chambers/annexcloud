const content = {
	blockName: 'e25m-realm/r-tabslr-l2-anxcld1',
	attrs: {
		modulePrefix: 'r-tabslr-l2',
		realmClassNames: [
			{
				value: 'r-tabslr-l2--anxcld1',
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
				realmDefaultClass: 'r-tabslr-l2',
				backgroundType: 'background_color',
				backgroundColorOptions: {
					regular: {
						settings: {
							backgroundColor: '#1c1f39',
						},
					},
				},
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
								colClassList: ['bs-column', 'col-sm-12'],
								columnClassNames: [
									{
										value: 'bs-column---default',
										label: 'Default',
									},
								],
							},
							[
								[
									'e25m/div',
									{
										content:
											'<div class="bs-div__inner     "></div>',
										divClassNames: [
											{
												value: 'bs-div---default',
												label: 'Default',
											},
											{
												value: 'bs-div--r-title-wrapper',
												label: 'R Title Wrapper',
											},
										],
									},
									[
										[
											'core/heading',
											{
												content:
													'Enterprise tested in across industries',
												textColor: 'pure-white',
											},
											[],
										],
									],
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
						],
					},
					[
						[
							'e25m/column',
							{
								colClassList: ['bs-column', 'col-sm-12'],
								columnClassNames: [
									{
										value: 'bs-column---default',
										label: 'Default',
									},
								],
							},
							[
								[
									'e25m/tab-slider-v2',
									{
										tabSliderClassNames: [
											{
												value: 'bs-tab-slider--r-tabslr-l2',
												label: 'R Tabslr L2',
											},
											{
												value: 'bs-tab-slider--r-tabslr-l2-tabs-bottom-mobile',
												label: 'R Tabslr L2 Tabs Bottom Mobile',
											},
											{
												value: 'bs-tab-slider--r-tabslr-l2-anxcld1',
												label: 'R Tabslr L2 Anxcld1',
											},
										],
										tabNavSliderClassNames: [
											{
												value: 'bs-slider-tabs bs-slider---default',
												label: 'Default',
											},
										],
										tabContentSliderClassNames: [
											{
												value: 'bs-slider-content bs-slider---default',
												label: 'Default',
											},
										],
										tabNavsSliderOptions: {
											desktop: {
												settings: {
													slidesToShow: 1,
													draggable: false,
													swipe: false,
													variableWidth: true,
												},
											},
											mobile: {
												settings: {
													slidesToShow: 2,
													variableWidth: true,
												},
											},
											tablet: {
												settings: {
													slidesToShow: 4,
													variableWidth: true,
												},
											},
											tabletLandscape: {
												settings: {
													slidesToShow: 4,
													draggable: false,
													swipe: false,
													variableWidth: true,
												},
											},
										},
										tabContentsSliderOptions: {
											desktop: {
												settings: {
													arrows: true,
												},
											},
											mobile: {
												settings: {
													slidesToShow: 1,
													arrows: true,
												},
											},
											tablet: {
												settings: {
													slidesToShow: 1,
													arrows: true,
												},
											},
											tabletLandscape: {
												settings: {
													slidesToShow: 1,
													arrows: true,
												},
											},
										},
									},
									[
										[
											'e25m/tab-slider-v2-tab',
											{
												tabName: 'CPG',
											},
											[
												[
													'e25m/tab-slider-v2-tab-nav',
													{},
													[
														[
															'core/paragraph',
															{
																content: 'CPG',
															},
															[],
														],
													],
												],
												[
													'e25m/tab-slider-v2-tab-content',
													{},
													[
														[
															'e25m/div',
															{
																content:
																	'<div class="bs-div__inner     ">\n\n</div>',
																divClassNames: [
																	{
																		value: 'bs-div---default',
																		label: 'Default',
																	},
																	{
																		value: 'bs-div--r-tabslr-l2-anxcld1-slide-wrapper',
																		label: 'R Tabslr L2 Anxcld1 Slide Wrapper',
																	},
																],
																lock: {
																	move: true,
																	remove: true,
																},
															},
															[
																[
																	'e25m/div',
																	{
																		content:
																			'<div class="bs-div__inner     "></div>',
																		divClassNames:
																			[
																				{
																					value: 'bs-div---default',
																					label: 'Default',
																				},
																				{
																					value: 'bs-div--r-slide-media-wrapper',
																					label: 'R Slide Media Wrapper',
																				},
																			],
																		lock: {
																			move: true,
																			remove: true,
																		},
																	},
																	[
																		[
																			'e25m/media-elements',
																			{
																				image: 2557,
																				image_url:
																					'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/anex-c-test-img-c.png',
																				blockClassNames:
																					[
																						{
																							value: 'bs-media-element---default',
																							label: 'Default',
																						},
																					],
																			},
																			[],
																		],
																	],
																],
																[
																	'e25m/div',
																	{
																		content:
																			'<div class="bs-div__inner     ">\n\n\n\n</div>',
																		divClassNames:
																			[
																				{
																					value: 'bs-div---default',
																					label: 'Default',
																				},
																				{
																					value: 'bs-div--r-slide-content-wrapper',
																					label: 'R Slide Content Wrapper',
																				},
																			],
																		backGroundColorOptions:
																			{
																				regular:
																					{
																						settings:
																							{
																								backgroundColor:
																									'#fff',
																							},
																					},
																			},
																		lock: {
																			move: true,
																			remove: true,
																		},
																	},
																	[
																		[
																			'e25m/media-elements',
																			{
																				image: 2611,
																				image_url:
																					'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/anex-c-test-logo.png',
																				blockClassNames:
																					[
																						{
																							value: 'bs-media-element---default',
																							label: 'Default',
																						},
																					],
																			},
																			[],
																		],
																		[
																			'core/quote',
																			{
																				citation:
																					'<strong>Shirley Loyalty</strong> Harrods UK',
																			},
																			[
																				[
																					'core/paragraph',
																					{
																						content:
																							'"Annex Cloud has helped our teams to quickly deploy new ways to engage shoppers across all our channels…"',
																					},
																					[],
																				],
																			],
																		],
																		[
																			'e25m/pro-button',
																			{
																				buttonTitle:
																					'Learn more about our retail experience',
																				buttonClassNames:
																					[
																						{
																							value: 'bs-pro-button---default',
																							label: 'Default',
																						},
																						{
																							value: 'bs-pro-button--primary-link',
																							label: 'Primary Link',
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
										],
										[
											'e25m/tab-slider-v2-tab',
											{
												tabName: 'Retail',
											},
											[
												[
													'e25m/tab-slider-v2-tab-nav',
													{},
													[
														[
															'core/paragraph',
															{
																content:
																	'Retail',
															},
															[],
														],
													],
												],
												[
													'e25m/tab-slider-v2-tab-content',
													{},
													[
														[
															'e25m/div',
															{
																content:
																	'<div class="bs-div__inner     ">\n\n</div>',
																divClassNames: [
																	{
																		value: 'bs-div---default',
																		label: 'Default',
																	},
																	{
																		value: 'bs-div--r-tabslr-l2-anxcld1-slide-wrapper',
																		label: 'R Tabslr L2 Anxcld1 Slide Wrapper',
																	},
																],
																lock: {
																	move: true,
																	remove: true,
																},
															},
															[
																[
																	'e25m/div',
																	{
																		content:
																			'<div class="bs-div__inner     "></div>',
																		divClassNames:
																			[
																				{
																					value: 'bs-div---default',
																					label: 'Default',
																				},
																				{
																					value: 'bs-div--r-slide-media-wrapper',
																					label: 'R Slide Media Wrapper',
																				},
																			],
																		lock: {
																			move: true,
																			remove: true,
																		},
																	},
																	[
																		[
																			'e25m/media-elements',
																			{
																				image: 2557,
																				image_url:
																					'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/anex-c-test-img-c.png',
																				blockClassNames:
																					[
																						{
																							value: 'bs-media-element---default',
																							label: 'Default',
																						},
																					],
																			},
																			[],
																		],
																	],
																],
																[
																	'e25m/div',
																	{
																		content:
																			'<div class="bs-div__inner     ">\n\n\n\n</div>',
																		divClassNames:
																			[
																				{
																					value: 'bs-div---default',
																					label: 'Default',
																				},
																				{
																					value: 'bs-div--r-slide-content-wrapper',
																					label: 'R Slide Content Wrapper',
																				},
																			],
																		backGroundColorOptions:
																			{
																				regular:
																					{
																						settings:
																							{
																								backgroundColor:
																									'#fff',
																							},
																					},
																			},
																		lock: {
																			move: true,
																			remove: true,
																		},
																	},
																	[
																		[
																			'e25m/media-elements',
																			{
																				image: 2611,
																				image_url:
																					'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/anex-c-test-logo.png',
																				blockClassNames:
																					[
																						{
																							value: 'bs-media-element---default',
																							label: 'Default',
																						},
																					],
																			},
																			[],
																		],
																		[
																			'core/quote',
																			{
																				citation:
																					'<strong>Shirley Loyalty</strong> Harrods UK',
																			},
																			[
																				[
																					'core/paragraph',
																					{
																						content:
																							'"Annex Cloud has helped our teams to quickly deploy new ways to engage shoppers across all our channels…"',
																					},
																					[],
																				],
																			],
																		],
																		[
																			'e25m/pro-button',
																			{
																				buttonTitle:
																					'Learn more about our retail experience',
																				buttonClassNames:
																					[
																						{
																							value: 'bs-pro-button---default',
																							label: 'Default',
																						},
																						{
																							value: 'bs-pro-button--primary-link',
																							label: 'Primary Link',
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
										],
										[
											'e25m/tab-slider-v2-tab',
											{
												tabName: 'Convenience/Grocery',
											},
											[
												[
													'e25m/tab-slider-v2-tab-nav',
													{},
													[
														[
															'core/paragraph',
															{
																content:
																	'Convenience/Grocery',
															},
															[],
														],
													],
												],
												[
													'e25m/tab-slider-v2-tab-content',
													{},
													[
														[
															'e25m/div',
															{
																content:
																	'<div class="bs-div__inner     ">\n\n</div>',
																divClassNames: [
																	{
																		value: 'bs-div---default',
																		label: 'Default',
																	},
																	{
																		value: 'bs-div--r-tabslr-l2-anxcld1-slide-wrapper',
																		label: 'R Tabslr L2 Anxcld1 Slide Wrapper',
																	},
																],
																lock: {
																	move: true,
																	remove: true,
																},
															},
															[
																[
																	'e25m/div',
																	{
																		content:
																			'<div class="bs-div__inner     "></div>',
																		divClassNames:
																			[
																				{
																					value: 'bs-div---default',
																					label: 'Default',
																				},
																				{
																					value: 'bs-div--r-slide-media-wrapper',
																					label: 'R Slide Media Wrapper',
																				},
																			],
																		lock: {
																			move: true,
																			remove: true,
																		},
																	},
																	[
																		[
																			'e25m/media-elements',
																			{
																				image: 2557,
																				image_url:
																					'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/anex-c-test-img-c.png',
																				blockClassNames:
																					[
																						{
																							value: 'bs-media-element---default',
																							label: 'Default',
																						},
																					],
																			},
																			[],
																		],
																	],
																],
																[
																	'e25m/div',
																	{
																		content:
																			'<div class="bs-div__inner     ">\n\n\n\n</div>',
																		divClassNames:
																			[
																				{
																					value: 'bs-div---default',
																					label: 'Default',
																				},
																				{
																					value: 'bs-div--r-slide-content-wrapper',
																					label: 'R Slide Content Wrapper',
																				},
																			],
																		backGroundColorOptions:
																			{
																				regular:
																					{
																						settings:
																							{
																								backgroundColor:
																									'#fff',
																							},
																					},
																			},
																		lock: {
																			move: true,
																			remove: true,
																		},
																	},
																	[
																		[
																			'e25m/media-elements',
																			{
																				image: 2611,
																				image_url:
																					'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/anex-c-test-logo.png',
																				blockClassNames:
																					[
																						{
																							value: 'bs-media-element---default',
																							label: 'Default',
																						},
																					],
																			},
																			[],
																		],
																		[
																			'core/quote',
																			{
																				citation:
																					'<strong>Shirley Loyalty</strong> Harrods UK',
																			},
																			[
																				[
																					'core/paragraph',
																					{
																						content:
																							'"Annex Cloud has helped our teams to quickly deploy new ways to engage shoppers across all our channels…"',
																					},
																					[],
																				],
																			],
																		],
																		[
																			'e25m/pro-button',
																			{
																				buttonTitle:
																					'Learn more about our retail experience',
																				buttonClassNames:
																					[
																						{
																							value: 'bs-pro-button---default',
																							label: 'Default',
																						},
																						{
																							value: 'bs-pro-button--primary-link',
																							label: 'Primary Link',
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
										],
										[
											'e25m/tab-slider-v2-tab',
											{
												tabName: 'Travel/Hospitality',
											},
											[
												[
													'e25m/tab-slider-v2-tab-nav',
													{},
													[
														[
															'core/paragraph',
															{
																content:
																	'Travel/Hospitality',
															},
															[],
														],
													],
												],
												[
													'e25m/tab-slider-v2-tab-content',
													{},
													[
														[
															'e25m/div',
															{
																content:
																	'<div class="bs-div__inner     ">\n\n</div>',
																divClassNames: [
																	{
																		value: 'bs-div---default',
																		label: 'Default',
																	},
																	{
																		value: 'bs-div--r-tabslr-l2-anxcld1-slide-wrapper',
																		label: 'R Tabslr L2 Anxcld1 Slide Wrapper',
																	},
																],
																lock: {
																	move: true,
																	remove: true,
																},
															},
															[
																[
																	'e25m/div',
																	{
																		content:
																			'<div class="bs-div__inner     "></div>',
																		divClassNames:
																			[
																				{
																					value: 'bs-div---default',
																					label: 'Default',
																				},
																				{
																					value: 'bs-div--r-slide-media-wrapper',
																					label: 'R Slide Media Wrapper',
																				},
																			],
																		lock: {
																			move: true,
																			remove: true,
																		},
																	},
																	[
																		[
																			'e25m/media-elements',
																			{
																				image: 2557,
																				image_url:
																					'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/anex-c-test-img-c.png',
																				blockClassNames:
																					[
																						{
																							value: 'bs-media-element---default',
																							label: 'Default',
																						},
																					],
																			},
																			[],
																		],
																	],
																],
																[
																	'e25m/div',
																	{
																		content:
																			'<div class="bs-div__inner     ">\n\n\n\n</div>',
																		divClassNames:
																			[
																				{
																					value: 'bs-div---default',
																					label: 'Default',
																				},
																				{
																					value: 'bs-div--r-slide-content-wrapper',
																					label: 'R Slide Content Wrapper',
																				},
																			],
																		backGroundColorOptions:
																			{
																				regular:
																					{
																						settings:
																							{
																								backgroundColor:
																									'#fff',
																							},
																					},
																			},
																		lock: {
																			move: true,
																			remove: true,
																		},
																	},
																	[
																		[
																			'e25m/media-elements',
																			{
																				image: 2611,
																				image_url:
																					'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/anex-c-test-logo.png',
																				blockClassNames:
																					[
																						{
																							value: 'bs-media-element---default',
																							label: 'Default',
																						},
																					],
																			},
																			[],
																		],
																		[
																			'core/quote',
																			{
																				citation:
																					'<strong>Shirley Loyalty</strong> Harrods UK',
																			},
																			[
																				[
																					'core/paragraph',
																					{
																						content:
																							'"Annex Cloud has helped our teams to quickly deploy new ways to engage shoppers across all our channels…"',
																					},
																					[],
																				],
																			],
																		],
																		[
																			'e25m/pro-button',
																			{
																				buttonTitle:
																					'Learn more about our retail experience',
																				buttonClassNames:
																					[
																						{
																							value: 'bs-pro-button---default',
																							label: 'Default',
																						},
																						{
																							value: 'bs-pro-button--primary-link',
																							label: 'Primary Link',
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
										],
									],
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
	name: 'Tab Slider Layout 2 ANXCLD 1',
	content,
	tags: ['Tab Slider', 'Pro Button'],
	images: [
		'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/r-tabslr-l2-anxcld1.png',
		'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/r-tabslr-l2-anxcld1-desktop.png',
		'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/r-tabslr-l2-anxcld1-mobile.png',
	],
};
