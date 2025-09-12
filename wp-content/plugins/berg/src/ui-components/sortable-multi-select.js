import React from "react"
import Select from "react-draggable-multi-select"

//Sortable multi select input
const SortableSelect = props => {
	const { value, onChange, options } = props
	return <Select value={value} onChange={onChange} options={options} isMulti={true} closeMenuOnSelect={false} />
}

export default SortableSelect
