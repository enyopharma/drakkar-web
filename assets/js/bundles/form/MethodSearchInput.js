import React from 'react'

import SearchInput from './SearchInput'
import SelectedValue from './SelectedValue'

const format = method => [
    method.psimi_id,
    method.name,
].join(' - ');

const MethodSearchInput = ({ method, search, select, unselect }) => (method == null ?
    <SearchInput search={search} select={select} format={format}>
        Search a method...
    </SearchInput>
    :
    <SelectedValue color="info" unselect={unselect}>
        <strong>{method.psimi_id}</strong> - {method.name}
    </SelectedValue>
)

export default MethodSearchInput
