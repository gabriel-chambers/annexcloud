import React from 'react';
import Section from './section';
import { TagInput } from './tag-input';
import { PreviewSlider } from './preview-slider';
import sectionPreviews from 'Realm/sections/sections-previews';
import {
	SelectControl,
	__experimentalGrid as Grid,
	__experimentalText as Text,
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';

export default function SectionsGrid({ closeModal, holderBlock }) {
	const validSectionPreviews = sectionPreviews.filter(
		(preview) =>
			typeof preview === 'object' &&
			typeof preview.name === 'string' &&
			(typeof preview.content === 'object' ||
				typeof preview.content === 'string') &&
			(typeof preview.image === 'string' ||
				Array.isArray(preview?.images))
	);
	const blockGroupOptions = [];
	const projectsOptions = [];
	const tagList = new Set();
	validSectionPreviews.forEach(({ name, tags }) => {
		const indexOfLayout = name.indexOf('Layout');
		const blockName =
			indexOfLayout != -1 ? name.substr(0, indexOfLayout - 1) : null;
		if (
			blockName !== null &&
			blockGroupOptions.find(({ value }) => value == blockName) ===
				undefined
		) {
			blockGroupOptions.push({ value: blockName, label: blockName });
		}

		const matches = name.match(
			/(.*Layout\s\d{1,}\s)[A-Za-z\d\s]{1,}(?=\s\d{1,})/
		);
		const projectName =
			Array.isArray(matches) && matches.length > 0
				? matches[0]
						.replace(/.*Layout\s\d{1,}\s(FW\s)?/, '')
						.toUpperCase()
				: undefined;

		if (
			projectName &&
			projectsOptions.find(({ value }) => value == projectName) ===
				undefined
		) {
			projectsOptions.push({
				value: projectName,
				label: projectName,
			});
		}

		// collect tags list
		(tags || []).forEach(tagList.add, tagList);
	});
	const [filteredSections, setFilteredSections] =
		useState(validSectionPreviews);
	const [searchQuery, setSearchQuery] = useState('');
	const [blockGroup, setBlockGroup] = useState('-1');
	const [project, setProject] = useState('-1');
	const [selectedTags, setSelectedTags] = useState([]);
	const [previewData, setPreviewData] = useState({});

	useEffect(() => {
		filterSections(searchQuery, blockGroup, project);
	}, [selectedTags, searchQuery, blockGroup, project]);

	const filterSections = (q, selectedBlock, selectedProject) => {
		let filteredSections = validSectionPreviews.filter((section) => {
			const [blockName, projectName] = section.name.split('Layout');
			return (
				section.name.toLowerCase().startsWith(q.toLowerCase()) &&
				(selectedBlock === '-1' ||
					(selectedBlock !== '-1' &&
						blockName
							.toLowerCase()
							.includes(selectedBlock.toLowerCase()))) &&
				(selectedProject === '-1' ||
					(selectedProject !== '-1' &&
						projectName
							.toLowerCase()
							.includes(selectedProject.toLowerCase())))
			);
		});

		if (selectedTags.length > 0) {
			filteredSections = filteredSections.filter((section) => {
				let matchedTagCount = 0;
				selectedTags.map((tag) => {
					if (section?.tags?.includes(tag)) {
						matchedTagCount++;
						return true;
					}
				});
				return selectedTags.length === matchedTagCount;
			});
		}

		setFilteredSections(filteredSections);
	};

	const handlePreview = (section) => {
		setPreviewData({
			...previewData,
			show: true,
			section,
		});
	};

	return (
		<div className="realm-insert-library">
			<Grid columns={3}>
				<TagInput
					tags={[...tagList]}
					label="Search"
					searchQuery={searchQuery}
					onChange={(val) => {
						setSearchQuery(val);
					}}
					onTagChange={setSelectedTags}
				/>
				<SelectControl
					value={blockGroup}
					options={[{ value: '-1', label: 'All' }].concat(
						blockGroupOptions
					)}
					onChange={(selectedGroup) => {
						setBlockGroup(selectedGroup);
					}}
					label="Section Type"
					labelPosition="top"
				/>
				<SelectControl
					value={project}
					options={[{ value: '-1', label: 'All' }].concat(
						projectsOptions.sort((a, b) =>
							a.value.toLowerCase() < b.value.toLowerCase()
								? -1
								: 1
						)
					)}
					onChange={(selectedProject) => {
						setProject(selectedProject);
					}}
					label="Suffix"
					labelPosition="top"
				/>
			</Grid>
			{filteredSections.length === 0 ? (
				<Grid columns={1}>
					<Text
						className="update-nag notice notice-warning inline"
						align={'center'}
					>
						No sections found.
					</Text>
				</Grid>
			) : (
				<div className="section-grid">
					{filteredSections.map((section, index) => (
						<Section
							key={index}
							handleCloseModal={closeModal}
							section={section}
							holderBlock={holderBlock}
							onPreview={handlePreview}
						/>
					))}
				</div>
			)}
			{previewData?.show && (
				<PreviewSlider
					section={previewData?.section}
					onClose={() => setPreviewData({})}
				/>
			)}
		</div>
	);
}
