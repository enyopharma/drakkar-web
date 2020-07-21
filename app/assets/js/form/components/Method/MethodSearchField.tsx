import React from 'react'

import { SearchField } from '../Shared/SearchField'

import { methods as api } from '../../src/api'

type Props = {
    select: (psimi_id: string) => void,
}

export const MethodSearchField: React.FC<Props> = ({ ...props }) => {
    return (
        <SearchField {...props}
            type="method"
            search={(query: string) => api.search(query).read()}
            placeholder="Search a method..."
            help="You may use + to perform queries with multiple search terms (eg: bio + tag)"
        />
    )
}
