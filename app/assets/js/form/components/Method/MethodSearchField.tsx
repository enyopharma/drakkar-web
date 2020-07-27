import React from 'react'
import { useAction } from '../../src/hooks'
import { methods as api } from '../../src/api'
import { selectMethod } from '../../src/reducer'

import { SearchField } from '../Shared/SearchField'

export const MethodSearchField: React.FC = () => {
    const select = useAction(selectMethod)

    return (
        <SearchField
            type="method"
            select={(psimi_id: string) => select({ psimi_id })}
            search={(query: string) => api.search(query).read()}
            placeholder="Search a method..."
            help="You may use + to perform queries with multiple search terms (eg: bio + tag)"
        />
    )
}
