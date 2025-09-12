const content = {
	blockName: 'e25m-realm/r-spstbk-l1-anxcld1',
	attrs: {
		modulePrefix: 'r-spstbk-l1',
		realmClassNames: [
			{
				value: 'r-spstbk-l1--anxcld1',
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
				backgroundType: 'background_color',
				backgroundColorOptions: {
					regular: {
						settings: {
							backgroundColor: '#ffffff',
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
								sizeMd: 12,
								sizeLg: 8,
								sizeXl: 8,
								colClassList: [
									'bs-column',
									'col-sm-12',
									'col-md-12',
									'col-lg-8',
									'col-xl-8',
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
										content:
											'Resource to help you build a life time of loyality',
										textColor: 'dark',
									},
									[],
								],
							],
						],
						[
							'e25m/column',
							{
								sizeMd: 12,
								sizeLg: 4,
								sizeXl: 4,
								colClassList: [
									'bs-column',
									'col-sm-12',
									'col-md-12',
									null,
									'col-xl-4',
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
									'e25m/pro-button',
									{
										buttonTitle: 'See all resources',
										optionSelect: 'post',
										buttonClassNames: [
											{
												value: 'bs-pro-button--primary',
												label: 'Primary',
											},
										],
										selectedPostType: 'page',
										selectedPost: {
											value: '',
											label: '',
										},
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
									'e25m/single-post',
									{
										selectedPostType: 'resource',
										dateFormat: 'M j, Y',
										displayOrder: [
											{
												label: 'Image',
												value: 'image',
											},
											{
												value: 'post_type',
												label: 'Post Type Name',
											},
											{
												value: 'taxonomy_resource-category',
												label: 'Resource Category',
											},
											{
												value: 'title',
												label: 'Title',
											},
											{
												value: 'more',
												label: 'Read more',
											},
										],
										popupDisplayOrder: [
											{
												value: 'content',
												label: 'Content',
											},
										],
										titleTag: 'h3',
										singlePostClassNames: [
											{
												value: '',
												label: '',
											},
											{
												value: 'bs-single-post--featured-r-spstbk-l1-anxcld1',
												label: 'Featured R Spstbk L1 Anxcld1',
											},
											{
												value: 'bs-single-post--r-spstbk-l1-anxcld1',
												label: 'R Spstbk L1 Anxcld1',
											},
										],
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
						],
					},
					[
						[
							'e25m/column',
							{
								sizeMd: 4,
								sizeLg: 4,
								colClassList: [
									'bs-column',
									null,
									'col-sm-12',
									'col-lg-4',
									'col-md-4',
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
									'e25m/single-post',
									{
										selectedPostType: 'resource',
										dateFormat: 'M j, Y',
										displayOrder: [
											{
												value: 'post_type',
												label: 'Post Type Name',
											},
											{
												value: 'taxonomy_resource-category',
												label: 'Resource Category',
											},
											{
												value: 'title',
												label: 'Title',
											},
											{
												value: 'more',
												label: 'Read more',
											},
										],
										popupDisplayOrder: [
											{
												value: 'content',
												label: 'Content',
											},
										],
										titleTag: 'h3',
										singlePostClassNames: [
											{
												value: '',
												label: '',
											},
											{
												value: 'bs-single-post--r-spstbk-l1-anxcld1',
												label: 'R Spstbk L1 Anxcld1',
											},
											{
												value: 'bs-single-post--no-image-r-spstbk-l1-anxcld1',
												label: 'No Image R Spstbk L1 Anxcld1',
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
								sizeMd: 4,
								sizeLg: 4,
								colClassList: [
									'bs-column',
									null,
									'col-sm-12',
									'col-lg-4',
									'col-md-4',
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
									'e25m/single-post',
									{
										selectedPostType: 'resource',
										dateFormat: 'M j, Y',
										displayOrder: [
											{
												value: 'image',
												label: 'Image',
											},
											{
												value: 'post_type',
												label: 'Post Type Name',
											},
											{
												value: 'taxonomy_resource-category',
												label: 'Resource Category',
											},
											{
												value: 'title',
												label: 'Title',
											},
											{
												value: 'more',
												label: 'Read more',
											},
										],
										popupDisplayOrder: [
											{
												value: 'content',
												label: 'Content',
											},
										],
										titleTag: 'h3',
										singlePostClassNames: [
											{
												value: '',
												label: '',
											},
											{
												value: 'bs-single-post--r-spstbk-l1-anxcld1',
												label: 'R Spstbk L1 Anxcld1',
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
								sizeMd: 4,
								sizeLg: 4,
								colClassList: [
									'bs-column',
									null,
									'col-sm-12',
									'col-lg-4',
									'col-md-4',
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
									'e25m/single-post',
									{
										selectedPostType: 'resource',
										dateFormat: 'M j, Y',
										displayOrder: [
											{
												value: 'post_type',
												label: 'Post Type Name',
											},
											{
												value: 'taxonomy_resource-category',
												label: 'Resource Category',
											},
											{
												value: 'title',
												label: 'Title',
											},
											{
												value: 'more',
												label: 'Read more',
											},
										],
										popupDisplayOrder: [
											{
												value: 'content',
												label: 'Content',
											},
										],
										titleTag: 'h3',
										singlePostClassNames: [
											{
												value: '',
												label: '',
											},
											{
												value: 'bs-single-post--no-image-r-spstbk-l1-anxcld1',
												label: 'No Image R Spstbk L1 Anxcld1',
											},
											{
												value: 'bs-single-post--r-spstbk-l1-anxcld1',
												label: 'R Spstbk L1 Anxcld1',
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
	name: 'Single Post Block Layout 1 Anxcld 1',
	content,
	images: [
		'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/r-sgtpstblk-l1-Desktop-image.png',
		'https://realm-uploads.s3.amazonaws.com/uploads/2023/04/r-sgtpstblk-l1-Mobile-Image.png',
	],
	tags: ['Single Post'],
};
