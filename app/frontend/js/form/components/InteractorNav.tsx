import React from 'react'

import { DescriptionType, InteractorI } from '../src/types'

type Props = {
    type: DescriptionType,
    current: InteractorI,
    update: (i: InteractorI) => void,
}

const titles: Record<DescriptionType, Record<InteractorI, string>> = {
    'hh': {
        1: 'Human protein 1',
        2: 'Human protein 2',
    },
    'vh': {
        1: 'Human protein',
        2: 'Viral protein',
    },
}

const classes: Record<DescriptionType, Record<InteractorI, string>> = {
    'hh': {
        1: 'nav-link text-primary',
        2: 'nav-link text-primary',
    },
    'vh': {
        1: 'nav-link text-primary',
        2: 'nav-link text-danger',
    },
}

export const InteractorNav: React.FC<Props> = ({ type, current, update }) => {
    const getClasses = (i: InteractorI) => {
        return classes[type][i] + (current == i ? ' active' : '')
    }

    const getUpdateTab = (i: InteractorI) => (e: any) => {
        update(i)
        e.preventDefault()
    }

    return (
        <ul className="nav nav-tabs nav-justified card-header-tabs">
            <li className="nav-item">
                <a className={getClasses(1)} onClick={getUpdateTab(1)} href="#">
                    {titles[type][1]}
                </a>
            </li>
            <li className="nav-item">
                <a className={getClasses(2)} onClick={getUpdateTab(2)} href="#">
                    {titles[type][2]}
                </a>
            </li>
        </ul>
    )
}
