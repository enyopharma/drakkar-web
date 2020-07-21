import React from 'react'

import { Table } from './Table'

import { Description } from '../src/types'

export const App = (descriptions: Description[]) => {
    return <Table descriptions={descriptions} />
}
