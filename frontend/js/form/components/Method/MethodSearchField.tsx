import React from 'react'

import { methods as api } from '../../api'

import { SearchField } from '../Shared/SearchField'

type Props = {
    query: string,
    update: (query: string) => void,
    select: (psimi_id: string) => void,
}

export const MethodSearchField: React.FC<Props> = ({ ...props }) => {
    return (
        <SearchField {...props}
            type="method"
            search={api.search(5)}
            placeholder="Search a method..."
        />
    )
}
