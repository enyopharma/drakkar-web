import React from 'react'

import { SearchType } from '../../src/types'

import { SearchOverlay } from './SearchOverlay'

type Props = {
    type: SearchType,
    enabled: boolean,
}

const classes: Record<SearchType, string> = {
    'method': 'progress-bar progress-bar-striped progress-bar-animated bg-info',
    'human': 'progress-bar progress-bar-striped progress-bar-animated bg-primary',
    'virus': 'progress-bar progress-bar-striped progress-bar-animated bg-danger',
}

export const SearchLoader: React.FC<Props> = ({ type, enabled }) => {
    return !enabled ? null : (
        <SearchOverlay>
            <ul className="list-group">
                <li className="list-group-item">
                    <div className="progress">
                        <div className={classes[type]} style={{ width: '100%' }}></div>
                    </div>
                </li>
            </ul>
        </SearchOverlay>
    )
}
