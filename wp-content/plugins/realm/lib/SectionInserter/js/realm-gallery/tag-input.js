import React, { useState, useEffect } from 'react';

export const TagInput = (props) => {
	const { searchQuery, onChange, label, tags: allTags, onTagChange } = props;
	const [selectedTags, setSelectedTags] = useState([]);
	const [tagsToShow, setTagsToShow] = useState(allTags);
	const [showDropdown, setShowDropdown] = useState(false);

	useEffect(() => {
		setTagsToShow(allTags.filter((tag) => !selectedTags.includes(tag)));
		onTagChange(selectedTags);
	}, [selectedTags]);

	const handleInput = (query) => {
		let filteredTags = allTags.filter((tag) => !selectedTags.includes(tag));
		if (query !== '') {
			filteredTags = filteredTags.filter((tag) =>
				tag.toLowerCase().includes(query.toLowerCase())
			);
		}
		onChange(query);
		setTagsToShow(filteredTags);
	};

	const handleTagClick = (tag) => {
		onChange('');
		setSelectedTags([...selectedTags, tag]);
	};

	const handleFocus = () => {
		setShowDropdown(true);
	};

	const handleBlur = () => {
		setTimeout(() => setShowDropdown(false), 150);
	};

	const handleTagClose = (clickedTag) => {
		setSelectedTags(selectedTags.filter((tag) => tag !== clickedTag));
	};

	return (
		<div className="tag-input">
			<label htmlFor="input_query">{label}</label>
			<div className="tag-input__field">
				{[...selectedTags].map((tag, i) => (
					<div key={i} className="tag-input__tag">
						{tag}{' '}
						<button
							onClick={() => handleTagClose(tag)}
							className="tag-input__tag-close-btn"
						></button>
					</div>
				))}
				<input
					id="input_query"
					type="text"
					placeholder="Search by name, tags"
					value={searchQuery}
					onChange={(e) => handleInput(e.target.value)}
					onFocus={handleFocus}
					onBlur={handleBlur}
				/>
			</div>
			{showDropdown && tagsToShow.length > 0 && (
				<div className="tag-input__list">
					<ul>
						{tagsToShow.map((tag, i) => (
							<li key={i} onClick={() => handleTagClick(tag)}>
								{tag}
							</li>
						))}
					</ul>
				</div>
			)}
		</div>
	);
};
